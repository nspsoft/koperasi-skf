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
        ]);

        try {
            DB::transaction(function () use ($request) {
                // Create Purchase Header
                $purchase = Purchase::create([
                    'supplier_id' => $request->supplier_id,
                    'reference_number' => $request->reference_number,
                    'purchase_date' => $request->purchase_date,
                    'status' => 'pending',
                    'note' => $request->note,
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
        $sheet->mergeCells('A1:G1');
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
        
        $sheet->mergeCells('A2:G2');
        $infoText = empty($filterStrings) ? __('messages.savings_page.all') ?? 'Semua Data' : implode(' | ', $filterStrings);
        $sheet->setCellValue('A2', $infoText . ' | ' . __('messages.purchases.excel_downloaded') . ': ' . date('d/m/Y H:i'));
        $sheet->getStyle('A2')->applyFromArray($subtitleStyle);

        // Empty row
        $sheet->setCellValue('A3', '');

        // Column Headers (at row 4)
        $headers = [
            'No', 
            __('messages.purchases.table_no'), 
            __('messages.purchases.table_date'), 
            __('messages.purchases.table_supplier'), 
            __('messages.purchases.table_total'), 
            __('messages.purchases.table_status'), 
            __('messages.purchases.table_creator')
        ];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '4', $header);
            $col++;
        }
        $sheet->getStyle('A4:G4')->applyFromArray($headerStyle);

        // Data (starting at row 5)
        $row = 5;
        foreach ($purchases as $index => $p) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $p->reference_number);
            $sheet->setCellValue('C' . $row, $p->purchase_date->format('d/m/Y'));
            $sheet->setCellValue('D' . $row, $p->supplier->name ?? '-');
            $sheet->setCellValue('E' . $row, $p->total_amount);
            $sheet->setCellValue('F' . $row, strtoupper($p->status));
            $sheet->setCellValue('G' . $row, $p->creator->name ?? '-');
            $row++;
        }

        // Format amount column
        $sheet->getStyle('E5:E' . ($row - 1))->getNumberFormat()->setFormatCode('#,##0');

        // Total Row
        $sheet->setCellValue('A' . $row, 'TOTAL');
        $sheet->mergeCells('A' . $row . ':D' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $sheet->setCellValue('E' . $row, $purchases->sum('total_amount'));
        $sheet->getStyle('E' . $row)->getFont()->setBold(true);
        $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('A' . $row . ':G' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('F3F4F6');

        // Auto-size columns
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Add borders to data
        $sheet->getStyle('A4:G' . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Download
        $filename = 'Riwayat_Pembelian_' . date('Y-m-d_His') . '.xlsx';
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }
}
