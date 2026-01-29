<?php

namespace App\Http\Controllers;

use App\Models\DocumentTemplate;
use App\Services\WhatsappService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    /**
     * Send WA notification to members with pending credits
     */
    public function whatsappNotify(Request $request, DocumentTemplate $template)
    {
        if ($template->name !== 'Surat Pemotongan Payroll Kredit Mart') {
            return response()->json(['success' => false, 'message' => 'Fungsi ini hanya untuk dokumen Kredit Mart.'], 400);
        }

        $rawPeriode = $request->periode; // YYYY-MM
        if (! $rawPeriode) {
            return response()->json(['success' => false, 'message' => 'Periode harus dipilih.'], 400);
        }

        $dateParts = explode('-', $rawPeriode);
        $year = $dateParts[0];
        $month = $dateParts[1];

        $credits = \App\Models\Transaction::with('user')
            ->where('payment_method', 'kredit')
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->get()
            ->groupBy('user_id');

        if ($credits->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Tidak ada tagihan kredit pada periode ini.'], 404);
        }

        $waService = app(WhatsappService::class);
        $months = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $periodeStr = $months[(int) $month].' '.$year;

        $successCount = 0;
        $failCount = 0;

        foreach ($credits as $userId => $userTransactions) {
            $user = $userTransactions->first()->user;
            if (! $user || ! $user->phone) {
                $failCount++;

                continue;
            }

            $totalAmount = $userTransactions->sum('total_amount');
            $formattedAmount = number_format($totalAmount, 0, ',', '.');

            $message = "*[Pemberitahuan Koperasi]*\n\n";
            $message .= "Halo Saudara/i *{$user->name}*,\n\n";
            $message .= "Kami informasikan bahwa terdapat tagihan *Kredit Mart* yang belum lunas untuk periode *{$periodeStr}* sebesar:\n";
            $message .= "*Rp {$formattedAmount}*\n\n";
            $message .= "Tagihan ini akan diajukan untuk *Pemotongan Payroll* bulan ini. Mohon pastikan nominal tersebut sudah sesuai.\n\n";
            $message .= "Jika terdapat ketidaksesuaian, mohon segera konfirmasi ke Pengurus Koperasi dalam waktu *2 x 24 jam*. Jika tidak ada konfirmasi, maka tagihan dianggap menyetujui dan akan diproses potong gaji.\n\n";
            $message .= "Terima kasih.\n\n";
            $message .= '_Pesan otomatis sistem Koperasi SPINDO Karawang Factory_';

            if ($waService->sendMessage($user->phone, $message)) {
                $successCount++;
            } else {
                $failCount++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Notifikasi berhasil dikirim ke {$successCount} anggota.".($failCount > 0 ? " ({$failCount} gagal/tidak ada no WA)" : ''),
        ]);
    }

    public function index()
    {
        $templates = DocumentTemplate::all()
            ->groupBy('type')
            ->sortBy(function ($group, $type) {
                return $type === 'official' ? 0 : 1;
            });

        $history = \App\Models\GeneratedDocument::with('user')
            ->whereNotIn('document_type', ['Member Card', 'SHU Slip'])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('documents.index', compact('templates', 'history'));
    }

    public function create(DocumentTemplate $template, Request $request)
    {
        $placeholders = json_decode($template->placeholders, true) ?: [];

        // Define default values for specific templates
        $defaults = [];

        // If editing an existing document
        if ($request->has('from_document')) {
            $existingDoc = \App\Models\GeneratedDocument::find($request->from_document);
            if ($existingDoc) {
                $defaults = $existingDoc->data;
            }
        } else {
            if ($template->name === 'Surat Undangan') {
                $defaults = [
                    'perihal' => 'Undangan Rapat Anggota',
                    'waktu' => '13:00 WIB - Selesai',
                    'tempat' => 'Ruang Meeting Koperasi Lt. 2',
                    'agenda' => "1. Pembukaan\n2. Sambutan Ketua Koperasi\n3. Pembahasan Laporan Bulanan\n4. Sesi Tanya Jawab\n5. Penutup & Doa",
                ];
            } elseif ($template->name === 'Surat Pernyataan') {
                $user = auth()->user();
                $defaults = [
                    'nama_anggota' => $user->name,
                    'nik' => $user->member->employee_id ?? $user->employee_id ?? '-',
                    'isi_pernyataan' => "Bahwa saya bersedia mematuhi segala peraturan dan ketentuan yang berlaku di Koperasi Karyawan Spindo Karawang Factory.\n\nDemikian pernyataan ini saya buat dengan kesadaran penuh tanpa ada paksaan dari pihak manapun.",
                ];
            } elseif ($template->name === 'Surat Pemberitahuan') {
                $defaults = [
                    'perihal' => 'Pemberitahuan Layanan Koperasi',
                    'isi_pemberitahuan' => "Dengan ini kami informasikan kepada seluruh anggota terkait update layanan koperasi.\n\n[Silakan lengkapi detail pemberitahuan di sini]\n\nDemikian informasi ini kami sampaikan, atas perhatian dan kerjasamanya kami ucapkan terima kasih.",
                ];
            } elseif ($template->name === 'Surat Penunjukan Pengurus') {
                $year = date('Y');
                $defaults = [
                    'masa_jabatan' => "Periode $year - ".($year + 3),
                    'jabatan_pengurus' => 'Pengurus',
                ];
            } elseif ($template->name === 'Surat Keterangan Anggota') {
                $user = auth()->user();
                $defaults = [
                    'nama_anggota' => $user->name,
                    'no_anggota' => $user->member->member_id ?? '-',
                    'nik' => $user->member->employee_id ?? $user->employee_id ?? '-',
                    'jabatan' => $user->member->position ?? '-',
                    'tanggal' => date('Y-m-d'),
                ];
            } elseif ($template->name === 'Surat Pengunduran Diri') {
                $user = auth()->user();
                $defaults = [
                    'nama_anggota' => $user->name,
                    'no_anggota' => $user->member->member_id ?? '-',
                    'alasan' => 'Kesibukan Pribadi',
                ];
            } elseif ($template->name === 'Surat Permohonan Pinjaman') {
                $user = auth()->user();
                $defaults = [
                    'nama_anggota' => $user->name,
                    'no_anggota' => $user->member->member_id ?? '-',
                    'nik' => $user->member->employee_id ?? $user->employee_id ?? '-',
                ];
            } elseif ($template->name === 'Surat Perjanjian Pinjaman') {
                $user = auth()->user();
                $defaults = [
                    'nama_anggota' => $user->name,
                ];
            } elseif ($template->name === 'Berita Acara Rapat Pembentukan') {
                $defaults = [
                    'hari_tanggal' => \Carbon\Carbon::now()->translatedFormat('l, d F Y'),
                    'waktu' => '09:00 WIB s/d Selesai',
                    'tempat' => 'Ruang Rapat Utama',
                    'jumlah_hadir' => '20',
                    'pimpinan_rapat' => auth()->user()->name,
                    'sekretaris_rapat' => 'Sekretaris Rapat (Isi Manual)',
                    'nama_koperasi' => 'Koperasi Konsumen Sejahtera Bersama',
                    'ketua_terpilih' => 'Ketua Terpilih (Isi Manual)',
                    'susunan_pengurus_lainnya' => "Wakil Ketua: ...\nSekretaris: ...\nBendahara: ...",
                    'pengawas_terpilih' => 'Pengawas Terpilih (Isi Manual)',
                    'susunan_pengawas_lainnya' => "Anggota Pengawas 1: ...\nAnggota Pengawas 2: ...",
                ];
            } elseif ($template->name === 'Daftar Hadir Rapat Pendirian') {
                $defaults = [
                    'hari_tanggal' => \Carbon\Carbon::now()->translatedFormat('l, d F Y'),
                    'waktu' => '09:00 WIB s/d Selesai',
                    'tempat' => 'Ruang Rapat Utama',
                    'pimpinan_rapat' => auth()->user()->name,
                    'pengawas_terpilih' => 'Pengawas Terpilih (Isi Manual)',
                ];
            } elseif ($template->name === 'Surat Kuasa Pendirian Koperasi') {
                $defaults = [
                    'nama_pemberi_kuasa' => 'Para Pendiri Koperasi (Lihat Lampiran)',
                    'nama_penerima_kuasa' => auth()->user()->name,
                    'nama_koperasi' => 'Koperasi Konsumen Sejahtera Bersama',
                ];
            }

            // Default recipient based on template type
            if (in_array($template->name, ['Surat Undangan', 'Surat Pemberitahuan'])) {
                $defaults['tujuan_penerima'] = 'Seluruh Anggota Koperasi';
            } elseif (in_array($template->name, ['Surat Permohonan Pinjaman', 'Surat Pengunduran Diri'])) {
                $defaults['tujuan_penerima'] = 'Pengurus Koperasi Karyawan Spindo Karawang Factory';
            } elseif (str_contains($template->name, 'Payroll') || str_contains($template->name, 'Kredit Mart')) {
                $defaults['tujuan_penerima'] = 'Bagian HRD / Payroll' . "\n" . 'PT Steel Pipe Industry Of Indonesia Tbk';
            } else {
                $defaults['tujuan_penerima'] = 'Pimpinan / Pihak Terkait';
            }

            // Default code for dynamic numbering
            $defaults['kode_surat'] = $template->code;

            // Auto-Generate Sequential Document Number
            $generatedNumber = $this->generateDocumentNumber($template);
            $defaults['nomor_surat'] = $defaults['nomor_surat'] ?? $generatedNumber;
            $defaults['no_surat'] = $defaults['no_surat'] ?? $generatedNumber;
            $defaults['nomor'] = $defaults['nomor'] ?? $generatedNumber;
        }

        // Fetch all active members for 'nama_anggota' dropdown
        $members = \App\Models\Member::with('user')
            ->where('status', 'active')
            ->get()
            ->sortBy('user.name')
            ->values();

        return view('documents.create', compact('template', 'placeholders', 'defaults', 'members'));
    }

    public function generate(Request $request, DocumentTemplate $template)
    {
        $data = $request->except(['_token']);

        // Add default dynamic placeholders
        $now = Carbon::now();
        $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $months = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        $data['today'] = $now->format('d/m/Y');
        $data['today_full'] = $now->day.' '.$months[$now->month].' '.$now->year;
        $data['day_name'] = $days[$now->dayOfWeek];
        $data['month'] = $now->format('m');
        $data['month_name'] = $months[$now->month];
        $data['year'] = $now->year;

        $content = $template->content;

        // Format 'periode' if present (converting YYYY-MM to Month Year)
        if (isset($data['periode']) && preg_match('/^\d{4}-\d{2}$/', $data['periode'])) {
            $months = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            $dateParts = explode('-', $data['periode']);
            $year = $dateParts[0];
            $month = (int) $dateParts[1];
            $data['periode'] = $months[$month].' '.$year;
        }

        // Format dates for 'tanggal' fields (YYYY-MM-DD to DayName, DD Month YYYY)
        foreach ($data as $key => $val) {
            if (str_contains($key, 'tanggal') && preg_match('/^\d{4}-\d{2}-\d{2}$/', $val)) {
                try {
                    $date = Carbon::parse($val);
                    $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                    $months = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

                    $dayName = $days[$date->dayOfWeek];
                    $monthName = $months[$date->month];

                    $data[$key] = "{$dayName}, {$date->day} {$monthName} {$date->year}";
                } catch (\Exception $e) {
                    // Keep original value if parse fails
                }
            }
        }

        // Handle multiline textareas (convert key newlines to <br>)
        $multilineKeys = ['agenda', 'isi_pernyataan', 'isi_pemberitahuan', 'alasan', 'keperluan', 'susunan_pengurus_lainnya', 'susunan_pengawas_lainnya'];
        foreach ($multilineKeys as $key) {
            if (isset($data[$key])) {
                $data[$key] = nl2br($data[$key]);
            }
        }

        // Update 'today' to be formal Indonesian Date (e.g. 17 Januari 2026) instead of 17/01/2026
        $data['today'] = $now->day.' '.$months[$now->month].' '.$now->year;

        // Fix vertical alignment for Agenda row in Surat Undangan
        if ($template->name === 'Surat Undangan') {
            $content = str_replace(
                '<tr><td>Agenda</td><td>: {{agenda}}</td></tr>',
                '<tr>
                    <td style="vertical-align: top;">Agenda</td>
                    <td style="vertical-align: top; padding: 0;">
                        <table style="width: 100%; border-collapse: collapse; margin: 0;">
                            <tr>
                                <td style="width: 10px; vertical-align: top; padding: 0;">:</td>
                                <td style="vertical-align: top; padding: 0;">{{agenda}}</td>
                            </tr>
                        </table>
                    </td>
                </tr>',
                $content
            );
        }

        // Handle special logic for Payroll Deduction documents
        if ($template->name === 'Surat Pemotongan Payroll Simpanan Wajib') {
            $members = \App\Models\Member::with('user')
                ->where('status', 'active')
                ->get()
                ->sortBy('user.name')
                ->values();

            $mandatoryAmount = \App\Models\Setting::get('saving_mandatory', 100000);

            $table = '<table style="width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 8pt;">';
            $table .= '<thead style="background-color: #f3f4f6;">';
            $table .= '<tr>';
            $table .= '<th style="border: 1px solid #d1d5db; padding: 4px 8px; text-align: center; width: 30px;">No</th>';
            $table .= '<th style="border: 1px solid #d1d5db; padding: 4px 8px; text-align: left;">Nama Anggota</th>';
            $table .= '<th style="border: 1px solid #d1d5db; padding: 4px 8px; text-align: center; width: 100px;">NIK</th>';
            $table .= '<th style="border: 1px solid #d1d5db; padding: 4px 8px; text-align: right; width: 100px;">Nominal (Rp)</th>';
            $table .= '</tr>';
            $table .= '</thead>';
            $table .= '<tbody>';

            $total = 0;
            foreach ($members as $index => $member) {
                $table .= '<tr>';
                $table .= '<td style="border: 1px solid #d1d5db; padding: 4px 8px; text-align: center;">'.($index + 1).'</td>';
                $table .= '<td style="border: 1px solid #d1d5db; padding: 4px 8px;">'.($member->user->name ?? '-').'</td>';
                $table .= '<td style="border: 1px solid #d1d5db; padding: 4px 8px; text-align: center;">'.($member->employee_id ?? '-').'</td>';
                $table .= '<td style="border: 1px solid #d1d5db; padding: 4px 8px; text-align: right;">'.number_format($mandatoryAmount, 0, ',', '.').'</td>';
                $table .= '</tr>';
                $total += $mandatoryAmount;
            }

            $table .= '</tbody>';
            $table .= '<tfoot style="background-color: #f9fafb; font-weight: bold;">';
            $table .= '<tr>';
            $table .= '<td colspan="3" style="border: 1px solid #d1d5db; padding: 4px 8px; text-align: right;">TOTAL</td>';
            $table .= '<td style="border: 1px solid #d1d5db; padding: 4px 8px; text-align: right;">'.number_format($total, 0, ',', '.').'</td>';
            $table .= '</tr>';
            $table .= '</tfoot>';
            $table .= '</table>';

            $data['lampiran_anggota'] = $table;
        } elseif ($template->name === 'Surat Pemotongan Payroll Kredit Mart') {
            // Get period from the formatted string or raw if possible
            // We assume $data['periode'] has been formatted to 'Month Year'
            // We need to parse it back or use the request
            $rawPeriode = $request->periode; // YYYY-MM
            $dateParts = explode('-', $rawPeriode);
            $year = $dateParts[0];
            $month = $dateParts[1];

            $credits = \App\Models\Transaction::with('user.member')
                ->where('payment_method', 'kredit')
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->get()
                ->groupBy('user_id');

            $table = '<table style="width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 8pt;">';
            $table .= '<thead style="background-color: #f3f4f6;">';
            $table .= '<tr>';
            $table .= '<th style="border: 1px solid #d1d5db; padding: 4px 8px; text-align: center; width: 30px;">No</th>';
            $table .= '<th style="border: 1px solid #d1d5db; padding: 4px 8px; text-align: left;">Nama Anggota</th>';
            $table .= '<th style="border: 1px solid #d1d5db; padding: 4px 8px; text-align: center; width: 100px;">NIK</th>';
            $table .= '<th style="border: 1px solid #d1d5db; padding: 4px 8px; text-align: right; width: 100px;">Total Kredit (Rp)</th>';
            $table .= '</tr>';
            $table .= '</thead>';
            $table .= '<tbody>';

            $grandTotal = 0;
            $index = 1;

            // Sort grouped result by user name
            $sortedCredits = $credits->sortBy(function ($group) {
                return $group->first()->user->name ?? '';
            });

            foreach ($sortedCredits as $userId => $userTransactions) {
                $user = $userTransactions->first()->user;
                $totalAmount = $userTransactions->sum('total_amount');

                $table .= '<tr>';
                $table .= '<td style="border: 1px solid #d1d5db; padding: 4px 8px; text-align: center;">'.$index++.'</td>';
                $table .= '<td style="border: 1px solid #d1d5db; padding: 4px 8px;">'.($user->name ?? '-').'</td>';
                $table .= '<td style="border: 1px solid #d1d5db; padding: 4px 8px; text-align: center;">'.($user->member->employee_id ?? '-').'</td>';
                $table .= '<td style="border: 1px solid #d1d5db; padding: 4px 8px; text-align: right;">'.number_format($totalAmount, 0, ',', '.').'</td>';
                $table .= '</tr>';
                $grandTotal += $totalAmount;
            }

            if ($sortedCredits->isEmpty()) {
                $table .= '<tr><td colspan="4" style="border: 1px solid #d1d5db; padding: 10px; text-align: center;">Tidak ada data transaksi kredit untuk periode ini.</td></tr>';
            }

            $table .= '</tbody>';
            $table .= '<tfoot style="background-color: #f9fafb; font-weight: bold;">';
            $table .= '<tr>';
            $table .= '<td colspan="3" style="border: 1px solid #d1d5db; padding: 4px 8px; text-align: right;">TOTAL</td>';
            $table .= '<td style="border: 1px solid #d1d5db; padding: 4px 8px; text-align: right;">'.number_format($grandTotal, 0, ',', '.').'</td>';
            $table .= '</tr>';
            $table .= '</tfoot>';
            $table .= '</table>';

            $data['lampiran_anggota'] = $table;
        }

        foreach ($data as $key => $value) {
            $content = str_replace('{{'.$key.'}}', $value, $content);
        }

        // Fetch logos from settings
        $logo1 = \App\Models\Setting::get('doc_logo_1');
        $logo2 = \App\Models\Setting::get('doc_logo_2');

        // Convert to base64 for DomPDF compatibility
        // Solusi SUPER: Ambil gambar via URL Website (seperti browser)

        // --- Logo 1 ---
        $logo1Base64 = null;
        if ($logo1) {
            // URL: https://kopkarskf.com/storage/logo.png
            $url = asset('storage/'.$logo1);
            $fileData = @file_get_contents($url); // Download dari diri sendiri

            if ($fileData) {
                $ext = pathinfo($logo1, PATHINFO_EXTENSION);
                $logo1Base64 = 'data:image/'.$ext.';base64,'.base64_encode($fileData);
            }
        }

        // --- Logo 2 ---
        $logo2Base64 = null;
        if ($logo2) {
            $url = asset('storage/'.$logo2);
            $fileData = @file_get_contents($url);

            if ($fileData) {
                $ext = pathinfo($logo2, PATHINFO_EXTENSION);
                $logo2Base64 = 'data:image/'.$ext.';base64,'.base64_encode($fileData);
            }
        }

        // Sanitize data for JSON storage to prevent Malformed UTF-8 errors
        $cleanData = array_map(function ($value) {
            if (is_string($value)) {
                return mb_convert_encoding($value, 'UTF-8', 'UTF-8');
            }

            return $value;
        }, $data);

        // Create Generated Document Record for Verification
        $generatedDoc = \App\Models\GeneratedDocument::create([
            'document_number' => $data['nomor_surat'] ?? $data['no_surat'] ?? $data['nomor'] ?? 'DOC/'.time(),
            'document_type' => $template->name,
            'user_id' => auth()->id(),
            'data' => $cleanData,
            'verified_at' => null,
        ]);

        // Generate QR Code for Public Verification
        $verificationUrl = route('documents.verify.public', $generatedDoc->id);

        // Use Local Library (SimpleQrCode)
        $qrCodeBase64 = null;
        try {
            // Use SVG format which is lighter and doesn't require GD extension
            // DomPDF handles SVG data URIs well
            $qrContent = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(150)->margin(0)->generate($verificationUrl);
            // SVG content is XML, need to be careful with base64
            $qrCodeBase64 = 'data:image/svg+xml;base64,'.base64_encode($qrContent);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('QR Generation Failed (Local): '.$e->getMessage());

            // Fallback to External API if local fails
            try {
                $apiUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data='.urlencode($verificationUrl);
                $qrContent = @file_get_contents($apiUrl);
                if ($qrContent) {
                    $qrCodeBase64 = 'data:image/png;base64,'.base64_encode($qrContent);
                }
            } catch (\Exception $ex) {
                \Illuminate\Support\Facades\Log::error('QR Generation Failed (External): '.$ex->getMessage());
            }
        }

        $pdf = Pdf::loadView('documents.pdf_template', [
            'content' => $content,
            'title' => $template->name,
            'logo1' => $logo1Base64,
            'logo2' => $logo2Base64,
            'qrCode' => $qrCodeBase64,
            'documentId' => $generatedDoc->id,
        ]);

        return $pdf->stream($template->name.'_'.$now->format('YmdHis').'.pdf');
    }

    /**
     * Generate a sequential document number.
     * Format: 001/CODE/MONTH_ROMAN/YEAR
     */
    private function generateDocumentNumber($template)
    {
        $code = $template->code ?? 'DOC';
        $month = date('n');
        $year = date('Y');
        $monthRoman = $this->numberToRoman($month);

        // Count generated documents of this type in the current month/year
        $count = \App\Models\GeneratedDocument::where('document_type', $template->name)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count();

        $sequence = str_pad($count + 1, 3, '0', STR_PAD_LEFT);

        return sprintf('%s/%s/%s/%s', $sequence, $code, $monthRoman, $year);
    }

    public function download(\App\Models\GeneratedDocument $generatedDocument)
    {
        if ($generatedDocument->document_type === 'Member Card' && $generatedDocument->reference_id) {
            return redirect()->route('members.card', $generatedDocument->reference_id);
        }

        $template = DocumentTemplate::where('name', $generatedDocument->document_type)->first();
        if (! $template) {
            return back()->with('error', 'Template dokumen tidak ditemukan. Dokumen ini mungkin dibuat dari modul lain.');
        }

        $now = $generatedDocument->created_at;
        $months = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        return $this->renderPdf($template, $generatedDocument->data, $generatedDocument->id, $now, $months);
    }

    public function edit(\App\Models\GeneratedDocument $generatedDocument)
    {
        if ($generatedDocument->document_type === 'Member Card' && $generatedDocument->reference_id) {
            return redirect()->route('members.edit', $generatedDocument->reference_id);
        }

        $template = DocumentTemplate::where('name', $generatedDocument->document_type)->first();
        if (! $template) {
            return back()->with('error', 'Dokumen ini tidak dapat diedit secara kustom karena dibuat secara otomatis oleh sistem.');
        }

        return redirect()->route('documents.create', ['template' => $template->id, 'from_document' => $generatedDocument->id]);
    }

    public function destroy(\App\Models\GeneratedDocument $generatedDocument)
    {
        $generatedDocument->delete();

        return back()->with('success', 'Dokumen berhasil dihapus dari arsip.');
    }

    private function renderPdf($template, $data, $generatedDocId, $now, $months)
    {
        $content = $template->content;

        foreach ($data as $key => $value) {
            // If value is array (for lampiran or complex data), we don't str_replace
            if (! is_array($value)) {
                $content = str_replace('{{'.$key.'}}', $value, $content);
            } else {
                // For lampiran_anggota which is HTML table string (stored in array as string)
                if (is_string($value)) {
                    $content = str_replace('{{'.$key.'}}', $value, $content);
                }
            }
        }

        // Fetch logos
        $logo1 = \App\Models\Setting::get('doc_logo_1');
        $logo2 = \App\Models\Setting::get('doc_logo_2');

        $logo1Base64 = null;
        if ($logo1 && file_exists(public_path('storage/'.$logo1))) {
            $type = pathinfo(public_path('storage/'.$logo1), PATHINFO_EXTENSION);
            $logoContent = file_get_contents(public_path('storage/'.$logo1));
            $logo1Base64 = 'data:image/'.$type.';base64,'.base64_encode($logoContent);
        }

        $logo2Base64 = null;
        if ($logo2 && file_exists(public_path('storage/'.$logo2))) {
            $type = pathinfo(public_path('storage/'.$logo2), PATHINFO_EXTENSION);
            $logoContent = file_get_contents(public_path('storage/'.$logo2));
            $logo2Base64 = 'data:image/'.$type.';base64,'.base64_encode($logoContent);
        }

        // Verification QR
        $verificationUrl = route('documents.verify.public', $generatedDocId);
        $qrCodeBase64 = null;
        try {
            $apiUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data='.urlencode($verificationUrl);
            $qrContent = file_get_contents($apiUrl);
            if ($qrContent) {
                $qrCodeBase64 = 'data:image/png;base64,'.base64_encode($qrContent);
            }
        } catch (\Exception $e) {
        }

        $pdf = Pdf::loadView('documents.pdf_template', [
            'content' => $content,
            'title' => $template->name,
            'logo1' => $logo1Base64,
            'logo2' => $logo2Base64,
            'qrCode' => $qrCodeBase64,
            'documentId' => $generatedDocId,
        ]);

        return $pdf->stream($template->name.'_'.$now->format('YmdHis').'.pdf');
    }

    private function numberToRoman($number)
    {
        $map = ['M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1];
        $returnValue = '';
        while ($number > 0) {
            foreach ($map as $roman => $int) {
                if ($number >= $int) {
                    $number -= $int;
                    $returnValue .= $roman;
                    break;
                }
            }
        }

        return $returnValue;
    }
}
