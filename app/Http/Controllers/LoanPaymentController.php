<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\LoanPayment;
use App\Http\Requests\LoanPaymentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoanPaymentController extends Controller
{
    /**
     * Display a listing of loan payments.
     */
    /**
     * Display a listing of loan payments.
     */
    public function index(Request $request)
    {
        $query = LoanPayment::with(['loan.member.user', 'receiver'])
            ->when($request->search, function($q) use ($request) {
                $q->where('payment_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('loan', function($lq) use ($request) {
                      $lq->where('loan_number', 'like', '%' . $request->search . '%')
                         ->orWhereHas('member', function($mq) use ($request) {
                             $mq->where('member_id', 'like', '%' . $request->search . '%')
                                ->orWhereHas('user', function($uq) use ($request) {
                                    $uq->where('name', 'like', '%' . $request->search . '%');
                                });
                         });
                  });
            })
            ->when($request->payment_method, function($q) use ($request) {
                $q->where('payment_method', $request->payment_method);
            })
            ->latest('payment_date');

        $payments = $query->paginate(15)->withQueryString();
            
        return view('loan_payments.index', compact('payments'));
    }

    /**
     * Show the form for creating a new payment.
     */
    public function create(Request $request)
    {
        $loan = null;
        if ($request->has('loan_id')) {
            $loan = Loan::with('member.user')->findOrFail($request->loan_id);
        }
        
        // Only active loans can be paid
        $activeLoans = Loan::with('member.user')
            ->where('status', 'active')
            ->get()
            ->map(function($l) {
                return [
                    'id' => $l->id,
                    'text' => $l->loan_number . ' - ' . $l->member->user->name . ' (Sisa: Rp ' . number_format($l->remaining_amount, 0, ',', '.') . ')'
                ];
            });

        return view('loan_payments.create', compact('loan', 'activeLoans'));
    }

    /**
     * Store a newly created payment in storage.
     */
    public function store(LoanPaymentRequest $request)
    {
        try {
            DB::beginTransaction();

            $loan = Loan::findOrFail($request->loan_id);

            // Calculate parts
            $amount = $request->amount;
            
            // Interest first logic (Simplified: proportional to outstanding)
            // Or use the one stored in Loan table field if we had complex amortization table.
            // For now, we'll just track total paid vs total due.
            
            // Create Payment Record
            $payment = LoanPayment::create([
                'loan_id' => $loan->id,
                'payment_number' => LoanPayment::generatePaymentNumber(),
                'installment_number' => $loan->payments()->count() + 1,
                'amount' => $amount,
                'principal_amount' => $amount, // Simplify: assuming all goes to principal + interest bulk
                'interest_amount' => 0, // Simplify for now
                'payment_date' => $request->payment_date,
                'due_date' => $request->payment_date, // Set default due date same as payment date for now
                'payment_method' => $request->payment_method,
                'status' => 'paid',
                'notes' => $request->notes,
                'received_by' => auth()->id(),
            ]);

            // Update Loan Balance
            $loan->remaining_amount -= $amount;
            
            // Check if paid off
            if ($loan->remaining_amount <= 0) {
                $loan->remaining_amount = 0;
                $loan->status = 'completed';
            }
            
            $loan->save();

            // Auto-journal
            \App\Services\JournalService::journalLoanPayment($payment, $payment->principal_amount, $payment->interest_amount);

            \App\Models\AuditLog::log(
                'create', 
                "Pembayaran angsuran #{$payment->payment_number} sebesar Rp " . number_format($payment->amount, 0, ',', '.') . " untuk pinjaman {$loan->loan_number} (Anggota: {$loan->member->user->name})",
                $payment
            );

            DB::commit();

            return redirect()->route('loans.show', $loan)
                ->with('success', 'Pembayaran berhasil diterima. Sisa pinjaman: Rp ' . number_format($loan->remaining_amount, 0, ',', '.'));

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Export loan payments data to Excel based on current filters.
     */
    public function export(Request $request)
    {
        if (!auth()->user()->hasAdminAccess()) {
            abort(403);
        }

        $query = LoanPayment::with(['loan.member.user', 'receiver'])
            ->when($request->search, function($q) use ($request) {
                $q->where('payment_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('loan', function($lq) use ($request) {
                      $lq->where('loan_number', 'like', '%' . $request->search . '%')
                         ->orWhereHas('member', function($mq) use ($request) {
                             $mq->where('member_id', 'like', '%' . $request->search . '%')
                                ->orWhereHas('user', function($uq) use ($request) {
                                    $uq->where('name', 'like', '%' . $request->search . '%');
                                });
                         });
                  });
            })
            ->when($request->payment_method, function($q) use ($request) {
                $q->where('payment_method', $request->payment_method);
            });

        $payments = $query->latest('payment_date')->get();

        // Create Excel file
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Riwayat Angsuran');

        // Title styling
        $titleStyle = [
            'font' => ['bold' => true, 'size' => 16],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];
        $subtitleStyle = [
            'font' => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '666666']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];

        // Report Title
        $sheet->mergeCells('A1:I1');
        $sheet->setCellValue('A1', 'LAPORAN RIWAYAT ANGSURAN PINJAMAN');
        $sheet->getStyle('A1')->applyFromArray($titleStyle);

        // Filter Info
        $filterInfo = [];
        if ($request->search) $filterInfo[] = "Pencarian: {$request->search}";
        if ($request->payment_method) {
            $methodLabels = ['cash' => 'Tunai', 'transfer' => 'Transfer', 'salary_deduction' => 'Potong Gaji'];
            $filterInfo[] = "Metode: " . ($methodLabels[$request->payment_method] ?? ucfirst($request->payment_method));
        }
        
        $sheet->mergeCells('A2:I2');
        $sheet->setCellValue('A2', empty($filterInfo) ? 'Semua Data - Diunduh: ' . date('d/m/Y H:i') : implode(' | ', $filterInfo) . ' | Diunduh: ' . date('d/m/Y H:i'));
        $sheet->getStyle('A2')->applyFromArray($subtitleStyle);

        // Empty row
        $sheet->setCellValue('A3', '');

        // Header styling
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        ];

        // Column Headers (at row 4)
        $headers = ['No', 'No Pembayaran', 'No Pinjaman', 'ID Anggota', 'Nama Anggota', 'Tanggal Bayar', 'Jumlah', 'Metode', 'Petugas'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '4', $header);
            $col++;
        }
        $sheet->getStyle('A4:I4')->applyFromArray($headerStyle);

        // Payment method labels
        $methodLabels = [
            'cash' => 'Tunai',
            'transfer' => 'Transfer',
            'salary_deduction' => 'Potong Gaji'
        ];

        // Data (starting at row 5)
        $row = 5;
        foreach ($payments as $index => $payment) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $payment->payment_number);
            $sheet->setCellValue('C' . $row, $payment->loan->loan_number ?? '-');
            $sheet->setCellValue('D' . $row, $payment->loan->member->member_id ?? '-');
            $sheet->setCellValue('E' . $row, $payment->loan->member->user->name ?? '-');
            $sheet->setCellValue('F' . $row, $payment->payment_date ? $payment->payment_date->format('d/m/Y') : '-');
            $sheet->setCellValue('G' . $row, $payment->amount);
            $sheet->setCellValue('H' . $row, $methodLabels[$payment->payment_method] ?? ucfirst($payment->payment_method));
            $sheet->setCellValue('I' . $row, $payment->receiver->name ?? '-');
            $row++;
        }

        // Format amount column as number
        $sheet->getStyle('G5:G' . ($row - 1))->getNumberFormat()->setFormatCode('#,##0');

        // Auto-size columns
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Add borders to data
        $sheet->getStyle('A4:I' . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Download
        $filename = 'Riwayat_Angsuran_' . date('Y-m-d_His') . '.xlsx';
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        \App\Models\AuditLog::log(
            'export', 
            "Mengekspor riwayat angsuran ke Excel" . (count($filterInfo) > 0 ? " (Filter: " . implode(', ', $filterInfo) . ")" : "")
        );

        $writer->save('php://output');
        exit;
    }

    /**
     * Bulk delete selected loan payments.
     */
    public function bulkDelete(Request $request)
    {
        if (!auth()->user()->hasAdminAccess()) {
            abort(403);
        }

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:loan_payments,id',
        ]);

        try {
            DB::beginTransaction();

            $payments = LoanPayment::whereIn('id', $request->ids)->get();
            $deletedCount = 0;

            foreach ($payments as $payment) {
                // Restore the loan balance
                $loan = $payment->loan;
                if ($loan) {
                    $loan->remaining_amount += $payment->amount;
                    
                    // If loan was completed, reactivate it
                    if ($loan->status === 'completed') {
                        $loan->status = 'active';
                    }
                    $loan->save();
                }

                // Delete related journal entries first
                \App\Models\JournalEntry::where('reference_type', LoanPayment::class)
                    ->where('reference_id', $payment->id)
                    ->each(function ($journal) {
                        $journal->lines()->delete();
                        $journal->delete();
                    });

                // Delete the payment
                $payment->delete();
                $deletedCount++;
            }

            \App\Models\AuditLog::log(
                'delete', 
                "Menghapus {$deletedCount} data pembayaran pinjaman secara massal"
            );

            DB::commit();

            return redirect()->route('loan-payments.index')
                ->with('success', "Berhasil menghapus {$deletedCount} data pembayaran.");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('loan-payments.index')
                ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
