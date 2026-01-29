<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WithdrawalRequestController extends Controller
{
    /**
     * Display a listing of withdrawal requests.
     * Admin sees all, member sees only their own.
     */
    public function index(Request $request)
    {
        $query = \App\Models\WithdrawalRequest::with(['member.user', 'approver'])
            ->when(!auth()->user()->hasAdminAccess(), function($q) {
                $q->whereHas('member', function($mq) {
                    $mq->where('user_id', auth()->id());
                });
            })
            ->when($request->status, function($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->latest();

        $requests = $query->paginate(15);

        return view('withdrawals.index', compact('requests'));
    }

    /**
     * Show the form for creating a new withdrawal request.
     */
    public function create()
    {
        $member = auth()->user()->member;
        if (!$member) {
            return redirect()->back()->with('error', 'Anda tidak terdaftar sebagai anggota.');
        }

        // Get member's savings balance
        $savings = \App\Models\Saving::where('member_id', $member->id)
            ->selectRaw("type, SUM(CASE WHEN transaction_type = 'deposit' THEN amount ELSE -amount END) as balance")
            ->groupBy('type')
            ->get()
            ->pluck('balance', 'type');

        return view('withdrawals.create', compact('member', 'savings'));
    }

    /**
     * Store a newly created withdrawal request.
     */
    public function store(Request $request)
    {
        $request->validate([
            'saving_type' => 'required|in:sukarela,wajib,pokok',
            'amount' => 'required|numeric|min:10000',
            'bank_name' => 'nullable|string|max:100',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_account_name' => 'nullable|string|max:100',
            'reason' => 'nullable|string|max:500',
        ]);

        $member = auth()->user()->member;
        if (!$member) {
            return redirect()->back()->with('error', 'Anda tidak terdaftar sebagai anggota.');
        }

        // Check available balance
        $balance = \App\Models\Saving::where('member_id', $member->id)
            ->where('type', $request->saving_type)
            ->selectRaw("SUM(CASE WHEN transaction_type = 'deposit' THEN amount ELSE -amount END) as balance")
            ->value('balance') ?? 0;

        if ($request->amount > $balance) {
            return redirect()->back()->withInput()->with('error', 'Jumlah penarikan melebihi saldo tersedia (Rp ' . number_format($balance, 0, ',', '.') . ').');
        }

        \App\Models\WithdrawalRequest::create([
            'member_id' => $member->id,
            'amount' => $request->amount,
            'saving_type' => $request->saving_type,
            'bank_name' => $request->bank_name,
            'bank_account_number' => $request->bank_account_number,
            'bank_account_name' => $request->bank_account_name,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        // Log the action
        \App\Models\AuditLog::log('create', 'Membuat permintaan penarikan simpanan Rp ' . number_format($request->amount, 0, ',', '.'));

        return redirect()->route('withdrawals.index')->with('success', 'Permintaan penarikan berhasil dibuat. Menunggu persetujuan admin.');
    }

    /**
     * Approve a withdrawal request.
     */
    public function approve(Request $request, \App\Models\WithdrawalRequest $withdrawal)
    {
        if (!auth()->user()->hasAdminAccess()) abort(403);

        if ($withdrawal->status !== 'pending') {
            return redirect()->back()->with('error', 'Hanya permintaan dengan status pending yang dapat disetujui.');
        }

        $withdrawal->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'admin_notes' => $request->admin_notes,
        ]);

        \App\Models\AuditLog::log('approve', 'Menyetujui permintaan penarikan #' . $withdrawal->id . ' senilai Rp ' . number_format($withdrawal->amount, 0, ',', '.'), $withdrawal);

        return redirect()->back()->with('success', 'Permintaan penarikan disetujui. Silakan proses pencairan dana.');
    }

    /**
     * Reject a withdrawal request.
     */
    public function reject(Request $request, \App\Models\WithdrawalRequest $withdrawal)
    {
        if (!auth()->user()->hasAdminAccess()) abort(403);

        if ($withdrawal->status !== 'pending') {
            return redirect()->back()->with('error', 'Hanya permintaan dengan status pending yang dapat ditolak.');
        }

        $withdrawal->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'admin_notes' => $request->admin_notes,
        ]);

        \App\Models\AuditLog::log('reject', 'Menolak permintaan penarikan #' . $withdrawal->id . '. Alasan: ' . ($request->admin_notes ?? 'Tidak ada'), $withdrawal);

        return redirect()->back()->with('success', 'Permintaan penarikan ditolak.');
    }

    /**
     * Mark withdrawal as completed (disbursed).
     */
    public function complete(\App\Models\WithdrawalRequest $withdrawal)
    {
        if (!auth()->user()->hasAdminAccess()) abort(403);

        if ($withdrawal->status !== 'approved') {
            return redirect()->back()->with('error', 'Hanya permintaan yang sudah disetujui yang dapat diselesaikan.');
        }

        // Create withdrawal transaction in savings
        $saving = \App\Models\Saving::create([
            'member_id' => $withdrawal->member_id,
            'type' => $withdrawal->saving_type,
            'transaction_type' => 'withdrawal',
            'amount' => $withdrawal->amount,
            'transaction_date' => now(),
            'description' => 'Penarikan via Request #' . $withdrawal->id,
        ]);

        // Create journal entry for financial reports
        \App\Services\JournalService::journalSavingWithdrawal($saving);

        $withdrawal->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        \App\Models\AuditLog::log('complete', 'Menyelesaikan pencairan penarikan #' . $withdrawal->id . ' senilai Rp ' . number_format($withdrawal->amount, 0, ',', '.'), $withdrawal);

        return redirect()->back()->with('success', 'Pencairan dana berhasil. Transaksi penarikan telah dicatat.');
    }
}
