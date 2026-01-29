<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Member;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Carbon\Carbon;

class RegisteredUserController extends Controller
{

    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $departments = \App\Models\Department::active()->orderBy('name')->get();
        $positions = \App\Models\Position::active()->orderBy('name')->get();

        return view('auth.register', compact('departments', 'positions'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'employee_id' => ['required', 'string', 'max:20'], // NIK Karyawan
            'department' => ['required', 'string', 'max:100'],
            'position' => ['required', 'string', 'max:100'],
            'id_card_number' => ['nullable', 'string', 'max:20'], // NIK KTP
            'birth_date' => ['required', 'date'],
            'gender' => ['required', 'in:male,female'],
            'address' => ['required', 'string', 'max:500'],
            'photo' => ['nullable', 'image', 'max:5120'], // Max 5MB
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'terms' => ['accepted'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'member', // Default role
            'is_active' => false, // Default inactive
        ]);

        // Handle photo upload
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('member-photos', 'public');
        }

        // Generate temporary Member ID
        // Format: REG-YYYYMMDD-UserID
        $tempMemberId = 'REG-' . Carbon::now()->format('Ymd') . '-' . str_pad($user->id, 4, '0', STR_PAD_LEFT);

        // Create Inactive Member Record
        Member::create([
            'user_id' => $user->id,
            'member_id' => $tempMemberId,
            'employee_id' => $request->employee_id,
            'department' => $request->department,
            'position' => $request->position,
            'id_card_number' => $request->id_card_number,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
            'address' => $request->address,
            'photo' => $photoPath,
            'status' => 'inactive', // Menunggu persetujuan admin
            'join_date' => Carbon::now(),
        ]);

        event(new Registered($user));

        // Auth::login($user); // Disable auto login

        return redirect()->route('login')->with('status', 'Registrasi berhasil! Mohon tunggu persetujuan Admin untuk mengaktifkan akun Anda.');
    }
}
