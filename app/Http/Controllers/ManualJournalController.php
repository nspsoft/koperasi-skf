<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\JournalService;

class ManualJournalController extends Controller
{
    public function tutorial()
    {
        return view('journals.tutorial');
    }

    public function index(Request $request)
    {
        $startDate = $request->start_date ?? now()->startOfMonth()->toDateString();
        $endDate = $request->end_date ?? now()->toDateString();

        $journals = JournalEntry::whereBetween('transaction_date', [$startDate, $endDate])
            ->latest('transaction_date')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('journals.index', compact('journals', 'startDate', 'endDate'));
    }

    public function create()
    {
        // Exclude accounts that are managed by automated modules to prevent data inconsistency
        $excludedCodes = [
            '1201', // Piutang Anggota (Managed by Loans)
            '1301', // Persediaan (Managed by Mart/Purchases)
            '2101', // Simpanan Pokok
            '2102', // Simpanan Wajib
            '2103', // Simpanan Sukarela
            '4101', // Pendapatan Bunga (Managed by Loans)
            '4102', // Pendapatan Jual (Managed by Mart)
            '5201', // HPP (Managed by Mart)
        ];

        $accounts = Account::whereNotIn('code', $excludedCodes)
            ->orderBy('code')
            ->get()
            ->groupBy('type'); // Group by type for better UI grouping

        return view('journals.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'transaction_date' => 'required|date',
            'description' => 'required|string',
            'lines' => 'required|array|min:2',
            'lines.*.account_code' => 'required|exists:accounts,code',
            'lines.*.debit' => 'nullable|numeric|min:0',
            'lines.*.credit' => 'nullable|numeric|min:0',
            'lines.*.description' => 'nullable|string',
        ]);

        $totalDebit = 0;
        $totalCredit = 0;
        $entries = [];

        foreach ($request->lines as $line) {
            $debit = floatval($line['debit'] ?? 0);
            $credit = floatval($line['credit'] ?? 0);
            
            if ($debit == 0 && $credit == 0) continue;

            $totalDebit += $debit;
            $totalCredit += $credit;
            
            $entries[] = [
                'account_code' => $line['account_code'],
                'debit' => $debit,
                'credit' => $credit,
                'description' => $line['description'] ?? $request->description,
            ];
        }

        if (abs($totalDebit - $totalCredit) > 0.01) {
            return redirect()->back()
                ->withInput()
                ->with('error', "Jurnal tidak seimbang! Total Debit: Rp " . number_format($totalDebit) . ", Total Kredit: Rp " . number_format($totalCredit));
        }

        if (count($entries) < 2) {
            return redirect()->back()
                ->withInput()
                ->with('error', "Minimal harus ada 2 baris transaksi yang diinput.");
        }

        try {
            DB::beginTransaction();

            // We use a dummy model for reference since this is a manual entry
            // In a more robust system, we might have a ManualJournal model
            // But for now, we'll use a transaction with a special reference_type
            
            // Create a dummy record or just use the first journal entry as a temporary reference helper
            // Better: Let's modify JournalEntry to allow null reference or specific ManualJournal type
            
            $journal = JournalEntry::create([
                'journal_number' => JournalEntry::generateJournalNumber(),
                'reference_type' => 'Manual',
                'reference_id' => 0,
                'transaction_date' => $request->transaction_date,
                'description' => $request->description,
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'status' => 'posted',
                'created_by' => auth()->id(),
                'posted_by' => auth()->id(),
                'posted_at' => now(),
            ]);

            foreach ($entries as $entry) {
                $account = Account::where('code', $entry['account_code'])->first();
                JournalEntryLine::create([
                    'journal_entry_id' => $journal->id,
                    'account_id' => $account->id,
                    'debit' => $entry['debit'],
                    'credit' => $entry['credit'],
                    'description' => $entry['description']
                ]);
            }

            DB::commit();

            return redirect()->route('journals.index')
                ->with('success', 'Jurnal manual berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan jurnal: ' . $e->getMessage());
        }
    }

    public function show(JournalEntry $journal)
    {
        $journal->load('lines.account', 'creator');
        return view('journals.show', compact('journal'));
    }

    public function destroy(JournalEntry $journal)
    {
        \Illuminate\Support\Facades\Gate::authorize('delete-data');

        // Only allow deleting manual journals to prevent breaking automatic ones
        if ($journal->reference_type !== 'Manual') {
            return redirect()->back()->with('error', 'Jurnal otomatis tidak dapat dihapus secara manual.');
        }

        try {
            DB::beginTransaction();
            $journal->lines()->delete();
            $journal->delete();
            DB::commit();
            return redirect()->route('journals.index')->with('success', 'Jurnal berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus jurnal: ' . $e->getMessage());
        }
    }
}
