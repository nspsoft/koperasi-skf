<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Expense::with(['category', 'creator'])->latest('expense_date');

        if ($request->search) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        if ($request->category_id) {
            $query->where('expense_category_id', $request->category_id);
        }

        if ($request->date_start) {
            $query->whereDate('expense_date', '>=', $request->date_start);
        }

        if ($request->date_end) {
            $query->whereDate('expense_date', '<=', $request->date_end);
        }

        $expenses = $query->paginate(15)->withQueryString();
        $categories = ExpenseCategory::orderBy('name')->get();
        
        // Stats
        $totalExpense = Expense::when($request->date_start, function($q) use ($request) {
                $q->whereDate('expense_date', '>=', $request->date_start);
            })
            ->when($request->date_end, function($q) use ($request) {
                $q->whereDate('expense_date', '<=', $request->date_end);
            })
            ->sum('amount');

        return view('expenses.index', compact('expenses', 'categories', 'totalExpense'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = ExpenseCategory::orderBy('name')->get();
        return view('expenses.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'expense_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string',
            'proof' => 'nullable|image|max:2048', // Max 2MB
        ]);

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $data = $request->all();
            $data['created_by'] = auth()->id();

            if ($request->hasFile('proof')) {
                $data['proof'] = $request->file('proof')->store('expenses', 'public');
            }

            $expense = Expense::create($data);

            // Auto-journal
            \App\Services\JournalService::journalExpense($expense);

            \App\Models\AuditLog::log(
                'create', 
                "Mencatat biaya operasional: {$expense->category->name} - Rp " . number_format($expense->amount, 0, ',', '.'),
                $expense
            );

            \Illuminate\Support\Facades\DB::commit();

            return redirect()->route('expenses.index')->with('success', __('messages.expenses.success_created'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            if (isset($data['proof']) && Storage::disk('public')->exists($data['proof'])) {
                Storage::disk('public')->delete($data['proof']);
            }
            return back()->withInput()->with('error', __('messages.expenses.error_save', ['error' => $e->getMessage()]));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Expense $expense)
    {
        return view('expenses.show', compact('expense'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Expense $expense)
    {
        \Illuminate\Support\Facades\Gate::authorize('delete-data');
        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            if ($expense->proof) {
                Storage::disk('public')->delete($expense->proof);
            }

            // Delete related journal entries first
            \App\Models\JournalEntry::where('reference_type', Expense::class)
                ->where('reference_id', $expense->id)
                ->each(function ($journal) {
                    $journal->lines()->delete();
                    $journal->delete();
                });
            
            $expense->delete();

            \App\Models\AuditLog::log(
                'delete', 
                "Menghapus catatan biaya operasional: {$expense->category->name} - Rp " . number_format($expense->amount, 0, ',', '.')
            );

            \Illuminate\Support\Facades\DB::commit();
            return redirect()->route('expenses.index')->with('success', __('messages.expenses.success_deleted'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->back()->with('error', __('messages.expenses.error_delete', ['error' => $e->getMessage()]));
        }
    }

    /**
     * Export operational expenses to Excel.
     */
    public function export(Request $request)
    {
        if (!auth()->user()->hasAdminAccess()) {
            abort(403);
        }

        $query = Expense::with(['category', 'creator'])->latest('expense_date');

        if ($request->search) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        if ($request->category_id) {
            $query->where('expense_category_id', $request->category_id);
        }

        if ($request->date_start) {
            $query->whereDate('expense_date', '>=', $request->date_start);
        }

        if ($request->date_end) {
            $query->whereDate('expense_date', '<=', $request->date_end);
        }

        $expenses = $query->get();

        // Create Excel file
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(__('messages.expenses.title'));

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
        $sheet->mergeCells('A1:F1');
        $sheet->setCellValue('A1', __('messages.expenses.excel_title'));
        $sheet->getStyle('A1')->applyFromArray($titleStyle);

        // Filter Info
        $filterStrings = [];
        if ($request->search) $filterStrings[] = __('messages.expenses.excel_search', ['search' => $request->search]);
        if ($request->category_id) {
            $catName = ExpenseCategory::find($request->category_id)->name ?? '';
            $filterStrings[] = __('messages.expenses.excel_category', ['category' => $catName]);
        }
        if ($request->date_start || $request->date_end) {
            $start = $request->date_start ? date('d/m/Y', strtotime($request->date_start)) : '-';
            $end = $request->date_end ? date('d/m/Y', strtotime($request->date_end)) : '-';
            $filterStrings[] = __('messages.expenses.excel_period', ['start' => $start, 'end' => $end]);
        }
        
        $sheet->mergeCells('A2:F2');
        $infoText = empty($filterStrings) ? (__('messages.savings_page.all') ?? 'Semua Data') : implode(' | ', $filterStrings);
        $sheet->setCellValue('A2', $infoText . ' | ' . __('messages.expenses.excel_downloaded') . ': ' . date('d/m/Y H:i'));
        $sheet->getStyle('A2')->applyFromArray($subtitleStyle);

        // Empty row
        $sheet->setCellValue('A3', '');

        // Column Headers (at row 4)
        $headers = [
            'No', 
            __('messages.expenses.table_date'), 
            __('messages.expenses.table_category'), 
            __('messages.expenses.table_description'), 
            __('messages.expenses.table_amount'), 
            __('messages.expenses.table_creator')
        ];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '4', $header);
            $col++;
        }
        $sheet->getStyle('A4:F4')->applyFromArray($headerStyle);

        // Data (starting at row 5)
        $row = 5;
        foreach ($expenses as $index => $e) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $e->expense_date->format('d/m/Y'));
            $sheet->setCellValue('C' . $row, $e->category->name ?? '-');
            $sheet->setCellValue('D' . $row, $e->description);
            $sheet->setCellValue('E' . $row, $e->amount);
            $sheet->setCellValue('F' . $row, $e->creator->name ?? '-');
            $row++;
        }

        // Format amount column
        $sheet->getStyle('E5:E' . ($row - 1))->getNumberFormat()->setFormatCode('#,##0');

        // Total Row
        $sheet->setCellValue('A' . $row, __('messages.expenses.excel_total'));
        $sheet->mergeCells('A' . $row . ':D' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $sheet->setCellValue('E' . $row, $expenses->sum('amount'));
        $sheet->getStyle('E' . $row)->getFont()->setBold(true);
        $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('A' . $row . ':F' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('F3F4F6');

        // Auto-size columns
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Add borders to data
        $sheet->getStyle('A4:F' . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Download
        $filename = 'Biaya_Operasional_' . date('Y-m-d_His') . '.xlsx';
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        \App\Models\AuditLog::log(
            'export', 
            "Mengekspor laporan biaya operasional ke Excel" . (count($filterStrings) > 0 ? " (Filter: " . implode(', ', $filterStrings) . ")" : "")
        );

        $writer->save('php://output');
        exit;
    }
}
