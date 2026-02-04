<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Account;

class ShuController extends Controller
{
    /**
     * Display SHU dashboard (Admin)
     */
    public function index(Request $request)
    {
        if (!auth()->user()->hasAdminAccess()) abort(403);

        $year = $request->year ?? date('Y');
        
        // Get setting for this year
        $setting = \App\Models\ShuSetting::where('period_year', $year)->first();
        
        // Get existing distributions for this year
        $distributions = \App\Models\ShuDistribution::with('member.user')
            ->where('period_year', $year)
            ->orderByDesc('total_shu')
            ->get();

        // Get available years from settings
        $availableYears = \App\Models\ShuSetting::orderByDesc('period_year')
            ->pluck('period_year');

        return view('shu.index', compact('distributions', 'year', 'availableYears', 'setting'));
    }

    /**
     * Display SHU Tutorial/Guide page
     */
    public function tutorial()
    {
        return view('shu.tutorial');
    }

    /**
     * Display SHU Interactive Simulator
     */
    public function simulator()
    {
        return view('shu.simulator');
    }

    /**
     * Show SHU configuration/calculation form (Admin)
     */
    public function calculator(Request $request)
    {
        if (!auth()->user()->hasAdminAccess()) abort(403);

        $year = $request->year ?? date('Y');
        
        // Get existing setting or create default
        $setting = \App\Models\ShuSetting::firstOrNew(
            ['period_year' => $year],
            [
                'persen_cadangan' => 25,
                'persen_jasa_modal' => 30,
                'persen_jasa_usaha' => 25,
                'persen_pengurus' => 5,
                'persen_karyawan' => 5,
                'persen_pendidikan' => 5,
                'persen_sosial' => 2.5,
                'persen_pembangunan' => 2.5,
            ]
        );

        $availableYears = range(date('Y'), 2020);

        // Calculate current Net Income for this year from Ledger
        $startOfYear = Carbon::create($year, 1, 1)->startOfDay();
        $endOfYear = Carbon::create($year, 12, 31)->endOfDay();
        
        $revenue = \App\Services\JournalService::getTotalRevenue($startOfYear, $endOfYear);
        $expense = \App\Services\JournalService::getTotalExpenses($startOfYear, $endOfYear);
        $suggestedShu = $revenue - $expense;

        return view('shu.calculator', compact('setting', 'year', 'availableYears', 'suggestedShu'));
    }

    /**
     * Save SHU settings (step 1)
     */
    public function saveSettings(Request $request)
    {
        if (!auth()->user()->hasAdminAccess()) abort(403);

        $request->validate([
            'year' => 'required|integer|min:2020|max:' . (date('Y') + 1),
            'total_shu_pool' => 'required|numeric|min:0',
            'persen_cadangan' => 'required|numeric|min:20|max:100', // Min 20% sesuai UU
            'persen_jasa_modal' => 'required|numeric|min:0|max:100',
            'persen_jasa_usaha' => 'required|numeric|min:0|max:100',
            'persen_pengurus' => 'required|numeric|min:0|max:100',
            'persen_karyawan' => 'required|numeric|min:0|max:100',
            'persen_pendidikan' => 'required|numeric|min:0|max:100',
            'persen_sosial' => 'required|numeric|min:0|max:100',
            'persen_pembangunan' => 'required|numeric|min:0|max:100',
        ]);

        // Validate total = 100%
        $total = $request->persen_cadangan + $request->persen_jasa_modal + $request->persen_jasa_usaha +
                 $request->persen_pengurus + $request->persen_karyawan + $request->persen_pendidikan +
                 $request->persen_sosial + $request->persen_pembangunan;

        if (abs($total - 100) > 0.01) {
            return redirect()->back()->withInput()->with('error', 'Total persentase harus 100%. Saat ini: ' . $total . '%');
        }

        $setting = \App\Models\ShuSetting::updateOrCreate(
            ['period_year' => $request->year],
            [
                'total_shu_pool' => $request->total_shu_pool,
                'persen_cadangan' => $request->persen_cadangan,
                'persen_jasa_modal' => $request->persen_jasa_modal,
                'persen_jasa_usaha' => $request->persen_jasa_usaha,
                'persen_pengurus' => $request->persen_pengurus,
                'persen_karyawan' => $request->persen_karyawan,
                'persen_pendidikan' => $request->persen_pendidikan,
                'persen_sosial' => $request->persen_sosial,
                'persen_pembangunan' => $request->persen_pembangunan,
                'status' => 'draft',
                'created_by' => auth()->id(),
            ]
        );

        // Calculate pools
        $setting->calculatePools();
        $setting->save();

        \App\Models\AuditLog::log('create', 'Menyimpan konfigurasi SHU tahun ' . $request->year);

        return redirect()->route('shu.calculator', ['year' => $request->year])
            ->with('success', 'Konfigurasi SHU tahun ' . $request->year . ' berhasil disimpan.');
    }

