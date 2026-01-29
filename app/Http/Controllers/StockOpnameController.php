<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockOpname;
use App\Models\StockOpnameItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockOpnameController extends Controller
{
    /**
     * Display listing of stock opnames
     */
    public function index(Request $request)
    {
        $query = StockOpname::with('creator')
            ->when($request->search, function($q) use ($request) {
                $q->where('opname_number', 'like', '%' . $request->search . '%');
            })
            ->when($request->status, function($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->latest('opname_date');

        $opnames = $query->paginate(15)->withQueryString();

        return view('inventory.stock-opname.index', compact('opnames'));
    }

    /**
     * Show form to create new stock opname
     */
    public function create()
    {
        $products = Product::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('inventory.stock-opname.create', compact('products'));
    }

    /**
     * Store new stock opname
     */
    public function store(Request $request)
    {
        $request->validate([
            'opname_date' => 'required|date',
            'notes' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.actual_stock' => 'required|integer|min:0',
            'items.*.notes' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $opname = StockOpname::create([
                'opname_date' => $request->opname_date,
                'notes' => $request->notes,
                'created_by' => auth()->id(),
                'status' => 'draft',
            ]);

            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                
                $opnameItem = new StockOpnameItem([
                    'product_id' => $item['product_id'],
                    'system_stock' => $product->stock ?? 0,
                    'actual_stock' => $item['actual_stock'],
                    'notes' => $item['notes'] ?? null,
                ]);
                $opnameItem->calculateDifference();
                $opname->items()->save($opnameItem);
            }

            DB::commit();

            return redirect()->route('stock-opname.show', $opname)
                ->with('success', __('messages.stock_opname.success_create'));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membuat stock opname: ' . $e->getMessage());
        }
    }

    /**
     * Show stock opname detail
     */
    public function show(StockOpname $stockOpname)
    {
        $stockOpname->load(['creator', 'items.product']);
        
        return view('inventory.stock-opname.show', compact('stockOpname'));
    }

    /**
     * Complete stock opname and adjust stock
     */
    public function complete(StockOpname $stockOpname)
    {
        if ($stockOpname->status !== 'draft') {
            return back()->with('error', __('messages.stock_opname.error_not_draft_complete'));
        }

        try {
            DB::beginTransaction();

            // Adjust stock for each item
            foreach ($stockOpname->items as $item) {
                $product = $item->product;
                if ($product) {
                    $product->stock = $item->actual_stock;
                    $product->save();
                }
            }

            // Update opname status
            $stockOpname->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('stock-opname.show', $stockOpname)
                ->with('success', __('messages.stock_opname.success_complete'));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyelesaikan stock opname: ' . $e->getMessage());
        }
    }

    /**
     * Cancel stock opname
     */
    public function cancel(StockOpname $stockOpname)
    {
        if ($stockOpname->status !== 'draft') {
            return back()->with('error', __('messages.stock_opname.error_not_draft_cancel'));
        }

        $stockOpname->update(['status' => 'cancelled']);

        return redirect()->route('stock-opname.index')
            ->with('success', __('messages.stock_opname.success_cancel'));
    }

    /**
     * Delete stock opname (only draft)
     */
    public function destroy(StockOpname $stockOpname)
    {
        if ($stockOpname->status === 'completed') {
            return back()->with('error', __('messages.stock_opname.error_completed_delete'));
        }

        $stockOpname->delete();

        return redirect()->route('stock-opname.index')
            ->with('success', __('messages.stock_opname.success_delete'));
    }

    /**
     * Export stock opname to Excel
     */
    public function export(StockOpname $stockOpname)
    {
        $stockOpname->load(['creator', 'items.product']);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Stock Opname');

        // Title
        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', __('messages.stock_opname.excel_report_title'));
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Info
        $sheet->setCellValue('A3', __('messages.stock_opname.table_no') . ':');
        $sheet->setCellValue('B3', $stockOpname->opname_number);
        $sheet->setCellValue('A4', __('messages.stock_opname.table_date') . ':');
        $sheet->setCellValue('B4', $stockOpname->opname_date->format('d/m/Y'));
        $sheet->setCellValue('A5', __('messages.stock_opname.table_status') . ':');
        $sheet->setCellValue('B5', $stockOpname->status_label);
        $sheet->setCellValue('A6', __('messages.stock_opname.table_creator') . ':');
        $sheet->setCellValue('B6', $stockOpname->creator->name ?? '-');

        // Headers
        $headers = [
            __('messages.stock_opname.table_col_no'),
            __('messages.stock_opname.table_col_code'),
            __('messages.stock_opname.table_col_name'),
            __('messages.stock_opname.table_col_system'),
            __('messages.stock_opname.table_col_actual'),
            __('messages.stock_opname.table_col_diff'),
            __('messages.stock_opname.table_col_notes')
        ];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '8', $header);
            $col++;
        }
        $sheet->getStyle('A8:G8')->getFont()->setBold(true);
        $sheet->getStyle('A8:G8')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('4F46E5');
        $sheet->getStyle('A8:G8')->getFont()->getColor()->setRGB('FFFFFF');

        // Data
        $row = 9;
        foreach ($stockOpname->items as $index => $item) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $item->product->code ?? '-');
            $sheet->setCellValue('C' . $row, $item->product->name ?? '-');
            $sheet->setCellValue('D' . $row, $item->system_stock);
            $sheet->setCellValue('E' . $row, $item->actual_stock);
            $sheet->setCellValue('F' . $row, $item->difference);
            $sheet->setCellValue('G' . $row, $item->notes ?? '-');

            // Color difference
            if ($item->difference < 0) {
                $sheet->getStyle('F' . $row)->getFont()->getColor()->setRGB('DC2626');
            } elseif ($item->difference > 0) {
                $sheet->getStyle('F' . $row)->getFont()->getColor()->setRGB('16A34A');
            }

            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Add Total Row
        $totalSystemStock = $stockOpname->items->sum('system_stock');
        $totalActualStock = $stockOpname->items->sum('actual_stock');
        $totalDifference = $stockOpname->items->sum('difference');
        
        $sheet->setCellValue('A' . $row, '');
        $sheet->setCellValue('B' . $row, '');
        $sheet->setCellValue('C' . $row, 'TOTAL');
        $sheet->setCellValue('D' . $row, $totalSystemStock);
        $sheet->setCellValue('E' . $row, $totalActualStock);
        $sheet->setCellValue('F' . $row, $totalDifference);
        $sheet->setCellValue('G' . $row, '');
        
        // Style total row
        $sheet->getStyle('C' . $row . ':G' . $row)->getFont()->setBold(true);
        $sheet->getStyle('C' . $row . ':G' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('E5E7EB');
        
        // Color total difference
        if ($totalDifference < 0) {
            $sheet->getStyle('F' . $row)->getFont()->getColor()->setRGB('DC2626');
        } elseif ($totalDifference > 0) {
            $sheet->getStyle('F' . $row)->getFont()->getColor()->setRGB('16A34A');
        }
        
        $row++;

        // Borders (include total row)
        if ($row > 9) {
            $sheet->getStyle('A8:G' . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        }

        // Download using StreamedResponse
        $filename = 'StockOpname_' . $stockOpname->opname_number . '.xlsx';
        
        return response()->streamDownload(function() use ($spreadsheet) {
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
