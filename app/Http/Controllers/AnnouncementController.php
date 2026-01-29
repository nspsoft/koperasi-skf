<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $announcements = Announcement::latest()->paginate(10);
        return view('announcements.index', compact('announcements'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!auth()->user()->hasAdminAccess()) abort(403);

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $announcement = Announcement::create([
            'title' => $request->title,
            'content' => $request->content,
            'is_active' => true,
        ]);

        // Notify all Pengurus and Anggota about the new announcement
        $usersToNotify = \App\Models\User::whereIn('role', ['pengurus', 'member'])->where('is_active', true)->get();
        foreach ($usersToNotify as $user) {
            $user->notify(new \App\Notifications\NewAnnouncementNotification($announcement));
        }

        return redirect()->back()->with('success', 'Pengumuman berhasil ditambahkan.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Announcement $announcement)
    {
        if (!auth()->user()->hasAdminAccess()) abort(403);
        
        $announcement->delete();
        return redirect()->back()->with('success', 'Pengumuman dihapus.');
    }
}