    /**
     * Post SHU distribution to accounting (Journal)
     */
    public function postToAccounting(Request $request)
    {
        if (!auth()->user()->hasAdminAccess()) abort(403);

        $year = $request->year;
        $setting = \App\Models\ShuSetting::where('period_year', $year)->first();

        if (!$setting || $setting->status == 'posted') {
            return redirect()->back()->with('error', 'SHU tidak ditemukan atau sudah di-posting.');
        }

        try {
            DB::beginTransaction();

            $journalNumber = \App\Models\JournalEntry::generateJournalNumber();
            
            $journal = \App\Models\JournalEntry::create([
                'journal_number' => $journalNumber,
                'transaction_date' => now()->toDateString(),
                'description' => "Pembagian SHU Tahun Buku $year",
                'reference_type' => \App\Models\ShuSetting::class,
                'reference_id' => $setting->id,
                'total_debit' => $setting->total_shu_pool,
                'total_credit' => $setting->total_shu_pool,
                'status' => 'posted',
                'created_by' => auth()->id(),
                'posted_by' => auth()->id(),
                'posted_at' => now(),
            ]);

            // 1. Debit: SHU Belum Dibagikan (2301)
            $acc2301 = Account::where('code', '2301')->first();
            \App\Models\JournalEntryLine::create([
                'journal_entry_id' => $journal->id,
                'account_id' => $acc2301->id,
                'debit' => $setting->total_shu_pool,
                'credit' => 0,
                'description' => "Alokasi SHU $year"
            ]);

            // 2. Credit: Dana Cadangan (3201)
            $acc3201 = Account::where('code', '3201')->first();
            \App\Models\JournalEntryLine::create([
                'journal_entry_id' => $journal->id,
                'account_id' => $acc3201->id,
                'debit' => 0,
                'credit' => $setting->pool_cadangan,
                'description' => "Dana Cadangan SHU $year"
            ]);

            // 3. Credit: Hutang SHU Anggota (2301 - Reuse for members for now or use specific account if exists)
            // Note: In some systems 2301 is Laba, in others it's liability. 
            // Based on our seed: 2301 - SHU Belum Dibagikan. 
            // We should use a specific liability for distributed members or just keep it there but tagged.
            // For simplicity, let's keep it in 2301 for now or a general liability if available.
            \App\Models\JournalEntryLine::create([
                'journal_entry_id' => $journal->id,
                'account_id' => $acc2301->id,
                'debit' => 0,
                'credit' => $setting->pool_anggota,
                'description' => "Hutang SHU Anggota $year"
            ]);

            // 4. Credit: Dana-Dana Lainnya
            $funds = [
                '2401' => $setting->pool_pengurus,
                '2402' => $setting->pool_karyawan,
                '2403' => $setting->pool_pendidikan,
                '2404' => $setting->pool_sosial,
                '2405' => $setting->pool_pembangunan,
            ];

            foreach ($funds as $code => $amount) {
                if ($amount > 0) {
                    $acc = Account::where('code', $code)->first();
                    \App\Models\JournalEntryLine::create([
                        'journal_entry_id' => $journal->id,
                        'account_id' => $acc->id,
                        'debit' => 0,
                        'credit' => $amount,
                        'description' => "$acc->name SHU $year"
                    ]);
                }
            }

            $setting->update(['status' => 'distributed']); // Simplified status
            
            DB::commit();

            \App\Models\AuditLog::log('post', "Memposting Jurnal SHU Tahun $year: $journalNumber");

            return redirect()->back()->with('success', "SHU Tahun $year Berhasil di-posting ke Jurnal Umum.");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memposting jurnal: ' . $e->getMessage());
        }
    }

