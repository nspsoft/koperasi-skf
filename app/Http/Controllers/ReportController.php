<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Saving;
use App\Models\Loan;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MembersExport;
use App\Exports\SavingsExport;
use App\Exports\LoansExport;

use App\Models\Account;
use App\Models\JournalEntryLine;
use App\Models\JournalEntry;
use App\Services\JournalService;

class ReportController extends Controller
{
    /**
     * General Ledger Report (Buku Besar)
     */
    public function generalLedger(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfDay();
        $accountId = $request->account_id;

        // Simplified list for the dropdown
        $excludedCodes = [
            '1201', '1301', '2101', '2102', '2103', '4101', '4102', '5201',
        ];

        $accountsGrouped = Account::whereNotIn('code', $excludedCodes)
            ->orderBy('code')
            ->get()
            ->groupBy('type');

        $accounts = Account::orderBy('code')->get();
        $lines = collect();
        $openingBalance = 0;

        if ($accountId) {
            $selectedAccount = Account::find($accountId);
            
            // Calculate Opening Balance
            $openingBalance = JournalService::getAccountBalance($selectedAccount->code, $startDate->copy()->subDay());

            // Get Transactions
            $lines = JournalEntryLine::with(['journalEntry', 'account'])
                ->where('account_id', $accountId)
                ->whereHas('journalEntry', function($q) use ($startDate, $endDate) {
                    $q->whereBetween('transaction_date', [$startDate, $endDate])
                      ->where('status', 'posted');
                })
                ->get()
                ->sortBy(function($line) {
                    return $line->journalEntry->transaction_date . '-' . $line->created_at;
                });
        }

        return view('reports.accounting.ledger', compact(
            'accounts', 'accountsGrouped', 'lines', 'startDate', 'endDate', 'accountId', 'openingBalance'
        ));
    }

    /**
     * Balance Sheet Report (Neraca)
     */
    public function balanceSheet(Request $request)
    {
        $date = $request->date ? Carbon::parse($request->date) : Carbon::now();

        // Assets (1xxx)
        $assets = Account::where('code', 'like', '1%')->orderBy('code')->get();
        
        // Liabilities (2xxx)
        $liabilities = Account::where('code', 'like', '2%')->orderBy('code')->get();
        
        // Equity (3xxx)
        $equities = Account::where('code', 'like', '3%')->orderBy('code')->get();

        // Collect all codes to fetch in one go
        $allCodes = array_merge(
            $assets->pluck('code')->toArray(),
            $liabilities->pluck('code')->toArray(),
            $equities->pluck('code')->toArray()
        );
        
        $balances = JournalService::getBatchAccountBalances($allCodes, $date);

        // Map Balances
        foreach ($assets as $account) {
            $account->current_balance = $balances[$account->code] ?? 0;
        }
        foreach ($liabilities as $account) {
            $account->current_balance = $balances[$account->code] ?? 0;
        }
        foreach ($equities as $account) {
            $account->current_balance = $balances[$account->code] ?? 0;
        }

        // Calculate Current Earnings (Laba Rugi Berjalan)
        $startOfYear = $date->copy()->startOfYear();
        $revenue = JournalService::getTotalRevenue($startOfYear, $date);
        $expense = JournalService::getTotalExpenses($startOfYear, $date);
        $currentEarnings = $revenue - $expense;

        $totalAssets = $assets->sum('current_balance');
        $totalLiabilities = $liabilities->sum('current_balance');
        $totalEquity = $equities->sum('current_balance') + $currentEarnings; 

        return view('reports.accounting.balance_sheet', compact(
            'assets', 'liabilities', 'equities', 'currentEarnings',
            'totalAssets', 'totalLiabilities', 'totalEquity', 'date'
        ));
    }

