<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Purchase::with(['supplier', 'creator'])
            ->latest();

        if ($request->search) {
            $query->where('reference_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('supplier', function($q) use ($request) {
                      $q->where('name', 'like', '%' . $request->search . '%');
                  });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $purchases = $query->paginate(10);
        return view('purchases.index', compact('purchases'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();
        
        // Generate Auto PO Number
        $today = date('Ymd');
        $lastPo = Purchase::whereDate('created_at', today())->count();
        $poNumber = 'PO-' . $today . '-' . str_pad($lastPo + 1, 3, '0', STR_PAD_LEFT);

        return view('purchases.create', compact('suppliers', 'products', 'poNumber'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'reference_number' => 'required|unique:purchases',
            'purchase_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.cost' => 'required|numeric|min:0',
            'receipt_image' => 'nullable|image|max:2048',
        ]);

        try {
            DB::transaction(function () use ($request) {
                // Handle Image Upload
                $imagePath = null;
                if ($request->hasFile('receipt_image')) {
                    $imagePath = $request->file('receipt_image')->store('receipts', 'public');
                }

                // Create Purchase Header
                $purchase = Purchase::create([
                    'supplier_id' => $request->supplier_id,
                    'reference_number' => $request->reference_number,
                    'purchase_date' => $request->purchase_date,
                    'status' => 'pending',
                    'note' => $request->note,
                    'receipt_image' => $imagePath,
                    'created_by' => auth()->id(),
                ]);

                $totalAmount = 0;

                // Create Purchase Items
                foreach ($request->items as $item) {
                    $subtotal = $item['quantity'] * $item['cost'];
                    $totalAmount += $subtotal;

                    PurchaseItem::create([
                        'purchase_id' => $purchase->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'cost' => $item['cost'],
                        'subtotal' => $subtotal,
                    ]);
                }

                $purchase->update(['total_amount' => $totalAmount]);
            });

            return redirect()->route('purchases.index')->with('success', __('messages.purchases.success_create'));
        } catch (\Exception $e) {
            return back()->with('error', __('messages.purchases.error_save', ['error' => $e->getMessage()]));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'items.product', 'creator']);
        return view('purchases.show', compact('purchase'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Purchase $purchase)
    {
        // Only Admin can edit
        if (!auth()->user()->hasAdminAccess()) {
            abort(403);
        }

        $purchase->load(['items']);
        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();
        
        return view('purchases.edit', compact('purchase', 'suppliers', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Purchase $purchase)
    {
        if (!auth()->user()->hasAdminAccess()) {
            abort(403);
        }

        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.cost' => 'required|numeric|min:0',
            'receipt_image' => 'nullable|image|max:2048',
        ]);

        try {
            DB::transaction(function () use ($request, $purchase) {
                // 1. REVERT PHASE (if completed)
                $wasCompleted = $purchase->status === 'completed';
                
                if ($wasCompleted) {
                    // Revert Stock and Delete Journal
                    foreach ($purchase->items as $item) {
                        $product = $item->product;
                        if ($product) {
                            $conversionFactor = $product->conversion_factor ?? 1;
                            $stockToRemove = $item->quantity * $conversionFactor;
                            $product->decrement('stock', $stockToRemove);
                            // Costs are harder to revert precisely without full history, 
                            // so we accept WAC might slight shift. 
                            // Or restore `previous_cost` if straightforward? 
                            // Too risky if multiple txns happened. Keep as is.
                        }
                    }

                    // Delete Journal
                    $journal = \App\Models\JournalEntry::where('reference_type', Purchase::class)
                        ->where('reference_id', $purchase->id)
                        ->first();
                    
                    if ($journal) {
                        $journal->lines()->delete();
                        $journal->delete();
                    }
                }

                // Handle Image Upload
                $imagePath = $purchase->receipt_image;
                if ($request->hasFile('receipt_image')) {
                    // Delete old image if exists
                    if ($imagePath && \Storage::disk('public')->exists($imagePath)) {
                        \Storage::disk('public')->delete($imagePath);
                    }
                    $imagePath = $request->file('receipt_image')->store('receipts', 'public');
                }

                // 2. UPDATE PHASE
                $purchase->update([
                    'supplier_id' => $request->supplier_id,
                    'purchase_date' => $request->purchase_date,
                    'note' => $request->note,
                    'receipt_image' => $imagePath,
                    // If it was completed, we keep it as completed after update (will re-process)
                    // If it was pending, it stays pending unless logic changes. 
                    // Let's assume edit doesn't change status unless we explicitly want to.
                ]);

                // Sync Items
                $purchase->items()->delete();
                $totalAmount = 0;

                foreach ($request->items as $item) {
                    $subtotal = $item['quantity'] * $item['cost'];
                    $totalAmount += $subtotal;

                    PurchaseItem::create([
                        'purchase_id' => $purchase->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'cost' => $item['cost'],
                        'subtotal' => $subtotal,
                    ]);
                }

                $purchase->update(['total_amount' => $totalAmount]);

                // 3. RE-APPLY PHASE (if completed)
                if ($wasCompleted) {
                    // Refresh items
                    $purchase->load('items.product');

                     // Get inventory costing method from settings
                    $costingMethod = \App\Models\Setting::get('inventory_costing_method', 'last_price');

                    foreach ($purchase->items as $item) {
                        $product = $item->product;
                        if (!$product) continue;

                        $conversionFactor = $product->conversion_factor ?? 1;
                        $stockToAdd = $item->quantity * $conversionFactor;
                        $currentStock = $product->stock; // This is now reverted stock
                        $currentCost = $product->cost ?? 0;

                        $product->increment('stock', $stockToAdd);

                        // Recalculate Cost
                        if ($item->cost > 0) {
                            if ($costingMethod === 'wac') {
                                // WAC Logic
                                $oldValue = $currentStock * ($currentCost / $conversionFactor); 
                                $newValue = $item->quantity * $item->cost;
                                
                                $totalStockUnits = $currentStock + $stockToAdd;
                                $totalValue = $oldValue + $newValue; // Simplified
                                
                                // Avoid div by zero
                                if ($totalStockUnits > 0) {
                                    $newWacPerSaleUnit = ($totalValue / $totalStockUnits) / $conversionFactor; // Wait, logic check
                                    // Value = (Qty * Cost). 
                                    // Total Value / Total Units (Sale Units) = Cost Per Sale Unit
                                    $newCost = ($totalValue / $totalStockUnits);
                                    $product->update(['cost' => round($newCost, 2)]);
                                }
                            } else {
                                // Last Price
                                $product->update(['cost' => $item->cost]);
                            }
                        }
                    }

                    // Re-create Journal
                    \App\Services\JournalService::journalPurchase($purchase);
                }

                \App\Models\AuditLog::log('update', 'Mengubah data pembelian ' . $purchase->reference_number, $purchase);
            });

            return redirect()->route('purchases.show', $purchase)->with('success', 'Pembelian berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    /**
     * Update status (e.g., mark as completed)
     */
    public function updateStatus(Request $request, Purchase $purchase)
    {
        if ($purchase->status === 'completed' || $purchase->status === 'cancelled') {
            return back()->with('error', __('messages.purchases.error_status_unchangeable'));
        }

        $request->validate([
            'status' => 'required|in:completed,cancelled'
        ]);

        if ($request->status === 'completed') {
            // Process Stock Update
            DB::transaction(function () use ($purchase) {
                // Get inventory costing method from settings
                $costingMethod = \App\Models\Setting::get('inventory_costing_method', 'last_price');
                
                foreach ($purchase->items as $item) {
                    $product = $item->product;
                    
                    // Update Stock (multiply by conversion_factor for unit conversion)
                    $conversionFactor = $product->conversion_factor ?? 1;
                    $stockToAdd = $item->quantity * $conversionFactor;
                    $currentStock = $product->stock;
                    $currentCost = $product->cost ?? 0;
                    
                    $product->increment('stock', $stockToAdd);
                    
                    // Calculate new cost based on costing method
                    if ($item->cost > 0) {
                        if ($costingMethod === 'wac') {
                            // Weighted Average Cost
                            // WAC = (Old Stock × Old Cost + New Stock × New Cost) / Total Stock
                            $oldValue = $currentStock * ($currentCost / $conversionFactor); // Value of current stock in sale units
                            $newValue = $item->quantity * $item->cost; // Value in purchase units
                            $newValuePerUnit = $newValue / ($item->quantity * $conversionFactor); // Per sale unit
                            
                            $totalStockUnits = $currentStock + $stockToAdd;
                            $totalValue = ($currentStock * ($currentCost / $conversionFactor)) + ($stockToAdd * ($item->cost / $conversionFactor));
                            
                            // New cost per purchase unit = WAC per sale unit × conversion_factor
                            $newWacPerSaleUnit = $totalStockUnits > 0 ? $totalValue / $totalStockUnits : ($item->cost / $conversionFactor);
                            $newCost = $newWacPerSaleUnit * $conversionFactor;
                            
                            // Track price change indicator
                            if (abs($newCost - $currentCost) > 0.01) {
                                $product->update([
                                    'previous_cost' => $currentCost,
                                    'stock_at_old_cost' => $currentStock,
                                    'cost_changed_at' => now(),
                                    'cost' => round($newCost, 2),
                                ]);
                            } else {
                                $product->update(['cost' => round($newCost, 2)]);
                            }
                        } else {
                            // Keep Last Price (default)
                            if ($item->cost != $currentCost) {
                                $product->update([
                                    'previous_cost' => $currentCost,
                                    'stock_at_old_cost' => $currentStock,
                                    'cost_changed_at' => now(),
                                    'cost' => $item->cost,
                                ]);
                            } else {
                                $product->update(['cost' => $item->cost]);
                            }
                        }
                    }
                }

                $purchase->update([
                    'status' => 'completed', 
                    'completed_at' => now()
                ]);

                // Auto-journal
                \App\Services\JournalService::journalPurchase($purchase);
            });
            
            \App\Models\AuditLog::log('update', 'Menyelesaikan pembelian ' . $purchase->reference_number, $purchase);
            
            return back()->with('success', __('messages.purchases.success_complete'));
            
        } else {
            // Check if cancelled
            $purchase->update(['status' => 'cancelled']);
            return back()->with('success', __('messages.purchases.success_cancel'));
        }
    }

    /**
     * Export purchase transactions to Excel.
     */
    public function export(Request $request)
    {
        if (!auth()->user()->hasAdminAccess()) {
            abort(403);
        }

        $query = Purchase::with(['supplier', 'creator', 'items.product'])
            ->latest();

        if ($request->search) {
            $query->where('reference_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('supplier', function($q) use ($request) {
                      $q->where('name', 'like', '%' . $request->search . '%');
                  });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $purchases = $query->get();

        // Create Excel file
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Riwayat Pembelian');

        // Style Settings
        $titleStyle = [
            'font' => ['bold' => true, 'size' => 16],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        ];
        $subtitleStyle = [
            'font' => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '666666']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];

        // Report Title
        $sheet->mergeCells('A1:J1');
        $sheet->setCellValue('A1', __('messages.purchases.excel_title'));
        $sheet->getStyle('A1')->applyFromArray($titleStyle);

        // Filter Info
        $filterStrings = [];
        if ($request->search) $filterStrings[] = __('messages.purchases.excel_search', ['search' => $request->search]);
        if ($request->status) {
            $statusLabels = [
                'pending' => __('messages.purchases.status_pending'), 
                'completed' => __('messages.purchases.status_completed'), 
                'cancelled' => __('messages.purchases.status_cancelled')
            ];
            $filterStrings[] = __('messages.purchases.excel_status', ['status' => ($statusLabels[$request->status] ?? $request->status)]);
        }
        
        $sheet->mergeCells('A2:J2');
        $infoText = empty($filterStrings) ? __('messages.savings_page.all') ?? 'Semua Data' : implode(' | ', $filterStrings);
        $sheet->setCellValue('A2', $infoText . ' | ' . __('messages.purchases.excel_downloaded') . ': ' . date('d/m/Y H:i'));
        $sheet->getStyle('A2')->applyFromArray($subtitleStyle);

        // Empty row
        $sheet->setCellValue('A3', '');

        // Column Headers (at row 4) matching Import Template + Extra Info
        $headers = [
            'No PO', 
            'Tanggal', 
            'Supplier', 
            'Kode Produk', 
            'Nama Produk', 
            'Jumlah', 
            'Harga Satuan', 
            'Subtotal',
            'Status', 
            'Dibuat Oleh'
        ];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '4', $header);
            $col++;
        }
        $sheet->getStyle('A4:J4')->applyFromArray($headerStyle);

        // Data (starting at row 5)
        $row = 5;
        foreach ($purchases as $p) {
            foreach ($p->items as $item) {
                // If purchase has no items (should rare), loop won't run, handle main logic here
                $sheet->setCellValue('A' . $row, $p->reference_number);
                $sheet->setCellValue('B' . $row, $p->purchase_date->format('Y-m-d')); // Format for easier edit
                $sheet->setCellValue('C' . $row, $p->supplier->name ?? '-');
                $sheet->setCellValue('D' . $row, $item->product->code ?? '-');
                $sheet->setCellValue('E' . $row, $item->product->name ?? '-');
                $sheet->setCellValue('F' . $row, $item->quantity);
                $sheet->setCellValue('G' . $row, $item->cost);
                $sheet->setCellValue('H' . $row, $item->subtotal);
                $sheet->setCellValue('I' . $row, strtoupper($p->status));
                $sheet->setCellValue('J' . $row, $p->creator->name ?? '-');
                $row++;
            }
            // Handle case where purchase has no items (optional, but good for data integrity check)
            if ($p->items->isEmpty()) {
                $sheet->setCellValue('A' . $row, $p->reference_number);
                $sheet->setCellValue('B' . $row, $p->purchase_date->format('Y-m-d'));
                $sheet->setCellValue('C' . $row, $p->supplier->name ?? '-');
                $sheet->setCellValue('D' . $row, '-');
                $sheet->setCellValue('E' . $row, 'NO ITEMS');
                $sheet->setCellValue('F' . $row, 0);
                $sheet->setCellValue('G' . $row, 0);
                $sheet->setCellValue('H' . $row, $p->total_amount); // Show total in subtotal col
                $sheet->setCellValue('I' . $row, strtoupper($p->status));
                $sheet->setCellValue('J' . $row, $p->creator->name ?? '-');
                $row++;
            }
        }

        // Format money columns
        $sheet->getStyle('G5:H' . ($row - 1))->getNumberFormat()->setFormatCode('#,##0');

        // Auto-size columns
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Add borders to data
        $sheet->getStyle('A4:J' . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Download
        $filename = 'Riwayat_Pembelian_Detail_' . date('Y-m-d_His') . '.xlsx';
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }
    /**
     * Show import form
     */
    public function import()
    {
        return view('purchases.import');
    }

    /**
     * Process import
     */
    public function storeImport(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        try {
            $import = new \App\Imports\PurchasesImport;
            \Maatwebsite\Excel\Facades\Excel::import($import, $request->file('file'));

            if (count($import->errors) > 0) {
                return back()->with('error', 'Import selesai dengan beberapa error: ' . implode(', ', $import->errors));
            }

            return redirect()->route('purchases.index')->with('success', "Berhasil import {$import->importedCount} transaksi pembelian!");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }

    /**
     * Download Template
     */
    public function downloadTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $headers = ['No PO', 'Tanggal', 'Supplier', 'Kode Produk', 'Jumlah', 'Harga'];
        $sheet->fromArray($headers, null, 'A1');
        
        // Sample Data
        $sheet->fromArray([
            ['PO-2024001', '2024-01-25', 'PT Supplier Jaya', 'PRD001', 50, 10000],
            ['PO-2024001', '2024-01-25', 'PT Supplier Jaya', 'PRD002', 20, 5000],
            ['PO-2024002', '2024-01-26', 'CV Maju Mundur', 'PRD003', 100, 2500],
        ], null, 'A2');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'template_pembelian.xlsx';

        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename);
    }
}
