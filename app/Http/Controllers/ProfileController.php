<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $performanceHistories = \App\Models\PerformanceHistory::where('user_id', $request->user()->id)
            ->with('admin')
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();

        return view('profile.edit', [
            'user' => $request->user(),
            'performanceHistories' => $performanceHistories,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        if ($request->user()->member) {
            $memberData = [
                'gender' => $request->gender,
                'birth_date' => $request->birth_date,
                'address' => $request->address,
                'phone' => $request->phone,
            ];

            if ($request->hasFile('photo')) {
                // Delete old photo
                if ($request->user()->member->photo && \Storage::disk('public')->exists($request->user()->member->photo)) {
                    \Storage::disk('public')->delete($request->user()->member->photo);
                }
                $memberData['photo'] = $request->file('photo')->store('members/photos', 'public');
            }

            $request->user()->member->update($memberData);
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Required fields for profile completion
     */
    protected array $requiredFields = [
        'photo' => 'Foto Profil',
        'gender' => 'Jenis Kelamin',
        'birth_date' => 'Tanggal Lahir',
        'id_card_number' => 'Nomor KTP',
        'address' => 'Alamat',
        'employee_id' => 'NIK Karyawan',
        'department' => 'Departemen',
        'position' => 'Jabatan',
    ];

    /**
     * Show profile completion form
     */
    public function showCompleteForm(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        $member = $user->member;

        // If no member record, redirect to dashboard
        if (!$member) {
            return redirect()->route('dashboard');
        }

        // Get missing fields
        $missingFields = [];
        foreach ($this->requiredFields as $field => $label) {
            if (empty($member->{$field})) {
                $missingFields[$field] = $label;
            }
        }

        return view('profile.complete', [
            'user' => $user,
            'member' => $member,
            'missingFields' => $missingFields,
            'allFields' => $this->requiredFields,
            'departments' => \App\Models\Department::active()->orderBy('name')->get(),
            'positions' => \App\Models\Position::active()->orderBy('name')->get(),
        ]);
    }

    /**
     * Handle profile completion form submission
     */
    public function updateComplete(Request $request): RedirectResponse
    {
        $user = $request->user();
        $member = $user->member;

        // Validate required fields
        $rules = [
            'employee_id' => 'required|string|max:50',
            'department' => 'required|string|max:100',
            'position' => 'required|string|max:100',
            'gender' => 'required|in:male,female',
            'birth_date' => 'required|date|before:today',
            'id_card_number' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'terms' => 'accepted',
        ];

        // Photo is required only if not already set
        if (empty($member->photo)) {
            $rules['photo'] = 'required|image|mimes:jpeg,jpg,png|max:5120';
        } else {
            $rules['photo'] = 'nullable|image|mimes:jpeg,jpg,png|max:5120';
        }

        $validated = $request->validate($rules, [
            'employee_id.required' => 'NIK Karyawan wajib diisi',
            'department.required' => 'Departemen wajib diisi',
            'position.required' => 'Jabatan wajib diisi',
            'gender.required' => 'Jenis kelamin wajib dipilih',
            'birth_date.required' => 'Tanggal lahir wajib diisi',
            'birth_date.before' => 'Tanggal lahir harus sebelum hari ini',
            'id_card_number.required' => 'Nomor KTP wajib diisi',
            'address.required' => 'Alamat wajib diisi',
            'photo.required' => 'Foto profil wajib diunggah',
            'photo.image' => 'File harus berupa gambar',
            'photo.max' => 'Ukuran foto maksimal 5MB (Disarankan < 2MB agar cepat)',
            'terms.accepted' => 'Anda harus menyetujui Peraturan ANGGOTA KOPERASI (AD-ART) untuk melanjutkan',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($member->photo && \Storage::disk('public')->exists($member->photo)) {
                \Storage::disk('public')->delete($member->photo);
            }
            $validated['photo'] = $request->file('photo')->store('members/photos', 'public');
        }

        // Update member
        $member->update($validated);

        return redirect()->route('dashboard')
            ->with('success', 'Profil berhasil dilengkapi! Selamat datang di sistem.');
    }
}
