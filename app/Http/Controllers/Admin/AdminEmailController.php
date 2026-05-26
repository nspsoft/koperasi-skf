<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Webklex\IMAP\Facades\Client;

class AdminEmailController extends Controller
{
    /**
     * Folder mapping: URL slug => IMAP folder name
     */
    private const FOLDER_MAP = [
        'inbox'   => 'INBOX',
        'sent'    => 'INBOX.Sent',
        'drafts'  => 'INBOX.Drafts',
        'trash'   => 'INBOX.Trash',
        'spam'    => 'INBOX.Spam',
    ];

    /**
     * Check if user has email access permission.
     */
    private function authorizeEmail()
    {
        $user = auth()->user();
        if (!$user->isAdmin() && !$user->hasPermission('manage_email')) {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * Resolve the IMAP folder from slug, with fallback attempts.
     */
    private function resolveFolder($client, string $slug)
    {
        $imapName = self::FOLDER_MAP[$slug] ?? 'INBOX';

        // Try the primary folder name
        try {
            $folder = $client->getFolder($imapName);
            if ($folder) return $folder;
        } catch (\Exception $e) {}

        // Fallback: try common cPanel alternatives
        $alternatives = [
            'INBOX.Sent'   => ['Sent', 'Sent Messages', 'INBOX.Sent Messages'],
            'INBOX.Drafts' => ['Drafts', 'INBOX.Draft'],
            'INBOX.Trash'  => ['Trash', 'Deleted Messages', 'INBOX.Deleted Messages'],
            'INBOX.Spam'   => ['Spam', 'Junk', 'INBOX.Junk', 'Junk E-mail'],
        ];

        if (isset($alternatives[$imapName])) {
            foreach ($alternatives[$imapName] as $alt) {
                try {
                    $folder = $client->getFolder($alt);
                    if ($folder) return $folder;
                } catch (\Exception $e) {}
            }
        }

        // Final fallback to INBOX
        return $client->getFolder('INBOX');
    }

    /**
     * Display a listing of emails (with folder support).
     */
    public function index(Request $request, $folder = 'inbox')
    {
        $this->authorizeEmail();

        $activeFolder = $folder;
        $searchQuery = $request->get('q', '');

        try {
            /** @var \Webklex\PHPIMAP\Client $client */
            $client = Client::account('default');
            $client->connect();
            
            $imapFolder = $this->resolveFolder($client, $folder);
            
            $page = $request->get('page', 1);
            $perPage = 15;

            $query = $imapFolder->query();
            
            if (!empty($searchQuery)) {
                // IMAP search: OR search across subject, from, and body
                $query = $query->where([
                    ['OR'],
                    ['SUBJECT', $searchQuery],
                    ['FROM', $searchQuery],
                    ['BODY', $searchQuery],
                ]);
            } else {
                $query = $query->all();
            }
            
            $messages = $query->setFetchOrder("desc")->paginate($perPage, $page, 'page');
            
            return view('admin.email.index', compact('messages', 'activeFolder', 'searchQuery'));
            
        } catch (\Exception $e) {
            return view('admin.email.index', [
                'messages' => null,
                'activeFolder' => $activeFolder,
                'searchQuery' => $searchQuery,
                'imapError' => 'Gagal terhubung ke server email: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Display the specified email in split-pane view.
     */
    public function show(Request $request, $folder, $uid)
    {
        $this->authorizeEmail();

        $activeFolder = $folder;

        try {
            /** @var \Webklex\PHPIMAP\Client $client */
            $client = Client::account('default');
            $client->connect();
            
            $imapFolder = $this->resolveFolder($client, $folder);
            
            $page = $request->get('page', 1);
            $perPage = 15;
            $messages = $imapFolder->query()->all()->setFetchOrder("desc")->paginate($perPage, $page, 'page');

            $selectedMessage = $imapFolder->query()->getMessageByUid($uid);
            
            if (!$selectedMessage) {
                return redirect()->route('admin.email.folder', $folder)->with('error', 'Email tidak ditemukan.');
            }
            
            if(!$selectedMessage->hasFlag('seen')) {
                $selectedMessage->setFlag('seen');
            }
            
            return view('admin.email.index', compact('messages', 'selectedMessage', 'activeFolder'));
            
        } catch (\Exception $e) {
            return redirect()->route('admin.email.folder', $folder)->with('error', 'Gagal memuat email: ' . $e->getMessage());
        }
    }

    /**
     * Show compose form (new email).
     */
    public function compose(Request $request)
    {
        $this->authorizeEmail();

        $composeMode = 'new';
        $composeTo = $request->get('to', '');
        $composeSubject = $request->get('subject', '');
        $composeBody = '';
        $activeFolder = 'inbox';

        try {
            $client = Client::account('default');
            $client->connect();
            $imapFolder = $client->getFolder('INBOX');
            $page = $request->get('page', 1);
            $perPage = 15;
            $messages = $imapFolder->query()->all()->setFetchOrder("desc")->paginate($perPage, $page, 'page');
        } catch (\Exception $e) {
            $messages = null;
        }

        return view('admin.email.index', compact('messages', 'composeMode', 'composeTo', 'composeSubject', 'composeBody', 'activeFolder'));
    }

    /**
     * Show reply form.
     */
    public function reply(Request $request, $uid)
    {
        $this->authorizeEmail();

        $activeFolder = 'inbox';

        try {
            $client = Client::account('default');
            $client->connect();
            $imapFolder = $client->getFolder('INBOX');

            $page = $request->get('page', 1);
            $perPage = 15;
            $messages = $imapFolder->query()->all()->setFetchOrder("desc")->paginate($perPage, $page, 'page');

            $selectedMessage = $imapFolder->query()->getMessageByUid($uid);
            
            if (!$selectedMessage) {
                return redirect()->route('admin.email.index')->with('error', 'Email tidak ditemukan.');
            }

            $composeMode = 'reply';
            $composeTo = $selectedMessage->getFrom()[0]->mail ?? '';
            $composeSubject = 'Re: ' . ($selectedMessage->getSubject() ?? '');
            
            $originalDate = $selectedMessage->getDate()[0] ?? null;
            $originalSender = $selectedMessage->getFrom()[0]->personal ?? $selectedMessage->getFrom()[0]->mail ?? 'Unknown';
            $originalBody = $selectedMessage->getTextBody() ?? strip_tags($selectedMessage->getHTMLBody() ?? '');
            
            $quotedLines = collect(explode("\n", $originalBody))->map(function($line) {
                return '> ' . $line;
            })->implode("\n");

            $composeBody = "\n\n---\nPada " . ($originalDate ? $originalDate->format('d M Y, H:i') : '') . ", {$originalSender} menulis:\n{$quotedLines}";

            return view('admin.email.index', compact('messages', 'selectedMessage', 'composeMode', 'composeTo', 'composeSubject', 'composeBody', 'activeFolder'));

        } catch (\Exception $e) {
            return redirect()->route('admin.email.index')->with('error', 'Gagal memuat email: ' . $e->getMessage());
        }
    }

    /**
     * Generate AI reply suggestion for an email.
     */
    public function generateAiReply(Request $request)
    {
        $this->authorizeEmail();

        $request->validate([
            'subject' => 'required|string',
            'sender' => 'required|string',
            'body' => 'required|string',
            'tone' => 'nullable|string|in:formal,friendly,brief',
        ]);

        $config = \App\Models\AiSetting::getConfig();

        if (!$config['enabled']) {
            return response()->json(['success' => false, 'error' => 'AI belum diaktifkan. Silakan atur di menu Pengaturan > AI.'], 403);
        }

        $subject = $request->input('subject');
        $sender = $request->input('sender');
        $body = $request->input('body');
        $tone = $request->input('tone', 'formal');

        $toneDesc = match($tone) {
            'friendly' => 'ramah dan bersahabat',
            'brief' => 'singkat dan to the point',
            default => 'formal dan profesional',
        };

        $emailPrompt = "Kamu adalah asisten email untuk Koperasi Karyawan SKF. Buatkan balasan email yang {$toneDesc} dalam Bahasa Indonesia.\n\n"
            . "ATURAN:\n"
            . "- Tulis HANYA isi balasan email saja, tanpa 'Subject:', 'Kepada:', header atau metadata\n"
            . "- Mulai langsung dengan salam (misalnya 'Selamat Pagi/Siang/Sore')\n"
            . "- Akhiri dengan salam penutup dan tanda tangan 'Admin Koperasi SKF'\n"
            . "- Gunakan nada {$toneDesc}\n"
            . "- Jangan gunakan format markdown\n\n"
            . "EMAIL YANG PERLU DIBALAS:\n"
            . "Dari: {$sender}\n"
            . "Subjek: {$subject}\n"
            . "Isi:\n{$body}\n\n"
            . "BALASAN:";

        $provider = $config['provider'];
        $model = $config['model'];
        $apiKey = $config['apiKey'];
        $url = $config['url'];

        try {
            if ($provider === 'gemini') {
                $response = \Illuminate\Support\Facades\Http::timeout(60)
                    ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
                        'contents' => [
                            ['parts' => [['text' => $emailPrompt]]]
                        ],
                        'generationConfig' => [
                            'temperature' => 0.7,
                            'maxOutputTokens' => 1024,
                        ]
                    ]);

                if (!$response->successful()) {
                    throw new \Exception($response->json('error.message', 'Gemini error'));
                }

                $reply = $response->json('candidates.0.content.parts.0.text', '');

            } elseif ($provider === 'openai') {
                $response = \Illuminate\Support\Facades\Http::timeout(60)
                    ->withHeaders(['Authorization' => "Bearer {$apiKey}"])
                    ->post('https://api.openai.com/v1/chat/completions', [
                        'model' => $model,
                        'messages' => [
                            ['role' => 'system', 'content' => 'Kamu adalah asisten email profesional Koperasi Karyawan SKF.'],
                            ['role' => 'user', 'content' => $emailPrompt]
                        ]
                    ]);

                if (!$response->successful()) {
                    throw new \Exception($response->json('error.message', 'OpenAI error'));
                }

                $reply = $response->json('choices.0.message.content', '');

            } elseif ($provider === 'ollama') {
                $response = \Illuminate\Support\Facades\Http::timeout(120)
                    ->post("{$url}/api/generate", [
                        'model' => $model,
                        'prompt' => $emailPrompt,
                        'stream' => false
                    ]);

                if (!$response->successful()) {
                    throw new \Exception('Ollama error: ' . $response->status());
                }

                $reply = $response->json('response', '');

            } else {
                $response = \Illuminate\Support\Facades\Http::timeout(60)
                    ->post("{$url}/generate", ['prompt' => $emailPrompt]);

                $data = $response->json();
                $reply = $data['response'] ?? $data['text'] ?? $data['output'] ?? '';
            }

            return response()->json([
                'success' => true,
                'reply' => trim($reply),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Gagal generate AI reply: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Send email via SMTP (with file attachments support).
     */
    public function send(Request $request)
    {
        $this->authorizeEmail();

        $request->validate([
            'to' => 'required|email',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'attachments.*' => 'nullable|file|max:10240', // max 10MB per file
        ], [
            'to.required' => 'Alamat email tujuan harus diisi.',
            'to.email' => 'Format alamat email tidak valid.',
            'subject.required' => 'Subjek harus diisi.',
            'body.required' => 'Isi pesan harus diisi.',
            'attachments.*.max' => 'Ukuran file maksimal 10MB.',
        ]);

        try {
            $to = $request->input('to');
            $cc = $request->input('cc');
            $subject = $request->input('subject');
            $body = $request->input('body');
            $files = $request->file('attachments');

            // Auto-append email signature
            $coopName = \App\Models\Setting::get('coop_name', 'Koperasi Karyawan SKF');
            $coopEmail = \App\Models\Setting::get('coop_email', '');
            $coopPhone = \App\Models\Setting::get('coop_phone', '');
            $coopAddress = \App\Models\Setting::get('coop_address', '');

            $signature = "\n\n--\n";
            $signature .= "Hormat kami,\n";
            $signature .= $coopName . "\n";
            if ($coopAddress) $signature .= "📍 " . $coopAddress . "\n";
            if ($coopPhone) $signature .= "📞 " . $coopPhone . "\n";
            if ($coopEmail) $signature .= "📧 " . $coopEmail . "\n";

            $bodyWithSignature = $body . $signature;

            Mail::raw($bodyWithSignature, function ($message) use ($to, $cc, $subject, $files) {
                $message->to($to);
                if ($cc) {
                    $ccList = array_map('trim', explode(',', $cc));
                    foreach ($ccList as $ccAddr) {
                        if (filter_var($ccAddr, FILTER_VALIDATE_EMAIL)) {
                            $message->cc($ccAddr);
                        }
                    }
                }
                $message->subject($subject);

                // Attach uploaded files
                if ($files) {
                    foreach ($files as $file) {
                        if ($file && $file->isValid()) {
                            $message->attach($file->getRealPath(), [
                                'as' => $file->getClientOriginalName(),
                                'mime' => $file->getMimeType(),
                            ]);
                        }
                    }
                }
            });

            return redirect()->route('admin.email.index')->with('success', 'Email berhasil dikirim ke ' . $to);

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal mengirim email: ' . $e->getMessage());
        }
    }

    /**
     * Download an attachment from a message.
     */
    public function downloadAttachment(Request $request, $uid, $attachmentIndex)
    {
        $this->authorizeEmail();

        try {
            $client = Client::account('default');
            $client->connect();

            $folder = $request->get('folder', 'inbox');
            $imapFolder = $this->resolveFolder($client, $folder);
            $message = $imapFolder->query()->getMessageByUid($uid);

            if (!$message) {
                return redirect()->back()->with('error', 'Email tidak ditemukan.');
            }

            $attachments = $message->getAttachments();
            $index = (int) $attachmentIndex;

            if ($index < 0 || $index >= $attachments->count()) {
                return redirect()->back()->with('error', 'Lampiran tidak ditemukan.');
            }

            $attachment = $attachments->get($index);
            $content = $attachment->content;
            $filename = $attachment->name ?: 'attachment_' . $index;
            $mimeType = $attachment->content_type ?: 'application/octet-stream';

            return response($content, 200, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Content-Length' => strlen($content),
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengunduh lampiran: ' . $e->getMessage());
        }
    }

    /**
     * Search members for email autocomplete.
     */
    public function searchMembers(Request $request)
    {
        $this->authorizeEmail();

        $q = $request->get('q', '');

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $members = \App\Models\Member::with('user')
            ->where('status', 'active')
            ->whereHas('user', function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            })
            ->limit(10)
            ->get()
            ->map(function ($member) {
                return [
                    'name' => $member->user->name ?? 'Unknown',
                    'email' => $member->user->email ?? '',
                    'department' => $member->department ?? '',
                    'member_id' => $member->member_id,
                ];
            })
            ->filter(fn($m) => !empty($m['email']))
            ->values();

        return response()->json($members);
    }

    /**
     * Get email templates.
     */
    public function getTemplates()
    {
        $this->authorizeEmail();

        $coopName = \App\Models\Setting::get('coop_name', 'Koperasi Karyawan SKF');

        $templates = [
            [
                'id' => 'reminder_cicilan',
                'name' => '📄 Pengingat Cicilan',
                'subject' => 'Pengingat Pembayaran Cicilan Pinjaman',
                'body' => "Yth. Bapak/Ibu,\n\nDengan hormat,\n\nKami mengingatkan bahwa cicilan pinjaman Anda pada {$coopName} telah jatuh tempo.\n\nMohon untuk segera melakukan pembayaran cicilan sesuai jadwal yang telah disepakati.\n\nApabila sudah melakukan pembayaran, mohon abaikan pemberitahuan ini.\n\nTerima kasih atas perhatian dan kerjasamanya."
            ],
            [
                'id' => 'undangan_rat',
                'name' => '📋 Undangan RAT',
                'subject' => 'Undangan Rapat Anggota Tahunan (RAT) ' . date('Y'),
                'body' => "Yth. Anggota {$coopName},\n\nDengan hormat,\n\nKami mengundang Bapak/Ibu untuk menghadiri Rapat Anggota Tahunan (RAT) {$coopName} Tahun Buku " . (date('Y') - 1) . ".\n\nHari/Tanggal : [Hari], [Tanggal]\nWaktu        : [Waktu] WIB\nTempat       : [Tempat]\n\nAgenda Rapat:\n1. Laporan Pertanggungjawaban Pengurus\n2. Laporan Keuangan\n3. Pembagian SHU\n4. Rencana Kerja Tahun " . date('Y') . "\n\nKehadiran Bapak/Ibu sangat kami harapkan.\n\nTerima kasih."
            ],
            [
                'id' => 'pengumuman',
                'name' => '📢 Pengumuman Umum',
                'subject' => 'Pengumuman - ' . $coopName,
                'body' => "Yth. Seluruh Anggota {$coopName},\n\nDengan hormat,\n\nMelalui email ini, kami sampaikan pengumuman sebagai berikut:\n\n[Isi pengumuman di sini]\n\nDemikian pengumuman ini kami sampaikan. Atas perhatiannya, kami ucapkan terima kasih."
            ],
            [
                'id' => 'konfirmasi_bayar',
                'name' => '🧾 Konfirmasi Pembayaran',
                'subject' => 'Konfirmasi Penerimaan Pembayaran',
                'body' => "Yth. Bapak/Ibu,\n\nDengan hormat,\n\nKami mengkonfirmasi bahwa pembayaran Anda telah kami terima dengan rincian sebagai berikut:\n\nJenis       : [Simpanan/Cicilan/Lainnya]\nJumlah      : Rp [Nominal]\nTanggal     : [Tanggal Bayar]\nNo. Referensi: [No. Ref]\n\nTerima kasih atas pembayaran Anda.\n\nApabila ada pertanyaan, silakan hubungi kami."
            ],
            [
                'id' => 'selamat_bergabung',
                'name' => '🎉 Selamat Bergabung',
                'subject' => 'Selamat Bergabung di ' . $coopName,
                'body' => "Yth. Bapak/Ibu,\n\nSelamat datang dan selamat bergabung sebagai anggota {$coopName}!\n\nBerikut informasi keanggotaan Anda:\n- Nomor Anggota: [Nomor]\n- Tanggal Bergabung: [Tanggal]\n\nSebagai anggota, Anda berhak atas:\n1. Layanan simpan pinjam\n2. Belanja di Mart Koperasi\n3. Pembagian SHU tahunan\n\nUntuk informasi lebih lanjut, silakan login di website atau hubungi kami.\n\nTerima kasih dan selamat berkoperasi!"
            ],
        ];

        return response()->json($templates);
    }
}
