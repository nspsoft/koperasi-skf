<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Webklex\IMAP\Facades\Client;

class AdminEmailController extends Controller
{
    /**
     * Display a listing of emails.
     */
    public function index(Request $request)
    {
        // Pastikan hanya role admin yang bisa mengakses
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            /** @var \Webklex\PHPIMAP\Client $client */
            $client = Client::account('default');
            $client->connect();
            
            $folder = $client->getFolder('INBOX');
            
            $page = $request->get('page', 1);
            $perPage = 15;
            
            // Fetch messages ordered by date descending
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
     * Display the specified email.
     */
    public function show(Request $request, $uid)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            /** @var \Webklex\PHPIMAP\Client $client */
            $client = Client::account('default');
            $client->connect();
            
            $folder = $client->getFolder('INBOX');
            
            // Fetch the list of messages for the middle pane
            $page = $request->get('page', 1);
            $perPage = 15;
            $messages = $folder->query()->all()->setFetchOrder("desc")->paginate($perPage, $page, 'page');

            // Fetch the specific selected message for the right pane
            $selectedMessage = $folder->query()->getMessageByUid($uid);
            
            if (!$selectedMessage) {
                return redirect()->route('admin.email.index')->with('error', 'Email tidak ditemukan.');
            }
            
            // Mark as read if it is unseen
            if(!$selectedMessage->hasFlag('seen')) {
                $selectedMessage->setFlag('seen');
            }
            
            // Render index view with both list and selected message
            return view('admin.email.index', compact('messages', 'selectedMessage'));
            
        } catch (\Exception $e) {
            return redirect()->route('admin.email.index')->with('error', 'Gagal memuat email: ' . $e->getMessage());
        }
    }
}
