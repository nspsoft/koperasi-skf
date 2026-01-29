<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function index()
    {
        $assets_count = \App\Models\Asset::where('status', 'active')->count();
        $assets_value = \App\Models\Asset::where('status', 'active')->sum('current_value');
        $upcoming_meetings = \App\Models\Meeting::where('scheduled_at', '>=', now())
            ->where('status', 'scheduled')
            ->orderBy('scheduled_at')
            ->take(5)
            ->get();
        $active_profiles = \App\Models\OrganizationProfile::where('status', 'active')->with('user')->get();

        return view('organization.index', compact('assets_count', 'assets_value', 'upcoming_meetings', 'active_profiles'));
    }

    // --- ASSETS ---
    public function assets()
    {
        $assets = \App\Models\Asset::orderBy('purchase_date', 'desc')->paginate(15);
        return view('organization.assets.index', compact('assets'));
    }

    public function storeAsset(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'code' => 'required|string|unique:assets,code',
            'category' => 'required|string',
            'purchase_date' => 'required|date',
            'purchase_price' => 'required|numeric',
            'condition' => 'required|string',
        ]);

        $validated['current_value'] = $validated['purchase_price'];
        
        \App\Models\Asset::create($validated);
        
        return redirect()->back()->with('success', 'Aset berhasil didaftarkan');
    }

    public function updateAsset(Request $request, \App\Models\Asset $asset)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'code' => 'required|string|unique:assets,code,' . $asset->id,
            'category' => 'required|string',
            'purchase_date' => 'required|date',
            'current_value' => 'required|numeric',
            'condition' => 'required|string',
            'status' => 'required|string',
        ]);

        $asset->update($validated);
        return redirect()->back()->with('success', 'Aset berhasil diupdate');
    }

    public function destroyAsset(\App\Models\Asset $asset)
    {
        $asset->delete();
        return redirect()->back()->with('success', 'Aset berhasil dihapus');
    }

    // --- MEETINGS ---
    public function meetings()
    {
        $meetings = \App\Models\Meeting::orderBy('scheduled_at', 'desc')->paginate(10);
        return view('organization.meetings.index', compact('meetings'));
    }

    public function storeMeeting(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'type' => 'required|string',
            'scheduled_at' => 'required|date',
            'location' => 'nullable|string',
            'agenda' => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['status'] = 'scheduled';

        \App\Models\Meeting::create($validated);

        return redirect()->back()->with('success', 'Rapat berhasil dijadwalkan');
    }

    public function updateMeeting(Request $request, \App\Models\Meeting $meeting)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'type' => 'required|string',
            'scheduled_at' => 'required|date',
            'location' => 'nullable|string',
            'agenda' => 'nullable|string',
            'notes' => 'nullable|string',
            'status' => 'required|string',
        ]);

        $meeting->update($validated);
        return redirect()->back()->with('success', 'Data rapat berhasil diupdate');
    }

    public function destroyMeeting(\App\Models\Meeting $meeting)
    {
        $meeting->delete();
        return redirect()->back()->with('success', 'Data rapat berhasil dihapus');
    }

    // --- PROFILES ---
    public function profiles()
    {
        $profiles = \App\Models\OrganizationProfile::with('user.member')
            ->orderBy('position')
            ->paginate(20);
        $users = \App\Models\User::all();
        
        return view('organization.profiles.index', compact('profiles', 'users'));
    }

    public function storeProfile(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'position' => 'required|string',
            'department' => 'nullable|string',
            'start_date' => 'required|date',
            'period' => 'required|string',
        ]);

        $validated['status'] = 'active';

        \App\Models\OrganizationProfile::create($validated);

        return redirect()->back()->with('success', 'Pengurus berhasil ditambahkan');
    }

    public function updateProfile(Request $request, \App\Models\OrganizationProfile $profile)
    {
        $validated = $request->validate([
            'position' => 'required|string',
            'department' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'period' => 'required|string',
            'status' => 'required|string',
        ]);

        $profile->update($validated);
        return redirect()->back()->with('success', 'Data pengurus berhasil diupdate');
    }

    public function destroyProfile(\App\Models\OrganizationProfile $profile)
    {
        $profile->delete();
        return redirect()->back()->with('success', 'Data pengurus berhasil dihapus');
    }
}
