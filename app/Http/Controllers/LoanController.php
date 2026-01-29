<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Member;
use App\Http\Requests\LoanRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    /**
     * Display a listing of loans.
     */
    public function index(Request $request)
    {
        $query = Loan::with('member.user')
            ->when(!auth()->user()->hasAdminAccess(), function($q) {
                $q->whereHas('member', function($memberQ) {
                    $memberQ->where('user_id', auth()->id());
                });
            })
            ->when($request->search, function($q) use ($request) {
                // Search by Member Name, Member ID, or Loan Number
                $q->where('loan_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('member', function($memberQ) use ($request) {
                      $memberQ->where('member_id', 'like', '%' . $request->search . '%')
                              ->orWhereHas('user', function($userQ) use ($request) {
                                  $userQ->where('name', 'like', '%' . $request->search . '%');
                              });
                  });
            })
            ->when($request->status, function($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->loan_type, function($q) use ($request) {
                $q->where('loan_type', $request->loan_type);
            });

        // Sorting: Prioritaskan status 'pending' di atas
        // Menggunakan CASE WHEN agar kompatibel dengan SQLite
        $loans = $query->orderByRaw("CASE status 
                WHEN 'pending' THEN 1 
                WHEN 'approved' THEN 2 
                WHEN 'active' THEN 3 
                WHEN 'defaulted' THEN 4 
                WHEN 'completed' THEN 5 
                WHEN 'rejected' THEN 6 
                ELSE 7 END")
            ->latest('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('loans.index', compact('loans'));
    }

    /**
     * Show the form for creating a new loan.
     */
    public function create()
    {
        $membersQuery = Member::with('user')->where('status', 'active');
        
        // Jika bukan admin, hanya bisa pilih diri sendiri
        if (!auth()->user()->hasAdminAccess()) {
            $membersQuery->where('user_id', auth()->id());
        }

        $members = $membersQuery->get()
            ->map(function($member) {
                return [
                    'id' => $member->id,
                    'text' => $member->member_id . ' - ' . $member->user->name
                ];
            });

        return view('loans.create', compact('members'));
    }

    /**
     * Store a newly created loan in storage.
     */
    public function store(LoanRequest $request)
    {
        // Validasi kepemilikan jika bukan admin
        if (!auth()->user()->hasAdminAccess()) {
            $member = Member::find($request->member_id);
            if (!$member || $member->user_id !== auth()->id()) {
                abort(403);
            }
        }

        try {
            DB::beginTransaction();

            $loan = new Loan();
            $loan->member_id = $request->member_id;
            $loan->loan_number = Loan::generateLoanNumber();
            $loan->loan_type = $request->loan_type;
            $loan->amount = $request->amount;
            $loan->interest_rate = $request->interest_rate;
            $loan->duration_months = $request->duration_months;
            $loan->application_date = $request->application_date;
            $loan->purpose = $request->purpose;
            $loan->status = 'pending'; // Default status
            $loan->created_by = auth()->id();
            
            // Calculate financial details
            $loan->calculateLoanDetails();
            
            $loan->save();

            \App\Models\AuditLog::log(
                'create', 
                "Mengajukan pinjaman baru: {$loan->loan_number} senilai Rp " . number_format($loan->amount, 0, ',', '.') . " (Anggota: {$loan->member->user->name})",
                $loan
            );

            DB::commit();

            return redirect()->route('loans.index')
                ->with('success', 'Pengajuan pinjaman berhasil dibuat. Menunggu persetujuan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal membuat pengajuan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified loan.
     */
    public function show(Loan $loan)
    {
        $loan->load(['member.user', 'payments', 'approver']);

        if (!auth()->user()->hasAdminAccess() && auth()->id() !== $loan->member->user_id) {
            abort(403);
        }

        return view('loans.show', compact('loan'));
    }

    /**
     * Show the form for editing the specified loan.
     */
    public function edit(Loan $loan)
    {
        if (!auth()->user()->hasAdminAccess() && auth()->id() !== $loan->member->user_id) {
            abort(403);
        }

        if ($loan->status !== 'pending') {
            return redirect()->back()->with('error', 'Hanya pinjaman dengan status pending yang dapat diedit.');
        }
        
        $members = Member::with('user')->get();
        return view('loans.edit', compact('loan', 'members'));
    }

    /**
     * Update the specified loan in storage.
     */
    public function update(LoanRequest $request, Loan $loan)
    {
        if (!auth()->user()->hasAdminAccess() && auth()->id() !== $loan->member->user_id) {
            abort(403);
        }

        if ($loan->status !== 'pending') {
            return redirect()->back()->with('error', 'Hanya pinjaman dengan status pending yang dapat diedit.');
        }

        try {
            DB::beginTransaction();

            // Jika member, dilarang ganti member_id orang lain
            if (!auth()->user()->hasAdminAccess() && $request->member_id != $loan->member_id) {
                 abort(403);
            }
            // Jika admin, boleh ganti member_id

            $loan->member_id = $request->member_id;
            $loan->loan_type = $request->loan_type;
            $loan->amount = $request->amount;
            $loan->interest_rate = $request->interest_rate;
            $loan->duration_months = $request->duration_months;
            $loan->application_date = $request->application_date;
            $loan->purpose = $request->purpose;
            
            // Recalculate financial details
            $loan->calculateLoanDetails();
            
            $loan->save();

            \App\Models\AuditLog::log(
                'update', 
                "Memperbarui data pengajuan pinjaman: {$loan->loan_number}",
                $loan
            );

            DB::commit();

            return redirect()->route('loans.show', $loan)
                ->with('success', 'Data pengajuan berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified loan from storage.
     */
    public function destroy(Loan $loan)
    {
        if (!auth()->user()->hasAdminAccess() && auth()->id() !== $loan->member->user_id) {
            abort(403);
        }

        if (!in_array($loan->status, ['pending', 'rejected'])) {
             return redirect()->back()->with('error', 'Hanya pinjaman pending atau ditolak yang dapat dihapus.');
        }

        DB::beginTransaction();
        try {
            // Delete related journal entries first
            \App\Models\JournalEntry::where('reference_type', Loan::class)
                ->where('reference_id', $loan->id)
                ->each(function ($journal) {
                    $journal->lines()->delete();
                    $journal->delete();
                });

            $loan->delete();

            \App\Models\AuditLog::log(
                'delete', 
                "Menghapus pengajuan pinjaman: " . ($loan->loan_number ?? 'N/A')
            );

            DB::commit();
            return redirect()->route('loans.index')->with('success', 'Pengajuan pinjaman berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }

    /**
     * Remove multiple loans from storage.
     */
    public function bulkDestroy(Request $request)
    {
        if (!auth()->user()->hasAdminAccess()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:loans,id'
        ]);

        try {
            $count = 0;
            $failed = 0;

            DB::beginTransaction();

            foreach ($request->ids as $id) {
                $loan = Loan::find($id);
                
                // Only allow delete if pending or rejected
                if (!in_array($loan->status, ['pending', 'rejected'])) {
                    $failed++;
                    continue;
                }

                // Delete related journal entries first
                \App\Models\JournalEntry::where('reference_type', Loan::class)
                    ->where('reference_id', $loan->id)
                    ->each(function ($journal) {
                        $journal->lines()->delete();
                        $journal->delete();
                    });

                $loan->delete();
                $count++;
            }

            DB::commit();

            $message = "Berhasil menghapus {$count} pengajuan pinjaman.";
            if ($failed > 0) {
                $message .= " ({$failed} pinjaman gagal dihapus karena status bukan pending/rejected)";
            }

            \App\Models\AuditLog::log(
                'delete', 
                "Menghapus {$count} pengajuan pinjaman secara massal" . ($failed > 0 ? " ({$failed} gagal)" : "")
            );

            return redirect()->back()->with($failed > 0 ? 'warning' : 'success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    /**
     * Approve the loan.
     */
    public function approve(Request $request, Loan $loan)
    {
        if (!auth()->user()->hasAdminAccess()) abort(403);
        
        if ($loan->status !== 'pending') {
            return redirect()->back()->with('error', 'Pinjaman tidak dalam status pending.');
        }

        $loan->update([
            'status' => 'approved',
            'approval_date' => now(),
            'approved_by' => auth()->id(),
            'notes' => $request->notes // Optional admin notes
        ]);

        // Log the action
        \App\Models\AuditLog::log('approve', 'Menyetujui pinjaman ' . $loan->loan_number . ' senilai Rp ' . number_format($loan->amount, 0, ',', '.'), $loan);

        // Send notification to member
        if ($loan->member && $loan->member->user) {
            $loan->member->user->notify(new \App\Notifications\LoanApprovedNotification($loan));
        }

        return redirect()->back()->with('success', 'Pinjaman disetujui. Menunggu tanda tangan anggota sebelum pencairan.');
    }

    /**
     * Reject the loan.
     */
    public function reject(Request $request, Loan $loan)
    {
        if (!auth()->user()->hasAdminAccess()) abort(403);

        if ($loan->status !== 'pending') {
            return redirect()->back()->with('error', 'Pinjaman tidak dalam status pending.');
        }

        $loan->update([
            'status' => 'rejected',
            'approval_date' => now(), // Date of rejection
            'approved_by' => auth()->id(), // Admin who rejected
            'notes' => $request->notes // Reason for rejection
        ]);

        // Log the action
        \App\Models\AuditLog::log('reject', 'Menolak pinjaman ' . $loan->loan_number . '. Alasan: ' . ($request->notes ?? 'Tidak ada'), $loan);

        return redirect()->back()->with('success', 'Pinjaman telah ditolak.');
    }

    /**
     * Store signature for the loan.
     */
    public function storeSignature(Request $request, Loan $loan)
    {
        $request->validate([
            'signature' => 'required|string',
        ]);

        if ($loan->status !== 'approved') {
            return response()->json(['message' => 'Hanya pinjaman yang disetujui yang dapat ditandatangani.'], 400);
        }

        if ($loan->signature) {
            return response()->json(['message' => 'Pinjaman ini sudah ditandatangani.'], 400);
        }

        try {
            // Process Base64 Image
            $image_parts = explode(";base64,", $request->signature);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);

            $fileName = 'signatures/' . $loan->loan_number . '_' . time() . '.' . $image_type;
            
            \Illuminate\Support\Facades\Storage::disk('public')->put($fileName, $image_base64);

            $loan->update([
                'signature' => $fileName,
                'signed_at' => now(),
            ]);

            return response()->json(['message' => 'Tanda tangan berhasil disimpan.', 'redirect' => route('loans.show', $loan)]);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menyimpan tanda tangan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Disburse the loan (Cairkan Dana).
     */
    public function disburse(Loan $loan)
    {
        if (!auth()->user()->hasAdminAccess()) abort(403);

        if ($loan->status !== 'approved') {
            return redirect()->back()->with('error', 'Hanya pinjaman yang sudah disetujui yang dapat dicairkan.');
        }

        if (!$loan->signature) {
            return redirect()->back()->with('error', 'Pinjaman belum ditandatangani oleh anggota. Mohon minta anggota untuk melakukan tanda tangan akad.');
        }

        try {
            DB::beginTransaction();

            $loan->update([
                'status' => 'active',
                'disbursement_date' => now(),
                'due_date' => now()->addMonth(), // Cicilan pertama bulan depan
            ]);
            
            // Auto-journal
            \App\Services\JournalService::journalLoanDisbursement($loan);

            \App\Models\AuditLog::log(
                'update', 
                "Mencairkan dana pinjaman: {$loan->loan_number} senilai Rp " . number_format($loan->amount, 0, ',', '.'),
                $loan
            );

            DB::commit();
            return redirect()->back()->with('success', 'Dana pinjaman berhasil dicairkan. Status pinjaman sekarang AKTIF.');

        } catch (\Exception $e) {
            DB::rollBack();
             return redirect()->back()->with('error', 'Gagal mencairkan dana: ' . $e->getMessage());
        }
    }
    /**
     * Show the loan simulation calculator.
     */
    public function simulation()
    {
        return view('loans.simulation');
    }

    /**
     * Export loans data to Excel based on current filters.
     */
    public function export(Request $request)
    {
        if (!auth()->user()->hasAdminAccess()) {
            abort(403);
        }

        $query = Loan::with('member.user')
            ->when($request->search, function($q) use ($request) {
                $q->where('loan_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('member', function($memberQ) use ($request) {
                      $memberQ->where('member_id', 'like', '%' . $request->search . '%')
                              ->orWhereHas('user', function($userQ) use ($request) {
                                  $userQ->where('name', 'like', '%' . $request->search . '%');
                              });
                  });
            })
            ->when($request->status, function($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->loan_type, function($q) use ($request) {
                $q->where('loan_type', $request->loan_type);
            });

        $loans = $query->latest('created_at')->get();

        // Create Excel file
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Pinjaman');

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
        $sheet->mergeCells('A1:K1');
        $sheet->setCellValue('A1', 'LAPORAN DATA PINJAMAN');
        $sheet->getStyle('A1')->applyFromArray($titleStyle);

        // Filter Info
        $filterInfo = [];
        if ($request->search) $filterInfo[] = "Pencarian: {$request->search}";
        if ($request->status) {
            $statusLabels = ['pending' => 'Menunggu', 'approved' => 'Disetujui', 'active' => 'Aktif', 'completed' => 'Lunas', 'rejected' => 'Ditolak'];
            $filterInfo[] = "Status: " . ($statusLabels[$request->status] ?? ucfirst($request->status));
        }
        if ($request->loan_type) {
            $typeLabels = ['regular' => 'Reguler', 'emergency' => 'Darurat', 'education' => 'Pendidikan', 'special' => 'Khusus'];
            $filterInfo[] = "Jenis: " . ($typeLabels[$request->loan_type] ?? ucfirst($request->loan_type));
        }
        
        $sheet->mergeCells('A2:K2');
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
        $headers = ['No', 'No Pinjaman', 'ID Anggota', 'Nama Anggota', 'Jenis', 'Tanggal Pengajuan', 'Jumlah Pinjaman', 'Tenor (Bulan)', 'Sisa Pinjaman', 'Status', 'Keterangan'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '4', $header);
            $col++;
        }
        $sheet->getStyle('A4:K4')->applyFromArray($headerStyle);

        // Status labels
        $statusLabels = [
            'pending' => 'Menunggu',
            'approved' => 'Disetujui',
            'active' => 'Aktif',
            'completed' => 'Lunas',
            'rejected' => 'Ditolak',
            'defaulted' => 'Macet'
        ];

        // Loan type labels
        $typeLabels = [
            'regular' => 'Reguler',
            'emergency' => 'Darurat',
            'education' => 'Pendidikan',
            'special' => 'Khusus'
        ];

        // Data (starting at row 5)
        $row = 5;
        foreach ($loans as $index => $loan) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $loan->loan_number);
            $sheet->setCellValue('C' . $row, $loan->member->member_id ?? '-');
            $sheet->setCellValue('D' . $row, $loan->member->user->name ?? '-');
            $sheet->setCellValue('E' . $row, $typeLabels[$loan->loan_type] ?? ucfirst($loan->loan_type));
            $sheet->setCellValue('F' . $row, $loan->application_date ? $loan->application_date->format('d/m/Y') : '-');
            $sheet->setCellValue('G' . $row, $loan->amount);
            $sheet->setCellValue('H' . $row, $loan->duration_months);
            $sheet->setCellValue('I' . $row, $loan->remaining_amount);
            $sheet->setCellValue('J' . $row, $statusLabels[$loan->status] ?? ucfirst($loan->status));
            $sheet->setCellValue('K' . $row, $loan->purpose ?? '-');
            $row++;
        }

        // Format amount columns as number
        $sheet->getStyle('G5:G' . ($row - 1))->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('I5:I' . ($row - 1))->getNumberFormat()->setFormatCode('#,##0');

        // Auto-size columns
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Add borders to data
        $sheet->getStyle('A4:K' . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Download
        $filename = 'Data_Pinjaman_' . date('Y-m-d_His') . '.xlsx';
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        \App\Models\AuditLog::log(
            'export', 
            "Mengekspor data pinjaman ke Excel" . (count($filterInfo) > 0 ? " (Filter: " . implode(', ', $filterInfo) . ")" : "")
        );

        $writer->save('php://output');
        exit;
    }
}
