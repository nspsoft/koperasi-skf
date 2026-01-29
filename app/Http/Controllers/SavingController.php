<?php

namespace App\Http\Controllers;

use App\Models\Saving;
use App\Models\Member;
use App\Http\Requests\SavingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SavingController extends Controller
{
    /**
     * Display a listing of savings.
     */
    public function index(Request $request)
    {
        $query = Saving::with('member.user')
            ->when(!auth()->user()->hasAdminAccess(), function($q) {
                $q->whereHas('member', function($memberQ) {
                    $memberQ->where('user_id', auth()->id());
                });
            })
            ->when($request->search, function($q) use ($request) {
                $q->where('reference_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('member', function($memberQ) use ($request) {
                      $memberQ->where('member_id', 'like', '%' . $request->search . '%')
                              ->orWhereHas('user', function($userQ) use ($request) {
                                  $userQ->where('name', 'like', '%' . $request->search . '%');
                              });
                  });
            })
            ->when($request->type, function($q) use ($request) {
                $q->where('type', $request->type);
            })
            ->when($request->transaction_type, function($q) use ($request) {
                $q->where('transaction_type', $request->transaction_type);
            })
            ->when($request->date_start, function($q) use ($request) {
                $q->whereDate('transaction_date', '>=', $request->date_start);
            })
            ->when($request->date_end, function($q) use ($request) {
                $q->whereDate('transaction_date', '<=', $request->date_end);
            });

        $savings = $query->latest('transaction_date')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('savings.index', compact('savings'));
    }

    /**
     * Show the form for creating a new saving transaction.
     */
    public function create()
    {
        if (!auth()->user()->hasAdminAccess()) {
            abort(403);
        }

        $members = Member::with('user')
            ->where('status', 'active')
            ->get()
            ->map(function($member) {
                return [
                    'id' => $member->id,
                    'text' => $member->member_id . ' - ' . $member->user->name
                ];
            });

        $settings = [
            'principal' => \App\Models\Setting::where('key', 'saving_principal')->value('value'),
            'mandatory' => \App\Models\Setting::where('key', 'saving_mandatory')->value('value'),
        ];

        return view('savings.create', compact('members', 'settings'));
    }

    /**
     * Store a newly created saving in storage.
     */
    public function store(SavingRequest $request)
    {
        if (!auth()->user()->hasAdminAccess()) {
            abort(403);
        }

        try {
            DB::beginTransaction();

            $saving = Saving::create([
                'member_id' => $request->member_id,
                'type' => $request->type,
                'transaction_type' => $request->transaction_type,
                'amount' => $request->amount,
                'transaction_date' => $request->transaction_date,
                'reference_number' => Saving::generateReferenceNumber(),
                'description' => $request->description,
                'created_by' => auth()->id(),
            ]);

            // Auto-journal
            if ($saving->transaction_type == 'deposit') {
                \App\Services\JournalService::journalSavingDeposit($saving);
            } else {
                \App\Services\JournalService::journalSavingWithdrawal($saving);
            }

            \App\Models\AuditLog::log(
                'create', 
                "Input transaksi simpanan {$saving->type_label} Rp " . number_format($saving->amount, 0, ',', '.') . " untuk anggota {$saving->member->user->name}",
                $saving
            );

            DB::commit();

            return redirect()->route('savings.index')
                ->with('success', 'Transaksi berhasil disimpan. No Ref: ' . $saving->reference_number);

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified saving.
     */
    public function show(Saving $saving)
    {
        $saving->load(['member.user', 'creator']);

        if (!auth()->user()->hasAdminAccess() && auth()->id() !== $saving->member->user_id) {
            abort(403);
        }

        return view('savings.show', compact('saving'));
    }

    /**
     * Remove the specified saving from storage.
     */
    public function destroy(Saving $saving)
    {
        try {
            \Illuminate\Support\Facades\Gate::authorize('delete-data');

            DB::beginTransaction();

            // Delete related journal entries first
            \App\Models\JournalEntry::where('reference_type', Saving::class)
                ->where('reference_id', $saving->id)
                ->each(function ($journal) {
                    $journal->lines()->delete();
                    $journal->delete();
                });

            \App\Models\AuditLog::log(
                'delete', 
                "Menghapus transaksi simpanan {$saving->reference_number} senilai Rp " . number_format($saving->amount, 0, ',', '.') . " (Anggota: {$saving->member->user->name})"
            );

            $saving->delete();

            DB::commit();

            return redirect()->back()
                ->with('success', 'Transaksi berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal menghapus transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Remove multiple savings from storage.
     */
    public function bulkDestroy(Request $request)
    {
        \Illuminate\Support\Facades\Gate::authorize('delete-data');

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:savings,id'
        ]);

        try {
            $count = 0;
            DB::beginTransaction();

            foreach ($request->ids as $id) {
                $saving = Saving::find($id);
                
                // Delete related journal entries first
                \App\Models\JournalEntry::where('reference_type', Saving::class)
                    ->where('reference_id', $saving->id)
                    ->each(function ($journal) {
                        $journal->lines()->delete();
                        $journal->delete();
                    });

                $saving->delete();
                $count++;
            }

            DB::commit();

            \App\Models\AuditLog::log(
                'delete', 
                "Menghapus {$count} transaksi simpanan secara massal"
            );

            return redirect()->back()->with('success', "Berhasil menghapus {$count} transaksi simpanan.");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    /**
     * Print savings book (Buku Tabungan) for a member.
     */
    public function printBook(Member $member)
    {
        if (!auth()->user()->hasAdminAccess() && auth()->id() !== $member->user_id) {
            abort(403);
        }

        $member->load('user');
        
        $savings = Saving::where('member_id', $member->id)
            ->orderBy('transaction_date', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('savings.print_book', compact('member', 'savings'));
    }

    /**
     * Export savings data to Excel based on current filters.
     */
    public function export(Request $request)
    {
        if (!auth()->user()->hasAdminAccess()) {
            abort(403);
        }

        $query = Saving::with('member.user')
            ->when($request->search, function($q) use ($request) {
                $q->where('reference_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('member', function($memberQ) use ($request) {
                      $memberQ->where('member_id', 'like', '%' . $request->search . '%')
                              ->orWhereHas('user', function($userQ) use ($request) {
                                  $userQ->where('name', 'like', '%' . $request->search . '%');
                              });
                  });
            })
            ->when($request->type, function($q) use ($request) {
                $q->where('type', $request->type);
            })
            ->when($request->transaction_type, function($q) use ($request) {
                $q->where('transaction_type', $request->transaction_type);
            })
            ->when($request->date_start, function($q) use ($request) {
                $q->whereDate('transaction_date', '>=', $request->date_start);
            })
            ->when($request->date_end, function($q) use ($request) {
                $q->whereDate('transaction_date', '<=', $request->date_end);
            });

        $savings = $query->latest('transaction_date')->latest('id')->get();

        // Create Excel file
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Transaksi Simpanan');

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
        $sheet->setCellValue('A1', 'LAPORAN TRANSAKSI SIMPANAN');
        $sheet->getStyle('A1')->applyFromArray($titleStyle);

        // Filter Info
        $filterInfo = [];
        if ($request->search) $filterInfo[] = "Pencarian: {$request->search}";
        if ($request->type) $filterInfo[] = "Jenis: " . ucfirst($request->type);
        if ($request->transaction_type) $filterInfo[] = "Transaksi: " . ($request->transaction_type === 'deposit' ? 'Setoran' : 'Penarikan');
        if ($request->date_start) $filterInfo[] = "Dari: {$request->date_start}";
        if ($request->date_end) $filterInfo[] = "Sampai: {$request->date_end}";
        
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

        // Column Headers (now at row 4)
        $headers = ['No', 'Tanggal', 'No Referensi', 'ID Anggota', 'Nama Anggota', 'Jenis Simpanan', 'Jenis Transaksi', 'Jumlah', 'Keterangan'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '4', $header);
            $col++;
        }
        $sheet->getStyle('A4:I4')->applyFromArray($headerStyle);

        // Data (now starting at row 5)
        $row = 5;
        foreach ($savings as $index => $saving) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $saving->transaction_date->format('d/m/Y'));
            $sheet->setCellValue('C' . $row, $saving->reference_number);
            $sheet->setCellValue('D' . $row, $saving->member->member_id ?? '-');
            $sheet->setCellValue('E' . $row, $saving->member->user->name ?? '-');
            $sheet->setCellValue('F' . $row, ucfirst($saving->type));
            $sheet->setCellValue('G' . $row, $saving->transaction_type === 'deposit' ? 'Setoran' : 'Penarikan');
            $sheet->setCellValue('H' . $row, $saving->amount);
            $sheet->setCellValue('I' . $row, $saving->description ?? '-');
            $row++;
        }

        // Format amount as number
        $sheet->getStyle('H5:H' . ($row - 1))->getNumberFormat()->setFormatCode('#,##0');

        // Auto-size columns
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Add borders to data
        $sheet->getStyle('A4:I' . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Download
        $filename = 'Transaksi_Simpanan_' . date('Y-m-d_His') . '.xlsx';
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        \App\Models\AuditLog::log(
            'export', 
            "Mengekspor data transaksi simpanan ke Excel" . (count($filterInfo) > 0 ? " (Filter: " . implode(', ', $filterInfo) . ")" : "")
        );

        $writer->save('php://output');
        exit;
    }
}