    /**
     * Income Statement Report (Laba Rugi)
     */
    public function incomeStatement(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfDay();

        // Revenue (4xxx)
        $revenues = Account::where('code', 'like', '4%')->orderBy('code')->get();
        // Expenses (5xxx)
        $expenses = Account::where('code', 'like', '5%')->orderBy('code')->get();

        $allAccountIds = array_merge($revenues->pluck('id')->toArray(), $expenses->pluck('id')->toArray());

        // Get movements for all relevant accounts in the period
        $movements = JournalEntryLine::whereIn('account_id', $allAccountIds)
            ->whereHas('journalEntry', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('transaction_date', [$startDate, $endDate])
                  ->where('status', 'posted');
            })
            ->select('account_id', DB::raw('SUM(debit) as total_debit'), DB::raw('SUM(credit) as total_credit'))
            ->groupBy('account_id')
            ->get()
            ->keyBy('account_id');

        // Map Balances
        foreach ($revenues as $account) {
            $mv = $movements->get($account->id);
            $account->period_balance = $mv ? ($mv->total_credit - $mv->total_debit) : 0;
        }

        foreach ($expenses as $account) {
            $mv = $movements->get($account->id);
            $account->period_balance = $mv ? ($mv->total_debit - $mv->total_credit) : 0;
        }

        $totalRevenue = $revenues->sum('period_balance');
        $totalExpense = $expenses->sum('period_balance');
        $netIncome = $totalRevenue - $totalExpense;