    /**
     * Calculate SHU for all members (step 2)
     */
    public function calculate(Request $request)
    {
        if (!auth()->user()->hasAdminAccess()) abort(403);

        $year = $request->year;
        
        $setting = \App\Models\ShuSetting::where('period_year', $year)->first();
        if (!$setting) {
            return redirect()->back()->with('error', 'Konfigurasi SHU tahun ' . $year . ' belum dibuat.');
        }

        // Clear existing distributions
        \App\Models\ShuDistribution::where('period_year', $year)->delete();

        // Get all active members
        $members = \App\Models\Member::where('status', 'active')->get();

        // Calculate totals
        $grandTotalSavings = 0;
        $grandTotalTransactions = 0;
        $memberData = [];

        foreach ($members as $member) {
            // Average savings balance during the year
            $memberSavings = \App\Models\Saving::where('member_id', $member->id)
                ->whereYear('transaction_date', '<=', $year)
                ->selectRaw("SUM(CASE WHEN transaction_type = 'deposit' THEN amount ELSE -amount END) as balance")
                ->value('balance') ?? 0;

            // Total transactions (completed purchases)
            $memberTransactions = \App\Models\Transaction::whereHas('user', function($q) use ($member) {
                    $q->where('id', $member->user_id);
                })
                ->where('status', 'completed')
                ->whereYear('created_at', $year)
                ->sum('total_amount');

            $memberData[$member->id] = [
                'savings' => max(0, $memberSavings),
                'transactions' => $memberTransactions,
            ];

            $grandTotalSavings += max(0, $memberSavings);
            $grandTotalTransactions += $memberTransactions;
        }

        // Pool for members = Jasa Modal + Jasa Usaha
        $poolJasaModal = $setting->pool_jasa_modal;
        $poolJasaUsaha = $setting->pool_jasa_usaha;

        foreach ($members as $member) {
            $data = $memberData[$member->id];

            // SHU dari Jasa Modal (berdasarkan simpanan)
            $shuJasaModal = $grandTotalSavings > 0 
                ? ($data['savings'] / $grandTotalSavings) * $poolJasaModal 
                : 0;

            // SHU dari Jasa Usaha (berdasarkan transaksi)
            $shuJasaUsaha = $grandTotalTransactions > 0 
                ? ($data['transactions'] / $grandTotalTransactions) * $poolJasaUsaha 
                : 0;

            $totalShu = $shuJasaModal + $shuJasaUsaha;

            \App\Models\ShuDistribution::create([
                'period_year' => $year,
                'member_id' => $member->id,
                'total_savings' => $data['savings'],
                'total_transactions' => $data['transactions'],
                'total_loans' => 0, // Not used in this simplified version
                'shu_savings' => $shuJasaModal,
                'shu_transactions' => $shuJasaUsaha,
                'shu_jasa' => 0,
                'total_shu' => $totalShu,
                'status' => 'calculated',
                'calculated_by' => auth()->id(),
            ]);
        }

        // Update setting status
        $setting->update(['status' => 'calculated']);

        \App\Models\AuditLog::log('calculate', 'Menghitung SHU tahun ' . $year . ' untuk ' . $members->count() . ' anggota. Pool anggota: Rp ' . number_format($poolJasaModal + $poolJasaUsaha, 0, ',', '.'));

        return redirect()->route('shu.index', ['year' => $year])
            ->with('success', 'SHU tahun ' . $year . ' berhasil dihitung untuk ' . $members->count() . ' anggota.');
    }

    /**
     * Mark all SHU for a year as distributed
     */
    public function distribute(Request $request)
    {
        if (!auth()->user()->hasAdminAccess()) abort(403);

        $year = $request->year;

        $updated = \App\Models\ShuDistribution::where('period_year', $year)
            ->where('status', 'calculated')
            ->update([
                'status' => 'distributed',
                'distributed_at' => now(),
            ]);

        \App\Models\ShuSetting::where('period_year', $year)->update(['status' => 'distributed']);

        \App\Models\AuditLog::log('distribute', 'Mendistribusikan SHU tahun ' . $year . ' kepada ' . $updated . ' anggota.');

        return redirect()->back()->with('success', 'SHU tahun ' . $year . ' telah ditandai sebagai didistribusikan kepada ' . $updated . ' anggota.');
    }

