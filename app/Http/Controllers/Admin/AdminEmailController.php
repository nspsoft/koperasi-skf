<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Webklex\IMAP\Facades\Client;

class AdminEmailController extends Controller
{
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
     * Display a listing of emails.
     */
    public function index(Request $request)
    {
        $this->authorizeEmail();

        try {
            /** @var \Webklex\PHPIMAP\Client $client */
            $client = Client::account('default');
            $client->connect();
            
            $folder = $client->getFolder('INBOX');
            
            $page = $request->get('page', 1);
            $perPage = 15;
            
            $messages = $folder->query()->all()->setFetchOrder("desc")->paginate($perPage, $page, 'page');
            
            return view('admin.email.index', compact('messages'));
            
        } catch (\Exception $e) {
            return view('admin.email.index', [
                'messages' => null,
                'imapError' => 'Gagal terhubung ke server email: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Display the specified email in split-pane view.
     */
    public function show(Request $request, $uid)
    {
        $this->authorizeEmail();

        try {
            /** @var \Webklex\PHPIMAP\Client $client */
            $client = Client::account('default');
            $client->connect();
            
            $folder = $client->getFolder('INBOX');
            
            $page = $request->get('page', 1);
            $perPage = 15;
            $messages = $folder->query()->all()->setFetchOrder("desc")->paginate($perPage, $page, 'page');

            $selectedMessage = $folder->query()->getMessageByUid($uid);
            
            if (!$selectedMessage) {
                return redirect()->route('admin.email.index')->with('error', 'Email tidak ditemukan.');
            }
            
            if(!$selectedMessage->hasFlag('seen')) {
                $selectedMessage->setFlag('seen');
            }
            
            return view('admin.email.index', compact('messages', 'selectedMessage'));
            
        } catch (\Exception $e) {
            return redirect()->route('admin.email.index')->with('error', 'Gagal memuat email: ' . $e->getMessage());
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

        try {
            $client = Client::account('default');
            $client->connect();
            $folder = $client->getFolder('INBOX');
            $page = $request->get('page', 1);
            $perPage = 15;
            $messages = $folder->query()->all()->setFetchOrder("desc")->paginate($perPage, $page, 'page');
        } catch (\Exception $e) {
            $messages = null;
        }

        return view('admin.email.index', compact('messages', 'composeMode', 'composeTo', 'composeSubject', 'composeBody'));
    }

    /**
     * Show reply form.
     */
    public function reply(Request $request, $uid)
    {
        $this->authorizeEmail();

        try {
            $client = Client::account('default');
            $client->connect();
            $folder = $client->getFolder('INBOX');

            $page = $request->get('page', 1);
            $perPage = 15;
            $messages = $folder->query()->all()->setFetchOrder("desc")->paginate($perPage, $page, 'page');

            $selectedMessage = $folder->query()->getMessageByUid($uid);
            
            if (!$selectedMessage) {
                return redirect()->route('admin.email.index')->with('error', 'Email tidak ditemukan.');
            }

            $composeMode = 'reply';
            $composeTo = $selectedMessage->getFrom()[0]->mail ?? '';
            $composeSubject = 'Re: ' . ($selectedMessage->getSubject() ?? '');
            
            // Build quoted reply body
            $originalDate = $selectedMessage->getDate()[0] ?? null;
            $originalSender = $selectedMessage->getFrom()[0]->personal ?? $selectedMessage->getFrom()[0]->mail ?? 'Unknown';
            $originalBody = $selectedMessage->getTextBody() ?? strip_tags($selectedMessage->getHTMLBody() ?? '');
            
            $quotedLines = collect(explode("\n", $originalBody))->map(function($line) {
                return '> ' . $line;
            })->implode("\n");

            $composeBody = "\n\n---\nPada " . ($originalDate ? $originalDate->format('d M Y, H:i') : '') . ", {$originalSender} menulis:\n{$quotedLines}";

            return view('admin.email.index', compact('messages', 'selectedMessage', 'composeMode', 'composeTo', 'composeSubject', 'composeBody'));

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
