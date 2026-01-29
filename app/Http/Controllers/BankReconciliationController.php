<?php

namespace App\Http\Controllers;

use App\Models\BankTransaction;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Imports\BankStatementImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class BankReconciliationController extends Controller
{
    public function index(Request $request)
    {
        if (! auth()->user()->hasAdminAccess()) {
            abort(403);
        }
        $status = $request->query('status', 'pending');

        $bankTransactions = BankTransaction::query()
            ->when($status, function ($q) use ($status) {
                return $q->where('status', $status);
            })
            ->with(['journalEntry']) // Load linked journal
            ->orderBy('transaction_date', 'desc')
            ->paginate(20)
            ->withQueryString();

        // Count pending
        $pendingCount = BankTransaction::where('status', 'pending')->count();
        $reconciledCount = BankTransaction::where('status', 'reconciled')->count();

        // Get Potential Matches for pending transactions?
        // Maybe do this via AJAX or just in view loop if not too heavy. 
        // For now, load as is.

        return view('accounting.reconciliation.index', compact('bankTransactions', 'status', 'pendingCount', 'reconciledCount'));
    }

    public function import(Request $request)
    {
        if (! auth()->user()->hasAdminAccess()) {
            abort(403);
        }
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        try {
            Excel::import(new BankStatementImport, $request->file('file'));
            
            // Trigger Auto Match after import
            $matchCount = $this->runAutoMatch();

            return redirect()->route('reconciliation.index')
                ->with('success', 'Import berhasil! ' . $matchCount . ' transaksi otomatis dicocokkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }

    public function autoMatch()
    {
        if (! auth()->user()->hasAdminAccess()) {
            abort(403);
        }
        $count = $this->runAutoMatch();
        return back()->with('success', $count . ' transaksi berhasil dicocokkan otomatis.');
    }

    private function runAutoMatch()
    {
        $pending = BankTransaction::where('status', 'pending')->get();
        $matched = 0;

        foreach ($pending as $trx) {
            // Find Candidate Journal
            // Logic: 
            // Bank Credit (Money In) matches System Debit on Bank Account (1102)
            // Bank Debit (Money Out) matches System Credit on Bank Account (1102)
            
            $targetType = ($trx->type === 'credit') ? 'debit' : 'credit';
            
            // Find Journal Entry Line that:
            // 1. Is Account 1102 (Bank)
            // 2. Has same amount
            // 3. Has compatible type (Dr vs Cr)
            // 4. Date matches exactly (or narrow range)
            // 5. Belongs to a Journal Entry that is NOT yet linked to any bank transaction
            
            $candidate = JournalEntryLine::where('account_id', \App\Models\Account::where('code', '1102')->first()->id)
                ->where('type', $targetType)
                ->where('amount', $trx->amount)
                ->whereHas('journalEntry', function($q) use ($trx) {
                    $q->whereDate('transaction_date', $trx->transaction_date)
                      // Ensure this journal entry is not already taken
                      ->whereDoesntHave('bankTransaction'); 
                })
                ->with('journalEntry')
                ->first();

            if ($candidate) {
                // Link it
                $trx->update([
                    'journal_entry_id' => $candidate->journal_entry_id,
                    'status' => 'reconciled'
                ]);
                $matched++;
            }
        }
        return $matched;
    }

    public function match(Request $request, BankTransaction $bankTransaction)
    {
        if (! auth()->user()->hasAdminAccess()) {
            abort(403);
        }
        $request->validate(['journal_entry_id' => 'required|exists:journal_entries,id']);
        
        $bankTransaction->update([
            'journal_entry_id' => $request->journal_entry_id,
            'status' => 'reconciled'
        ]);

        return back()->with('success', 'Transaksi berhasil dicocokkan.');
    }

    public function createJournal(Request $request, BankTransaction $bankTransaction)
    {
        if (! auth()->user()->hasAdminAccess()) {
            abort(403);
        }
        // Quickly create journal for this bank transaction
        // e.g. for Admin Fee or Interest
        $request->validate([
            'description' => 'required',
            'contra_account_id' => 'required|exists:accounts,id', // The partner account (e.g. Expense: Admin Fee)
        ]);

        try {
            DB::beginTransaction();

            // Prepare Journal Data
            // Bank Side (1102)
            // If Bank Trx is Credit (In), Journal Bank is Debit.
            // If Bank Trx is Debit (Out), Journal Bank is Credit.
            
            $bankType = ($bankTransaction->type === 'credit') ? 'debit' : 'credit';
            $contraType = ($bankType === 'debit') ? 'credit' : 'debit';
            
            $journalData = [
                'date' => $bankTransaction->transaction_date,
                'description' => $request->description . ' (Reconciled: ' . $bankTransaction->description . ')',
                'reference_number' => $bankTransaction->reference_number ?? 'REC-' . $bankTransaction->id,
                'items' => [
                    [
                        'account_id' => \App\Models\Account::where('code', '1102')->first()->id, // Bank
                        'type' => $bankType,
                        'amount' => $bankTransaction->amount,
                        'description' => 'Bank Mutation'
                    ],
                    [
                        'account_id' => $request->contra_account_id,
                        'type' => $contraType,
                        'amount' => $bankTransaction->amount,
                        'description' => $request->description
                    ]
                ]
            ];

            // Create Journal via Service
            $journal = \App\Services\JournalService::createManualJournal($journalData, auth()->id());
            
            // Link it
            $bankTransaction->update([
                'journal_entry_id' => $journal->id,
                'status' => 'reconciled'
            ]);

            DB::commit();
            return back()->with('success', 'Jurnal berhasil dibuat dan dicocokkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membuat jurnal: ' . $e->getMessage());
        }
    }
    
    public function unmatch(BankTransaction $bankTransaction)
    {
        if (! auth()->user()->hasAdminAccess()) {
            abort(403);
        }
         $bankTransaction->update([
            'journal_entry_id' => null,
            'status' => 'pending'
        ]);
        return back()->with('success', 'Pencocokan dibatalkan.');
    }

    public function downloadTemplate()
    {
        return Excel::download(new \App\Exports\BankReconciliationTemplateExport, 'template_rekonsiliasi_bank.xlsx');
    }
}