        return view('reports.accounting.income_statement', compact(
            'revenues', 'expenses', 'totalRevenue', 'totalExpense', 'netIncome',
            'startDate', 'endDate'
        ));
    }

    /**
     * Trial Balance Report (Neraca Saldo)
     */
    public function trialBalance(Request $request)
    {
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfDay();

        $accounts = Account::orderBy('code')->get();
        $accountCodes = $accounts->pluck('code')->toArray();
        $balances = JournalService::getBatchAccountBalances($accountCodes, $endDate);

        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($accounts as $account) {
            $balance = $balances[$account->code] ?? 0;
            
            $account->debit_balance = 0;
            $account->credit_balance = 0;

            if ($balance != 0) {
                if ($account->normal_balance == 'debit') {
                    if ($balance > 0) {
                        $account->debit_balance = $balance;
                    } else {
                        $account->credit_balance = abs($balance);
                    }
                } else { // credit normal
                    if ($balance > 0) {
                        $account->credit_balance = $balance;
                    } else {
                        $account->debit_balance = abs($balance);
                    }
                }
            }

            $totalDebit += $account->debit_balance;
            $totalCredit += $account->credit_balance;
        }

        return view('reports.accounting.trial_balance', compact('accounts', 'endDate', 'totalDebit', 'totalCredit'));
    }

    public function index()
    {
        return view('reports.index');
    }

    /**
     * Member reports.
     */
    public function members(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfYear();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfDay();

        // 1. Total Members Stats
        $totalMembers = Member::count();
        $activeMembers = Member::where('status', 'active')->count();
        $newMembers = Member::whereBetween('join_date', [$startDate, $endDate])->count();
        
        // 2. Department Distribution
        $byDepartment = Member::select('department', DB::raw('count(*) as total'))
            ->where('status', 'active')
            ->groupBy('department')
            ->orderByDesc('total')
            ->get();

        // 3. Gender Distribution
        $byGender = Member::select('gender', DB::raw('count(*) as total'))
            ->where('status', 'active')
            ->groupBy('gender')
            ->get();

        // 4. Recent Members List (for table)
        $members = Member::with('user')
            ->whereBetween('join_date', [$startDate, $endDate])
            ->latest('join_date')
            ->get();

        return view('reports.members', compact(
            'totalMembers', 'activeMembers', 'newMembers', 
            'byDepartment', 'byGender', 'members',
            'startDate', 'endDate'
        ));
    }

    /**
     * Savings reports.
     */
    public function savings(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfDay();
        $type = $request->type;

        // Base Query
        $query = Saving::whereBetween('transaction_date', [$startDate, $endDate]);
        if ($type) {
            $query->where('type', $type);
        }

        // Restriction for Member
        if (!auth()->user()->hasAdminAccess()) {
             $query->where('member_id', auth()->user()->member->id);
        }

        // 1. Summary Stats
        $totalDeposits = (clone $query)->where('transaction_type', 'deposit')->sum('amount');
        $totalWithdrawals = (clone $query)->where('transaction_type', 'withdrawal')->sum('amount');
        $netFlow = $totalDeposits - $totalWithdrawals;

        // 2. Transaction List
        $transactions = $query->with('member.user')
            ->latest('transaction_date')
            ->get();

        // 3. Balance per Type (All time)
        $balanceByType = Saving::select('type', 
                DB::raw("SUM(CASE WHEN transaction_type = 'deposit' THEN amount ELSE -amount END) as balance")
            )
            ->when(!auth()->user()->hasAdminAccess(), function($q) {
                $q->where('member_id', auth()->user()->member->id);
            })
            ->groupBy('type')
            ->get();

        return view('reports.savings', compact(
            'totalDeposits', 'totalWithdrawals', 'netFlow',
            'transactions', 'balanceByType',
            'startDate', 'endDate', 'type'
        ));
    }

    /**
     * Loans reports.
     */
    public function loans(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfYear();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfDay();
        $status = $request->status;

        // 1. Portfolio Summary (All Time Active)
        $totalActiveLoans = Loan::where('status', 'active')->count();
        $totalOutstanding = Loan::where('status', 'active')->sum('remaining_amount');
        $totalDisbursed = Loan::where('status', 'active')->sum('amount');
        
        // 2. Loan Filtering
        $query = Loan::with('member.user')
            ->whereBetween('application_date', [$startDate, $endDate]);
            
        if ($status) {
            $query->where('status', $status);
        }

        // Restriction for Member
        if (!auth()->user()->hasAdminAccess()) {
             $query->where('member_id', auth()->user()->member->id);
        }

        $loans = $query->latest('application_date')->get();

        // 3. Status Distribution
        $byStatus = Loan::select('status', DB::raw('count(*) as total'))
            ->whereBetween('application_date', [$startDate, $endDate])
            ->groupBy('status')
            ->get();

        return view('reports.loans', compact(
            'totalActiveLoans', 'totalOutstanding', 'totalDisbursed',
            'loans', 'byStatus',
            'startDate', 'endDate', 'status'
        ));
    }

    /**
     * Transactions/Sales reports.
     */
    public function transactions(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfDay();
        $type = $request->type; // pos, online, or all
        $status = $request->status;

        // Base Query
        $query = Transaction::whereBetween('created_at', [$startDate, $endDate]);
        
        if ($type) {
            $query->where('type', $type);
        }
        if ($status) {
            $query->where('status', $status);
        }

        // 1. Summary Stats
        $totalSales = (clone $query)->where('status', 'completed')->sum('total_amount');
        $totalTransactions = (clone $query)->where('status', 'completed')->count();
        $averageTransaction = $totalTransactions > 0 ? $totalSales / $totalTransactions : 0;
        
        // Pending/Processing Orders
        $pendingOrders = (clone $query)->whereIn('status', ['pending', 'processing'])->count();
        
        // 2. Payment Method Distribution
        $byPaymentMethod = Transaction::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->select('payment_method', DB::raw('count(*) as count'), DB::raw('sum(total_amount) as total'))
            ->groupBy('payment_method')
            ->get();

        // 3. Daily Sales (for chart)
        $dailySales = Transaction::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('sum(total_amount) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // 4. Transaction List
        $transactions = $query->with(['user', 'cashier', 'items.product'])
            ->latest()
            ->paginate(20);

        // 5. Top Selling Products
        $topProducts = DB::table('transaction_items')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->join('products', 'transaction_items.product_id', '=', 'products.id')
            ->whereBetween('transactions.created_at', [$startDate, $endDate])
            ->where('transactions.status', 'completed')
            ->select('products.name', 'products.code', 
                DB::raw('sum(transaction_items.quantity) as total_qty'),
                DB::raw('sum(transaction_items.subtotal) as total_sales'))
            ->groupBy('products.id', 'products.name', 'products.code')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        return view('reports.transactions', compact(
            'totalSales', 'totalTransactions', 'averageTransaction', 'pendingOrders',
            'byPaymentMethod', 'dailySales', 'transactions', 'topProducts',
            'startDate', 'endDate', 'type', 'status'
        ));
    }

    /**
     * Export Members Report to PDF
     */
    public function exportMembersPDF(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfYear();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfDay();

        $totalMembers = Member::count();
        $activeMembers = Member::where('status', 'active')->count();
        $newMembers = Member::whereBetween('join_date', [$startDate, $endDate])->count();
        
        $members = Member::with('user')
            ->whereBetween('join_date', [$startDate, $endDate])
            ->latest('join_date')
            ->get();

        $pdf = PDF::loadView('reports.pdf.members', compact(
            'totalMembers', 'activeMembers', 'newMembers', 'members', 'startDate', 'endDate'
        ));

        \App\Models\AuditLog::log(
            'export', 
            "Mengunduh Laporan Anggota (PDF)"
        );

        return $pdf->download('laporan-anggota-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Export Savings Report to PDF
     */
    public function exportSavingsPDF(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfDay();

        $query = Saving::whereBetween('transaction_date', [$startDate, $endDate]);

        $totalDeposits = (clone $query)->where('transaction_type', 'deposit')->sum('amount');
        $totalWithdrawals = (clone $query)->where('transaction_type', 'withdrawal')->sum('amount');
        $totalTransactions = (clone $query)->count();



        // Restriction for Member
        if (!auth()->user()->hasAdminAccess()) {
             $query->where('member_id', auth()->user()->member->id);
        }

        $savings = $query->with('member.user')->latest('transaction_date')->get();

        $pdf = PDF::loadView('reports.pdf.savings', compact(
            'totalDeposits', 'totalWithdrawals', 'totalTransactions', 'savings', 'startDate', 'endDate'
        ));

        \App\Models\AuditLog::log(
            'export', 
            "Mengunduh Laporan Simpanan (PDF)"
        );

        return $pdf->download('laporan-simpanan-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Export Loans Report to PDF
     */
    public function exportLoansPDF(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfYear();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfDay();

        $loans = Loan::with('member.user')
            ->whereBetween('application_date', [$startDate, $endDate])
            ->when(!auth()->user()->hasAdminAccess(), function($q) {
                $q->where('member_id', auth()->user()->member->id);
            })
            ->latest('application_date')
            ->get();

        $totalLoans = $loans->sum('amount');
        $totalPaid = $loans->sum('paid_amount');
        $totalRemaining = $loans->sum('remaining_amount');
        $loanCount = $loans->count();

        $pdf = PDF::loadView('reports.pdf.loans', compact(
            'totalLoans', 'totalPaid', 'totalRemaining', 'loanCount', 'loans', 'startDate', 'endDate'
        ));

        \App\Models\AuditLog::log(
            'export', 
            "Mengunduh Laporan Pinjaman (PDF)"
        );

        return $pdf->download('laporan-pinjaman-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Export Members to Excel
     */
    public function exportMembersExcel(Request $request)
    {
        \App\Models\AuditLog::log(
            'export', 
            "Mengunduh Laporan Anggota (Excel)"
        );
        return Excel::download(new MembersExport($request), 'laporan-anggota-' . now()->format('Y-m-d') . '.xlsx');
    }

    /**
     * Export Savings to Excel
     */
    public function exportSavingsExcel(Request $request)
    {
        \App\Models\AuditLog::log(
            'export', 
            "Mengunduh Laporan Simpanan (Excel)"
        );
        return Excel::download(new SavingsExport($request), 'laporan-simpanan-' . now()->format('Y-m-d') . '.xlsx');
    }

    /**
     * Export Loans to Excel
     */
    public function exportLoansExcel(Request $request)
    {
        \App\Models\AuditLog::log(
            'export', 
            "Mengunduh Laporan Pinjaman (Excel)"
        );
        return Excel::download(new LoansExport($request), 'laporan-pinjaman-' . now()->format('Y-m-d') . '.xlsx');
    }
}