    /**
     * Member view - see their own SHU history
     */
    public function myShu()
    {
        $member = auth()->user()->member;
        if (!$member) {
            return redirect()->back()->with('error', 'Anda tidak terdaftar sebagai anggota.');
        }

        $distributions = \App\Models\ShuDistribution::where('member_id', $member->id)
            ->orderByDesc('period_year')
            ->get();

        return view('shu.my-shu', compact('distributions'));
    }

    /**
     * Print SHU report for a year (Admin)
     */
    public function printReport(Request $request)
    {
        if (!auth()->user()->hasAdminAccess()) abort(403);

        $year = $request->year ?? date('Y');
        
        $setting = \App\Models\ShuSetting::where('period_year', $year)->first();
        if (!$setting) {
            return redirect()->back()->with('error', 'Data SHU tahun ' . $year . ' tidak ditemukan.');
        }
        
        $distributions = \App\Models\ShuDistribution::with('member.user')
            ->where('period_year', $year)
            ->orderByDesc('total_shu')
            ->get();

        return view('shu.print-report', compact('distributions', 'year', 'setting'));
    }

    /**
     * Print individual member SHU slip
     */
    public function printSlip(Request $request, $id)
    {
        $distribution = \App\Models\ShuDistribution::with('member.user')->findOrFail($id);
        
        // Check access
        if (!auth()->user()->hasAdminAccess()) {
            $member = auth()->user()->member;
            if (!$member || $distribution->member_id !== $member->id) {
                abort(403);
            }
        }

        $setting = \App\Models\ShuSetting::where('period_year', $distribution->period_year)->first();

        // Generate document record for verification
        $documentData = [
            'type' => 'SHU Slip',
            'period_year' => $distribution->period_year,
            'member_id' => $distribution->member->member_id ?? '-',
            'member_name' => $distribution->member->user->name ?? '-',
            'total_shu' => $distribution->total_shu,
            'shu_savings' => $distribution->shu_savings,
            'shu_transactions' => $distribution->shu_transactions,
            'status' => $distribution->status,
        ];

        // Check if document already exists for this distribution
        $generatedDocument = \App\Models\GeneratedDocument::where('reference_type', \App\Models\ShuDistribution::class)
            ->where('reference_id', $distribution->id)
            ->first();

        if (!$generatedDocument) {
            // Generate document number: SHU-001/I/2026
            $month = now()->month;
            $year = now()->year;
            $monthRoman = $this->numberToRoman($month);
            
            $existingCount = \App\Models\GeneratedDocument::where('document_type', 'SHU Slip')
                ->whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->count();
            
            $seq = str_pad($existingCount + 1, 3, '0', STR_PAD_LEFT);
            $documentNumber = "SHU-{$seq}/{$monthRoman}/{$year}";

            $generatedDocument = \App\Models\GeneratedDocument::create([
                'document_type' => 'SHU Slip',
                'document_number' => $documentNumber,
                'data' => $documentData,
                'generated_by' => auth()->id(),
                'reference_type' => \App\Models\ShuDistribution::class,
                'reference_id' => $distribution->id,
            ]);
        }

        // Generate QR Code
        $verificationUrl = route('documents.verify.public', $generatedDocument->id);
        $qrApiUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=' . urlencode($verificationUrl);
        
        try {
            $qrImageData = @file_get_contents($qrApiUrl);
            $qrCode = $qrImageData ? 'data:image/png;base64,' . base64_encode($qrImageData) : null;
        } catch (\Exception $e) {
            $qrCode = null;
        }

        return view('shu.print-slip', compact('distribution', 'setting', 'generatedDocument', 'qrCode'));
    }

    /**
     * Convert number to Roman numeral
     */
    private function numberToRoman($number)
    {
        $map = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        return $map[$number - 1] ?? (string)$number;
    }

