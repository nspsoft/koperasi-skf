<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Member;
use App\Models\Transaction;
use App\Models\Product;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        // Data Statistik untuk ditampilkan (Pameran Kinerja Koperasi)
        $stats = [
            'members' => Member::where('status', 'active')->count(),
            'products' => Product::count(),
            // Hitung total transaksi sukses jika tabel transactions ada
            'transactions' => \Schema::hasTable('transactions') ? Transaction::count() : 0,
        ];


        $teamMembers = \App\Models\TeamMember::orderBy('order')->get();
        $workPrograms = \App\Models\WorkProgram::orderBy('order')->get();
        return view('landing.index', compact('stats', 'teamMembers', 'workPrograms'));
    }
}
