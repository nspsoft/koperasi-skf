<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Loan;
use App\Models\CreditInstallment;
use App\Models\PayrollExternalDeduction;
use App\Models\Setting;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PayrollDeductionController extends Controller
{
    /**
     * Management positions according to company policy.
     */
    protected array $managementPositions = [
        'asisten manager',
        'assistant manager',
        'manager',
        'deputy kadiv',
        'kadiv',
        'general manager',
        'direktur'
    ];

    /**
     * Display payroll deductions recap.
     */
    public function index(Request $request)
    {
        $period = $request->get('period', Carbon::now()->format('Y-m')); // '2026-10'
        $parsedMonth = Carbon::parse($period . '-01');

        // Custom cut-off date range: default 11th of prev month to 10th of current month
        $defaultStart = (clone $parsedMonth)->subMonth()->day(11)->format('Y-m-d');
        $defaultEnd = (clone $parsedMonth)->day(10)->format('Y-m-d');

        $startDate = $request->get('start_date', $defaultStart);
        $endDate = $request->get('end_date', $defaultEnd);
        $docDate = $request->get('doc_date', (clone $parsedMonth)->day(12)->format('Y-m-d'));

        $data = $this->calculateDeductions($period, $startDate, $endDate);

        return view('reports.payroll-deductions.index', array_merge($data, [
            'period' => $period,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'docDate' => $docDate,
            'monthLabel' => strtoupper($parsedMonth->translatedFormat('F Y')),
        ]));
    }

    /**
     * Calculate all deductions for the period and cut-off dates.
     */
    protected function calculateDeductions(string $period, string $startDate, string $endDate): array
    {
        // 1. Get all active members
        $members = Member::with(['user', 'loans' => function($q) {
            $q->where('status', 'active')->where('remaining_amount', '>', 0)->withCount('payments');
        }])
        ->where('status', 'active')
        ->orderByRaw("CAST(REGEXP_REPLACE(employee_id, '[^0-9]', '') AS UNSIGNED), employee_id ASC")
        ->get();

        // 2. Pre-fetch Credit Installments for Toko Koperasi in the cut-off range
        // Group by user_id
        $creditInstallments = CreditInstallment::join('transactions', 'credit_installments.transaction_id', '=', 'transactions.id')
            ->whereBetween('credit_installments.due_date', [$startDate, $endDate])
            ->whereNotIn('credit_installments.status', ['cancelled'])
            ->select('transactions.user_id', DB::raw('SUM(credit_installments.amount) as total_amount'))
            ->groupBy('transactions.user_id')
            ->pluck('total_amount', 'user_id');

        // Also check any direct credit transactions with single tenor if not in installments
        $directCredits = DB::table('transactions')
            ->where('payment_method', 'kredit')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->whereNotIn('status', ['cancelled', 'completed'])
            ->whereNotExists(function($query) {
                $query->select(DB::raw(1))
                    ->from('credit_installments')
                    ->whereColumn('credit_installments.transaction_id', 'transactions.id');
            })
            ->groupBy('user_id')
            ->pluck(DB::raw('SUM(total_amount - paid_amount) as total_amount'), 'user_id');

        // 3. Pre-fetch External Deductions (Toko AMIGO) for this period
        $amigoDeductions = PayrollExternalDeduction::where('period', $period)
            ->where('source', 'amigo')
            ->get();

        // Map normalized employee_id => amount
        $amigoMap = [];
        foreach ($amigoDeductions as $ad) {
            $norm = PayrollExternalDeduction::normalizeEmployeeId($ad->employee_id);
            $amigoMap[$norm] = ($amigoMap[$norm] ?? 0) + (float) $ad->amount;
        }

        $wajibDefault = (float) Setting::get('mandatory_saving_amount', 100000);

        $factoryList = [];
        $managementList = [];

        foreach ($members as $member) {
            $normEmployeeId = PayrollExternalDeduction::normalizeEmployeeId($member->employee_id);

            // Simpanan Wajib
            $simpananWajib = $wajibDefault;
            $simpananPokok = 0;
            $simpananSukarela = 0;
            $tabungan = 0;

            // Pinjaman Koperasi
            $pinjamanAngsuran = 0;
            $pinjamanKe = null;

            $activeLoan = $member->loans->first();
            if ($activeLoan) {
                $pinjamanAngsuran = (float) $activeLoan->monthly_installment;
                $pinjamanKe = $activeLoan->payments_count + 1;
            }

            // Unit Toko Koperasi
            $tokoKoperasi = (float) ($creditInstallments->get($member->user_id, 0)) + (float) ($directCredits->get($member->user_id, 0));

            // Unit Toko AMIGO
            $tokoAmigo = (float) ($amigoMap[$normEmployeeId] ?? 0);

            // Total Potongan
            $totalJumlah = $simpananPokok + $simpananWajib + $simpananSukarela + $tabungan + $pinjamanAngsuran + $tokoKoperasi + $tokoAmigo;

            $row = [
                'id' => $member->id,
                'member_id' => $member->member_id,
                'employee_id' => $member->employee_id ?: '-',
                'name' => $member->user->name ?? '-',
                'position' => $member->position ?? '-',
                'department' => $member->department ?? '-',
                'simpanan_pokok' => $simpananPokok,
                'simpanan_wajib' => $simpananWajib,
                'simpanan_sukarela' => $simpananSukarela,
                'tabungan' => $tabungan,
                'pinjaman_angsuran' => $pinjamanAngsuran,
                'pinjaman_ke' => $pinjamanKe,
                'toko_koperasi' => $tokoKoperasi,
                'toko_amigo' => $tokoAmigo,
                'jumlah' => $totalJumlah,
            ];

            // Classify: Management vs Factory/Staff
            $posClean = strtolower(trim($member->position ?? ''));
            $isManagement = in_array($posClean, $this->managementPositions);

            if ($isManagement) {
                $managementList[] = $row;
            } else {
                $factoryList[] = $row;
            }
        }

        // Calculate Totals
        $sumTotals = function(array $list) {
            return [
                'count' => count($list),
                'simpanan_pokok' => array_sum(array_column($list, 'simpanan_pokok')),
                'simpanan_wajib' => array_sum(array_column($list, 'simpanan_wajib')),
                'simpanan_sukarela' => array_sum(array_column($list, 'simpanan_sukarela')),
                'tabungan' => array_sum(array_column($list, 'tabungan')),
                'pinjaman_angsuran' => array_sum(array_column($list, 'pinjaman_angsuran')),
                'toko_koperasi' => array_sum(array_column($list, 'toko_koperasi')),
                'toko_amigo' => array_sum(array_column($list, 'toko_amigo')),
                'jumlah' => array_sum(array_column($list, 'jumlah')),
            ];
        };

        $factoryTotals = $sumTotals($factoryList);
        $managementTotals = $sumTotals($managementList);

        $grandTotals = [
            'count' => $factoryTotals['count'] + $managementTotals['count'],
            'simpanan_pokok' => $factoryTotals['simpanan_pokok'] + $managementTotals['simpanan_pokok'],
            'simpanan_wajib' => $factoryTotals['simpanan_wajib'] + $managementTotals['simpanan_wajib'],
            'simpanan_sukarela' => $factoryTotals['simpanan_sukarela'] + $managementTotals['simpanan_sukarela'],
            'tabungan' => $factoryTotals['tabungan'] + $managementTotals['tabungan'],
            'pinjaman_angsuran' => $factoryTotals['pinjaman_angsuran'] + $managementTotals['pinjaman_angsuran'],
            'toko_koperasi' => $factoryTotals['toko_koperasi'] + $managementTotals['toko_koperasi'],
            'toko_amigo' => $factoryTotals['toko_amigo'] + $managementTotals['toko_amigo'],
            'jumlah' => $factoryTotals['jumlah'] + $managementTotals['jumlah'],
        ];

        return [
            'factoryList' => $factoryList,
            'managementList' => $managementList,
            'factoryTotals' => $factoryTotals,
            'managementTotals' => $managementTotals,
            'grandTotals' => $grandTotals,
            'amigoTotalRecords' => count($amigoDeductions),
            'amigoTotalAmount' => (float) $amigoDeductions->sum('amount'),
        ];
    }

    /**
     * Import Excel data for Toko AMIGO deductions.
     */
    public function importAmigo(Request $request)
    {
        $request->validate([
            'period' => 'required|string',
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        $period = $request->period;
        $file = $request->file('file');

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            if (empty($rows)) {
                return redirect()->back()->with('error', 'File Excel kosong.');
            }

            // Find header columns: "Employee No", "Employee Name", "Total"
            $headerRowIndex = null;
            $colEmployeeNo = null;
            $colEmployeeName = null;
            $colTotal = null;

            foreach ($rows as $idx => $row) {
                foreach ($row as $colIdx => $cell) {
                    $cellClean = strtolower(trim((string)$cell));
                    if (in_array($cellClean, ['employee no', 'employee no.', 'no absen', 'no. absen', 'nik', 'employee_id'])) {
                        $colEmployeeNo = $colIdx;
                        $headerRowIndex = $idx;
                    } elseif (in_array($cellClean, ['employee name', 'nama', 'nama karyawan', 'name'])) {
                        $colEmployeeName = $colIdx;
                    } elseif (in_array($cellClean, ['total', 'jumlah', 'nominal', 'amount'])) {
                        $colTotal = $colIdx;
                    }
                }
                if ($colEmployeeNo !== null && $colTotal !== null) {
                    break;
                }
            }

            // Fallback if headers not found by name: assume Column 1 is Employee No, Col 2 Name, Col 3 Total
            if ($colEmployeeNo === null || $colTotal === null) {
                $headerRowIndex = 0;
                $colEmployeeNo = 1; // Col B
                $colEmployeeName = 2; // Col C
                $colTotal = 3; // Col D
            }

            // Get all member employee_ids for smart validation
            $members = Member::all(['id', 'employee_id']);
            $validNormIds = [];
            foreach ($members as $m) {
                if ($m->employee_id) {
                    $validNormIds[PayrollExternalDeduction::normalizeEmployeeId($m->employee_id)] = true;
                }
            }

            // Delete existing records for this period & source
            PayrollExternalDeduction::where('period', $period)->where('source', 'amigo')->delete();

            $importedCount = 0;
            $unmatchedRows = [];

            for ($i = $headerRowIndex + 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                $empNoRaw = trim((string)($row[$colEmployeeNo] ?? ''));
                $empNameRaw = trim((string)($row[$colEmployeeName] ?? ''));
                $totalRaw = $row[$colTotal] ?? 0;

                if (empty($empNoRaw) && empty($totalRaw)) {
                    continue; // skip blank row
                }

                // Clean numeric amount
                if (is_numeric($totalRaw)) {
                    $amount = (float) $totalRaw;
                } else {
                    $cleanNum = preg_replace('/[^0-9.]/', '', str_replace(',', '.', (string)$totalRaw));
                    $amount = (float) $cleanNum;
                }

                if ($amount <= 0) {
                    continue;
                }

                $normEmp = PayrollExternalDeduction::normalizeEmployeeId($empNoRaw);

                if (!isset($validNormIds[$normEmp])) {
                    $unmatchedRows[] = "{$empNoRaw} ({$empNameRaw}) - Rp " . number_format($amount, 0, ',', '.');
                }

                PayrollExternalDeduction::create([
                    'period' => $period,
                    'source' => 'amigo',
                    'employee_id' => $empNoRaw,
                    'employee_name' => $empNameRaw,
                    'amount' => $amount,
                ]);

                $importedCount++;
            }

            $msg = "Berhasil mengimpor {$importedCount} data tagihan Toko AMIGO untuk periode {$period}.";
            if (!empty($unmatchedRows)) {
                $unmatchedCount = count($unmatchedRows);
                $preview = implode(', ', array_slice($unmatchedRows, 0, 5));
                if ($unmatchedCount > 5) $preview .= " ... dan " . ($unmatchedCount - 5) . " lainnya";
                $msg .= " Catatan: {$unmatchedCount} no. absen tidak cocok dengan anggota aktif: [{$preview}].";
            }

            return redirect()->back()->with('success', $msg);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses file Excel: ' . $e->getMessage());
        }
    }

    /**
     * Export exact official Excel matching Image 1 layout.
     */
    public function exportExcel(Request $request)
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(300);

        $period = $request->get('period', Carbon::now()->format('Y-m'));
        $parsedMonth = Carbon::parse($period . '-01');

        $defaultStart = (clone $parsedMonth)->subMonth()->day(11)->format('Y-m-d');
        $defaultEnd = (clone $parsedMonth)->day(10)->format('Y-m-d');

        $startDate = $request->get('start_date', $defaultStart);
        $endDate = $request->get('end_date', $defaultEnd);
        $docDate = $request->get('doc_date', (clone $parsedMonth)->day(12)->format('Y-m-d'));
        $formattedDocDate = Carbon::parse($docDate)->translatedFormat('d F Y');

        $data = $this->calculateDeductions($period, $startDate, $endDate);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Potongan Koperasi');

        // Font setup
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Arial')->setSize(9);

        // Styling definitions
        $titleStyle = [
            'font' => ['bold' => true, 'size' => 12, 'name' => 'Arial'],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ];

        $subTitleStyle = [
            'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ];

        $tableHeaderStyle = [
            'font' => ['bold' => true, 'size' => 9, 'name' => 'Arial'],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];

        $dataRowStyle = [
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];

        $totalRowStyle = [
            'font' => ['bold' => true, 'size' => 9, 'name' => 'Arial'],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];

        $monthUpper = strtoupper($parsedMonth->translatedFormat('F Y'));

        $currentRow = 1;

        // ==========================================
        // SECTION 1: KARYAWAN PABRIK / STAFF UMUM
        // ==========================================
        $sheet->setCellValue("A{$currentRow}", 'DAFTAR POTONGAN KOPERASI SPINDO KARAWANG FACTORY');
        $sheet->getStyle("A{$currentRow}")->applyFromArray($titleStyle);
        $currentRow++;

        $sheet->setCellValue("A{$currentRow}", "BULAN : {$monthUpper}");
        $sheet->getStyle("A{$currentRow}")->applyFromArray($subTitleStyle);
        $currentRow++;

        // Render Table Headers
        $headerStartRow = $currentRow;
        $sheet->mergeCells("A{$headerStartRow}:A" . ($headerStartRow + 1))->setCellValue("A{$headerStartRow}", "No");
        $sheet->mergeCells("B{$headerStartRow}:B" . ($headerStartRow + 1))->setCellValue("B{$headerStartRow}", "No.\nAnggota");
        $sheet->mergeCells("C{$headerStartRow}:C" . ($headerStartRow + 1))->setCellValue("C{$headerStartRow}", "No.\nAbsen");
        $sheet->mergeCells("D{$headerStartRow}:D" . ($headerStartRow + 1))->setCellValue("D{$headerStartRow}", "Nama");

        $sheet->mergeCells("E{$headerStartRow}:G{$headerStartRow}")->setCellValue("E{$headerStartRow}", "Simpanan");
        $sheet->setCellValue("E" . ($headerStartRow + 1), "Pokok");
        $sheet->setCellValue("F" . ($headerStartRow + 1), "Wajib");
        $sheet->setCellValue("G" . ($headerStartRow + 1), "Sukarela");

        $sheet->mergeCells("H{$headerStartRow}:H" . ($headerStartRow + 1))->setCellValue("H{$headerStartRow}", "Tabungan");

        $sheet->mergeCells("I{$headerStartRow}:J{$headerStartRow}")->setCellValue("I{$headerStartRow}", "Pinjaman Koperasi");
        $sheet->setCellValue("I" . ($headerStartRow + 1), "Angsuran");
        $sheet->setCellValue("J" . ($headerStartRow + 1), "Ke");

        $sheet->mergeCells("K{$headerStartRow}:K" . ($headerStartRow + 1))->setCellValue("K{$headerStartRow}", "Unit Toko Koperasi");
        $sheet->mergeCells("L{$headerStartRow}:L" . ($headerStartRow + 1))->setCellValue("L{$headerStartRow}", "Unit Toko AMIGO");
        $sheet->mergeCells("M{$headerStartRow}:M" . ($headerStartRow + 1))->setCellValue("M{$headerStartRow}", "Jumlah");

        $sheet->getStyle("A{$headerStartRow}:M" . ($headerStartRow + 1))->applyFromArray($tableHeaderStyle);
        $currentRow += 2;

        // Render Factory Rows
        $factoryDataStart = $currentRow;
        $no = 1;
        foreach ($data['factoryList'] as $item) {
            $sheet->setCellValue("A{$currentRow}", $no++);
            $sheet->setCellValueExplicit("B{$currentRow}", $item['member_id'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit("C{$currentRow}", $item['employee_id'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue("D{$currentRow}", $item['name']);

            $sheet->setCellValue("E{$currentRow}", $item['simpanan_pokok'] > 0 ? $item['simpanan_pokok'] : '-');
            $sheet->setCellValue("F{$currentRow}", $item['simpanan_wajib'] > 0 ? $item['simpanan_wajib'] : '-');
            $sheet->setCellValue("G{$currentRow}", $item['simpanan_sukarela'] > 0 ? $item['simpanan_sukarela'] : '-');
            $sheet->setCellValue("H{$currentRow}", $item['tabungan'] > 0 ? $item['tabungan'] : '-');

            $sheet->setCellValue("I{$currentRow}", $item['pinjaman_angsuran'] > 0 ? $item['pinjaman_angsuran'] : '-');
            $sheet->setCellValue("J{$currentRow}", $item['pinjaman_ke'] ?: '-');

            $sheet->setCellValue("K{$currentRow}", $item['toko_koperasi'] > 0 ? $item['toko_koperasi'] : '-');
            $sheet->setCellValue("L{$currentRow}", $item['toko_amigo'] > 0 ? $item['toko_amigo'] : '-');
            $sheet->setCellValue("M{$currentRow}", $item['jumlah'] > 0 ? $item['jumlah'] : '-');

            $sheet->getStyle("A{$currentRow}:M{$currentRow}")->applyFromArray($dataRowStyle);

            // Alignments
            $sheet->getStyle("A{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("C{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("J{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("E{$currentRow}:I{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("K{$currentRow}:M{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            $currentRow++;
        }

        // Section 1: JUMLAH
        $sheet->mergeCells("A{$currentRow}:D{$currentRow}")->setCellValue("A{$currentRow}", "JUMLAH");
        $sheet->getStyle("A{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue("E{$currentRow}", $data['factoryTotals']['simpanan_pokok'] > 0 ? $data['factoryTotals']['simpanan_pokok'] : '-');
        $sheet->setCellValue("F{$currentRow}", $data['factoryTotals']['simpanan_wajib'] > 0 ? $data['factoryTotals']['simpanan_wajib'] : '-');
        $sheet->setCellValue("G{$currentRow}", $data['factoryTotals']['simpanan_sukarela'] > 0 ? $data['factoryTotals']['simpanan_sukarela'] : '-');
        $sheet->setCellValue("H{$currentRow}", $data['factoryTotals']['tabungan'] > 0 ? $data['factoryTotals']['tabungan'] : '-');
        $sheet->setCellValue("I{$currentRow}", $data['factoryTotals']['pinjaman_angsuran'] > 0 ? $data['factoryTotals']['pinjaman_angsuran'] : '-');
        $sheet->setCellValue("J{$currentRow}", '-');
        $sheet->setCellValue("K{$currentRow}", $data['factoryTotals']['toko_koperasi'] > 0 ? $data['factoryTotals']['toko_koperasi'] : '-');
        $sheet->setCellValue("L{$currentRow}", $data['factoryTotals']['toko_amigo'] > 0 ? $data['factoryTotals']['toko_amigo'] : '-');
        $sheet->setCellValue("M{$currentRow}", $data['factoryTotals']['jumlah'] > 0 ? $data['factoryTotals']['jumlah'] : '-');

        $sheet->getStyle("A{$currentRow}:M{$currentRow}")->applyFromArray($totalRowStyle);
        $sheet->getStyle("E{$currentRow}:I{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("K{$currentRow}:M{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("J{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $currentRow += 2;

        // Signature Block 1
        $sheet->setCellValue("A{$currentRow}", $formattedDocDate);
        $currentRow++;
        $sheet->setCellValue("A{$currentRow}", "Koperasi Karyawan SKF");
        $currentRow++;
        $sheet->setCellValue("E{$currentRow}", "Mengetahui,");
        $currentRow += 4;

        $sigRowNames1 = $currentRow;
        $sheet->setCellValue("B{$sigRowNames1}", "Citrawati");
        $sheet->setCellValue("E{$sigRowNames1}", "Ngafif Usman");
        $sheet->setCellValue("H{$sigRowNames1}", "Zia Ulhaq Al-Muqtaffi");
        $sheet->setCellValue("L{$sigRowNames1}", "Budi Dwi S");

        $sheet->getStyle("B{$sigRowNames1}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("E{$sigRowNames1}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("H{$sigRowNames1}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("L{$sigRowNames1}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("B{$sigRowNames1}:L{$sigRowNames1}")->getFont()->setUnderline(true)->setBold(true);

        $currentRow++;
        $sigRowRoles1 = $currentRow;
        $sheet->setCellValue("B{$sigRowRoles1}", "Bendahara");
        $sheet->setCellValue("E{$sigRowRoles1}", "K e t u a");
        $sheet->setCellValue("H{$sigRowRoles1}", "RGA&HSE Dept Head SKF");
        $sheet->setCellValue("L{$sigRowRoles1}", "HRD Div. Head");

        $sheet->getStyle("B{$sigRowRoles1}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("E{$sigRowRoles1}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("H{$sigRowRoles1}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("L{$sigRowRoles1}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $currentRow += 3;

        // ==========================================
        // SECTION 2: KARYAWAN MANAJEMEN
        // ==========================================
        $sheet->setCellValue("A{$currentRow}", 'DAFTAR POTONGAN KOPERASI SPINDO KARAWANG FACTORY');
        $sheet->getStyle("A{$currentRow}")->applyFromArray($titleStyle);
        $currentRow++;

        $sheet->setCellValue("A{$currentRow}", "BULAN : {$monthUpper}");
        $sheet->getStyle("A{$currentRow}")->applyFromArray($subTitleStyle);
        $currentRow++;

        $headerStartRow2 = $currentRow;
        $sheet->mergeCells("A{$headerStartRow2}:A" . ($headerStartRow2 + 1))->setCellValue("A{$headerStartRow2}", "No");
        $sheet->mergeCells("B{$headerStartRow2}:B" . ($headerStartRow2 + 1))->setCellValue("B{$headerStartRow2}", "No.\nAng.");
        $sheet->mergeCells("C{$headerStartRow2}:C" . ($headerStartRow2 + 1))->setCellValue("C{$headerStartRow2}", "No.\nAbsen");
        $sheet->mergeCells("D{$headerStartRow2}:D" . ($headerStartRow2 + 1))->setCellValue("D{$headerStartRow2}", "Nama");

        $sheet->mergeCells("E{$headerStartRow2}:G{$headerStartRow2}")->setCellValue("E{$headerStartRow2}", "Simpanan");
        $sheet->setCellValue("E" . ($headerStartRow2 + 1), "Pokok");
        $sheet->setCellValue("F" . ($headerStartRow2 + 1), "Wajib");
        $sheet->setCellValue("G" . ($headerStartRow2 + 1), "Sukarela");

        $sheet->mergeCells("H{$headerStartRow2}:H" . ($headerStartRow2 + 1))->setCellValue("H{$headerStartRow2}", "Tabungan");

        $sheet->mergeCells("I{$headerStartRow2}:J{$headerStartRow2}")->setCellValue("I{$headerStartRow2}", "Pinjaman Koperasi");
        $sheet->setCellValue("I" . ($headerStartRow2 + 1), "Angsuran");
        $sheet->setCellValue("J" . ($headerStartRow2 + 1), "Ke");

        $sheet->mergeCells("K{$headerStartRow2}:K" . ($headerStartRow2 + 1))->setCellValue("K{$headerStartRow2}", "Unit Toko Koperasi");
        $sheet->mergeCells("L{$headerStartRow2}:L" . ($headerStartRow2 + 1))->setCellValue("L{$headerStartRow2}", "Unit Toko AMIGO");
        $sheet->mergeCells("M{$headerStartRow2}:M" . ($headerStartRow2 + 1))->setCellValue("M{$headerStartRow2}", "Jumlah");

        $sheet->getStyle("A{$headerStartRow2}:M" . ($headerStartRow2 + 1))->applyFromArray($tableHeaderStyle);
        $currentRow += 2;

        // Subheader: KARYAWAN MANAJEMEN
        $sheet->mergeCells("A{$currentRow}:M{$currentRow}")->setCellValue("A{$currentRow}", "KARYAWAN MANAJEMEN");
        $sheet->getStyle("A{$currentRow}")->getFont()->setBold(true);
        $sheet->getStyle("A{$currentRow}:M{$currentRow}")->applyFromArray($dataRowStyle);
        $currentRow++;

        $noMgmt = 1;
        foreach ($data['managementList'] as $item) {
            $sheet->setCellValue("A{$currentRow}", $noMgmt++);
            $sheet->setCellValueExplicit("B{$currentRow}", $item['member_id'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit("C{$currentRow}", $item['employee_id'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue("D{$currentRow}", $item['name']);

            $sheet->setCellValue("E{$currentRow}", $item['simpanan_pokok'] > 0 ? $item['simpanan_pokok'] : '-');
            $sheet->setCellValue("F{$currentRow}", $item['simpanan_wajib'] > 0 ? $item['simpanan_wajib'] : '-');
            $sheet->setCellValue("G{$currentRow}", $item['simpanan_sukarela'] > 0 ? $item['simpanan_sukarela'] : '-');
            $sheet->setCellValue("H{$currentRow}", $item['tabungan'] > 0 ? $item['tabungan'] : '-');

            $sheet->setCellValue("I{$currentRow}", $item['pinjaman_angsuran'] > 0 ? $item['pinjaman_angsuran'] : '-');
            $sheet->setCellValue("J{$currentRow}", $item['pinjaman_ke'] ?: '-');

            $sheet->setCellValue("K{$currentRow}", $item['toko_koperasi'] > 0 ? $item['toko_koperasi'] : '-');
            $sheet->setCellValue("L{$currentRow}", $item['toko_amigo'] > 0 ? $item['toko_amigo'] : '-');
            $sheet->setCellValue("M{$currentRow}", $item['jumlah'] > 0 ? $item['jumlah'] : '-');

            $sheet->getStyle("A{$currentRow}:M{$currentRow}")->applyFromArray($dataRowStyle);

            $sheet->getStyle("A{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("C{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("J{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("E{$currentRow}:I{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("K{$currentRow}:M{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            $currentRow++;
        }

        // Section 2: JUMLAH
        $sheet->mergeCells("A{$currentRow}:D{$currentRow}")->setCellValue("A{$currentRow}", "JUMLAH");
        $sheet->getStyle("A{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue("E{$currentRow}", $data['managementTotals']['simpanan_pokok'] > 0 ? $data['managementTotals']['simpanan_pokok'] : '-');
        $sheet->setCellValue("F{$currentRow}", $data['managementTotals']['simpanan_wajib'] > 0 ? $data['managementTotals']['simpanan_wajib'] : '-');
        $sheet->setCellValue("G{$currentRow}", $data['managementTotals']['simpanan_sukarela'] > 0 ? $data['managementTotals']['simpanan_sukarela'] : '-');
        $sheet->setCellValue("H{$currentRow}", $data['managementTotals']['tabungan'] > 0 ? $data['managementTotals']['tabungan'] : '-');
        $sheet->setCellValue("I{$currentRow}", $data['managementTotals']['pinjaman_angsuran'] > 0 ? $data['managementTotals']['pinjaman_angsuran'] : '-');
        $sheet->setCellValue("J{$currentRow}", '-');
        $sheet->setCellValue("K{$currentRow}", $data['managementTotals']['toko_koperasi'] > 0 ? $data['managementTotals']['toko_koperasi'] : '-');
        $sheet->setCellValue("L{$currentRow}", $data['managementTotals']['toko_amigo'] > 0 ? $data['managementTotals']['toko_amigo'] : '-');
        $sheet->setCellValue("M{$currentRow}", $data['managementTotals']['jumlah'] > 0 ? $data['managementTotals']['jumlah'] : '-');

        $sheet->getStyle("A{$currentRow}:M{$currentRow}")->applyFromArray($totalRowStyle);
        $sheet->getStyle("E{$currentRow}:I{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("K{$currentRow}:M{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("J{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $currentRow += 2;

        // Signature Block 2
        $sheet->setCellValue("A{$currentRow}", $formattedDocDate);
        $currentRow++;
        $sheet->setCellValue("A{$currentRow}", "Koperasi Karyawan SKF");
        $currentRow++;
        $sheet->setCellValue("E{$currentRow}", "Mengetahui,");
        $currentRow += 4;

        $sigRowNames2 = $currentRow;
        $sheet->setCellValue("B{$sigRowNames2}", "Citrawati");
        $sheet->setCellValue("E{$sigRowNames2}", "Ngafif Usman");
        $sheet->setCellValue("H{$sigRowNames2}", "Hariyanti K");
        $sheet->setCellValue("L{$sigRowNames2}", "Budi Dwi S");

        $sheet->getStyle("B{$sigRowNames2}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("E{$sigRowNames2}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("H{$sigRowNames2}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("L{$sigRowNames2}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("B{$sigRowNames2}:L{$sigRowNames2}")->getFont()->setUnderline(true)->setBold(true);

        $currentRow++;
        $sigRowRoles2 = $currentRow;
        $sheet->setCellValue("B{$sigRowRoles2}", "Bendahara");
        $sheet->setCellValue("E{$sigRowRoles2}", "K e t u a");
        $sheet->setCellValue("H{$sigRowRoles2}", "HRM SPINDO, Tbk");
        $sheet->setCellValue("L{$sigRowRoles2}", "HRD Div. Head");

        $sheet->getStyle("B{$sigRowRoles2}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("E{$sigRowRoles2}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("H{$sigRowRoles2}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("L{$sigRowRoles2}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Number format: currency with comma / dot
        $sheet->getStyle("E4:I{$currentRow}")->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle("K4:M{$currentRow}")->getNumberFormat()->setFormatCode('#,##0');

        // Column widths
        $sheet->getColumnDimension('A')->setWidth(5);   // No
        $sheet->getColumnDimension('B')->setWidth(14);  // No. Anggota
        $sheet->getColumnDimension('C')->setWidth(11);  // No. Absen
        $sheet->getColumnDimension('D')->setWidth(26);  // Nama
        $sheet->getColumnDimension('E')->setWidth(11);  // Pokok
        $sheet->getColumnDimension('F')->setWidth(12);  // Wajib
        $sheet->getColumnDimension('G')->setWidth(11);  // Sukarela
        $sheet->getColumnDimension('H')->setWidth(11);  // Tabungan
        $sheet->getColumnDimension('I')->setWidth(13);  // Pinjaman Angsuran
        $sheet->getColumnDimension('J')->setWidth(5);   // Ke
        $sheet->getColumnDimension('K')->setWidth(16);  // Toko Koperasi
        $sheet->getColumnDimension('L')->setWidth(16);  // Toko AMIGO
        $sheet->getColumnDimension('M')->setWidth(15);  // Jumlah

        $filename = "Daftar_Potongan_Koperasi_{$period}.xlsx";
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}