    /**
     * Export SHU distribution data to Excel for a specific year.
     */
    public function export(Request $request)
    {
        if (!auth()->user()->hasAdminAccess()) {
            abort(403);
        }

        $year = $request->year ?? date('Y');
        
        // Get setting and distributions for this year
        $setting = \App\Models\ShuSetting::where('period_year', $year)->first();
        $distributions = \App\Models\ShuDistribution::with('member.user')
            ->where('period_year', $year)
            ->orderByDesc('total_shu')
            ->get();

        if ($distributions->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data SHU untuk tahun ' . $year);
        }

        // Create Excel file
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('SHU ' . $year);

        // Styling
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
        $summaryStyle = [
            'font' => ['bold' => true],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F3F4F6']],
        ];

        // Report Title
        $sheet->mergeCells('A1:H1');
        $sheet->setCellValue('A1', 'LAPORAN PEMBAGIAN SISA HASIL USAHA (SHU) TAHUN ' . $year);
        $sheet->getStyle('A1')->applyFromArray($titleStyle);

        // SHU Summary Info
        $sheet->setCellValue('A3', 'TOTAL SHU POOL:');
        $sheet->setCellValue('B3', $setting->total_shu_pool ?? 0);
        $sheet->getStyle('B3')->getNumberFormat()->setFormatCode('#,##0');
        
        $sheet->setCellValue('A4', 'STATUS:');
        $sheet->setCellValue('B4', strtoupper($setting->status ?? 'unknown'));

        $sheet->setCellValue('D3', 'JASA MODAL (' . ($setting->persen_jasa_modal ?? 0) . '%):');
        $sheet->setCellValue('E3', $setting->pool_jasa_modal ?? 0);
        $sheet->getStyle('E3')->getNumberFormat()->setFormatCode('#,##0');

        $sheet->setCellValue('D4', 'JASA USAHA (' . ($setting->persen_jasa_usaha ?? 0) . '%):');
        $sheet->setCellValue('E4', $setting->pool_jasa_usaha ?? 0);
        $sheet->getStyle('E4')->getNumberFormat()->setFormatCode('#,##0');

        $sheet->setCellValue('G3', 'DIUNDUH:');
        $sheet->setCellValue('H3', date('d/m/Y H:i'));

        // Column Headers (at row 6)
        $headers = ['No', 'ID Anggota', 'Nama Anggota', 'Saldo Simpanan', 'Total Transaksi', 'SHU Jasa Modal', 'SHU Jasa Usaha', 'Total SHU'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '6', $header);
            $col++;
        }
        $sheet->getStyle('A6:H6')->applyFromArray($headerStyle);

        // Data (starting at row 7)
        $row = 7;
        foreach ($distributions as $index => $dist) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $dist->member->member_id ?? '-');
            $sheet->setCellValue('C' . $row, $dist->member->user->name ?? '-');
            $sheet->setCellValue('D' . $row, $dist->total_savings);
            $sheet->setCellValue('E' . $row, $dist->total_transactions);
            $sheet->setCellValue('F' . $row, $dist->shu_savings);
            $sheet->setCellValue('G' . $row, $dist->shu_transactions);
            $sheet->setCellValue('H' . $row, $dist->total_total_shu ?? $dist->total_shu);
            $row++;
        }

        // Totals row
        $sheet->setCellValue('A' . $row, 'TOTAL');
        $sheet->mergeCells('A' . $row . ':C' . $row);
        $sheet->setCellValue('D' . $row, $distributions->sum('total_savings'));
        $sheet->setCellValue('E' . $row, $distributions->sum('total_transactions'));
        $sheet->setCellValue('F' . $row, $distributions->sum('shu_savings'));
        $sheet->setCellValue('G' . $row, $distributions->sum('shu_transactions'));
        $sheet->setCellValue('H' . $row, $distributions->sum('total_shu'));
        $sheet->getStyle('A' . $row . ':H' . $row)->applyFromArray($summaryStyle);

        // Format numbers
        $sheet->getStyle('D7:H' . $row)->getNumberFormat()->setFormatCode('#,##0');

        // Auto-size columns
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Add borders
        $sheet->getStyle('A6:H' . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Download
        $filename = 'SHU_' . $year . '_' . date('Ymd_His') . '.xlsx';
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }
}
