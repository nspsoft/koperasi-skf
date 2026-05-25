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

        try {
            /** @var \Webklex\PHPIMAP\Client $client */
            $client = Client::account('default');
            $client->connect();
            
            $imapFolder = $this->resolveFolder($client, $folder);
            
            $page = $request->get('page', 1);
            $perPage = 15;
            
            $messages = $imapFolder->query()->all()->setFetchOrder("desc")->paginate($perPage, $page, 'page');
            
            return view('admin.email.index', compact('messages', 'activeFolder'));
            
        } catch (\Exception $e) {
            return view('admin.email.index', [
                'messages' => null,
                'activeFolder' => $activeFolder,
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
     * Send email via SMTP.
     */
    public function send(Request $request)
    {
        $this->authorizeEmail();

        $request->validate([
            'to' => 'required|email',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
        ], [
            'to.required' => 'Alamat email tujuan harus diisi.',
            'to.email' => 'Format alamat email tidak valid.',
            'subject.required' => 'Subjek harus diisi.',
            'body.required' => 'Isi pesan harus diisi.',
        ]);

        try {
            $to = $request->input('to');
            $cc = $request->input('cc');
            $subject = $request->input('subject');
            $body = $request->input('body');

            Mail::raw($body, function ($message) use ($to, $cc, $subject) {
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
            });

            return redirect()->route('admin.email.index')->with('success', 'Email berhasil dikirim ke ' . $to);

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal mengirim email: ' . $e->getMessage());
        }
    }
}
