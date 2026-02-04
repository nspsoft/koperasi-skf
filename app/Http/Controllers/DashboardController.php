<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Saving;
use App\Models\Loan;
use App\Models\LoanPayment;
use App\Models\Announcement;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Admin/Pengurus Dashboard
        if ($user->hasAdminAccess()) {
            return $this->adminDashboard();
        }
        
        // Member Dashboard
        return $this->memberDashboard();
    }
    
    /**
     * Admin Dashboard with overall statistics
     */
    private function adminDashboard()
    {
        $currentYear = date('Y');

        // Statistics
        $stats = [
            'total_members' => Member::where('status', 'active')->count(),
            'total_savings' => Saving::where('transaction_type', 'deposit')->sum('amount') - 
                              Saving::where('transaction_type', 'withdrawal')->sum('amount'),
            'total_loans' => Loan::whereIn('status', ['active', 'approved'])->sum('amount'),
            'total_outstanding' => Loan::where('status', 'active')->sum('remaining_amount'),
            'pending_orders' => Transaction::where('type', 'online')->where('status', 'pending')->count(),
            // Kredit Mart Monitoring
            'total_kredit' => Transaction::where('payment_method', 'kredit')->whereNotIn('status', ['completed', 'cancelled'])->sum('total_amount'),
            'kredit_member_count' => Transaction::where('payment_method', 'kredit')->whereNotIn('status', ['completed', 'cancelled'])->distinct('user_id')->count('user_id'),
        ];
        
        // Top Kredit Mart Debtors
        $topKreditDebtors = Transaction::where('payment_method', 'kredit')
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->select('user_id', DB::raw('SUM(total_amount) as total_tagihan'))
            ->groupBy('user_id')
            ->orderBy('total_tagihan', 'desc')
            ->with('user.member')
            ->take(5)
            ->get();
        
        // Top Selling Products
        $topProducts = TransactionItem::select('product_id', DB::raw('SUM(quantity) as total_qty'))
            ->with('product')
            ->groupBy('product_id')
            ->orderBy('total_qty', 'desc')
            ->take(5)
            ->get();

        // --- NEW ANALYTICS ---

        // 1. Top 5 Customers (Sultan)
        $topCustomers = Transaction::select('user_id', DB::raw('SUM(total_amount) as total_spent'))
            ->where('status', 'completed')
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->orderByDesc('total_spent')
            ->with('user.member')
            ->take(5)
            ->get();

        // 2. Sales Channel Distribution (Offline vs Online)
        $salesChannel = Transaction::select('type', DB::raw('COUNT(*) as count'))
            ->where('status', 'completed')
            ->whereYear('created_at', $currentYear)
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();
        
        $salesChannelData = [
            $salesChannel['offline'] ?? 0,
            $salesChannel['online'] ?? 0
        ];

        // 3. Revenue & Profit Chart (Monthly)
        // Revenue = Class 4 (Credit - Debit)
        // Expenses = Class 5 (Debit - Credit)
        // Profit = Revenue - Expenses
        
        $monthlyRevenue = [];
        $monthlyProfit = [];

        for ($m = 1; $m <= 12; $m++) {
            // Revenue (Class 4)
            $revCredit = \App\Models\JournalEntryLine::whereHas('account', function($q) {
                    $q->where('code', 'like', '4%');
                })
                ->whereHas('journalEntry', function($q) use ($currentYear, $m) {
                    $q->whereYear('transaction_date', $currentYear)
                      ->whereMonth('transaction_date', $m);
                })->sum('credit');
            
            $revDebit = \App\Models\JournalEntryLine::whereHas('account', function($q) {
                    $q->where('code', 'like', '4%');
                })
                ->whereHas('journalEntry', function($q) use ($currentYear, $m) {
                    $q->whereYear('transaction_date', $currentYear)
                      ->whereMonth('transaction_date', $m);
                })->sum('debit');
            
            $revenue = $revCredit - $revDebit;

            // Expenses (Class 5)
            $expDebit = \App\Models\JournalEntryLine::whereHas('account', function($q) {
                    $q->where('code', 'like', '5%');
                })
                ->whereHas('journalEntry', function($q) use ($currentYear, $m) {
                    $q->whereYear('transaction_date', $currentYear)
                      ->whereMonth('transaction_date', $m);
                })->sum('debit');

            $expCredit = \App\Models\JournalEntryLine::whereHas('account', function($q) {
                    $q->where('code', 'like', '5%');
                })
                ->whereHas('journalEntry', function($q) use ($currentYear, $m) {
                    $q->whereYear('transaction_date', $currentYear)
                      ->whereMonth('transaction_date', $m);
                })->sum('credit');

            $expense = $expDebit - $expCredit;
            
            $monthlyRevenue[] = $revenue;
            $monthlyProfit[] = $revenue - $expense;
        }

        // ---------------------
        
        // Recent Members (5 newest)
        $recentMembers = Member::with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Pending Loans
        $pendingLoans = Loan::with('member.user')
            ->where('status', 'pending')
            ->orderBy('application_date', 'desc')
            ->take(5)
            ->get();
        
        // Active Announcements
        $announcements = Announcement::active()
            ->take(3)
            ->get();
            
        // Recent Activities
        $recentActivities = \App\Models\AuditLog::with('user')
            ->latest()
            ->take(10)
            ->get();
        
        // Chart Data - Monthly Savings (MySQL Compatible)
        $monthlySavings = Saving::select(
                DB::raw("MONTH(transaction_date) as month"),
                DB::raw('SUM(CASE WHEN transaction_type = "deposit" THEN amount ELSE -amount END) as total')
            )
            ->whereYear('transaction_date', $currentYear)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('total', 'month')
            ->toArray();
        
        // Fill missing months with 0
        $savingsChart = [];
        for ($i = 1; $i <= 12; $i++) {
            $savingsChart[] = $monthlySavings[$i] ?? 0;
        }
        
        // Chart Data - Loan Types Distribution
        $loanDistribution = Loan::select('loan_type', DB::raw('COUNT(*) as count'))
            ->where('status', 'active')
            ->groupBy('loan_type')
            ->get();
        
        return view('dashboard.admin', compact(
            'stats',
            'recentMembers',
            'pendingLoans',
            'announcements',
            'savingsChart',
            'loanDistribution',
            'topProducts',
            'topKreditDebtors',
            'recentActivities',
            'salesChannelData',
            'monthlyRevenue',
            'monthlyProfit',
            'topCustomers'
        ));
    }
    
    /**
     * Member Dashboard with personal information
     */
    private function memberDashboard()
    {
        $user = auth()->user();
        $member = $user->member;
        
        if (!$member) {
            return view('dashboard.no-member');
        }
        
        // Personal Statistics
        $stats = [
            'total_savings' => $member->total_simpanan ?? 0,
            'simpanan_pokok' => $member->total_simpanan_pokok ?? 0,
            'simpanan_wajib' => $member->total_simpanan_wajib ?? 0,
            'simpanan_sukarela' => $member->total_simpanan_sukarela ?? 0,
            'active_loans' => $member->total_pinjaman_aktif ?? 0,
            'credit_limit' => $member->credit_limit ?? 500000,
            'credit_used' => \App\Models\Transaction::where('user_id', $user->id)
                ->where('payment_method', 'kredit')
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->sum('total_amount'),
        ];

        $stats['credit_available'] = max(0, $stats['credit_limit'] - $stats['credit_used']);
        
        // Recent Transactions
        $recentSavings = $member->savings()
            ->orderBy('transaction_date', 'desc')
            ->take(5)
            ->get();
        
        // Active Loans
        $activeLoans = $member->loans()
            ->whereIn('status', ['active', 'approved', 'pending'])
            ->with('payments')
            ->orderBy('application_date', 'desc')
            ->get();
        
        // Upcoming Payments
        $upcomingPayments = LoanPayment::whereHas('loan', function($query) use ($member) {
                $query->where('member_id', $member->id)
                      ->where('status', 'active');
            })
            ->where('status', 'pending')
            ->orderBy('due_date', 'asc')
            ->take(5)
            ->get();
        
        // Active Announcements
        $announcements = Announcement::active()
            ->take(3)
            ->get();

        // Chart Data - Monthly Savings (Personal)
        $monthlySavings = $member->savings()
            ->select(
                DB::raw("MONTH(transaction_date) as month"),
                DB::raw('SUM(CASE WHEN transaction_type = "deposit" THEN amount ELSE -amount END) as total')
            )
            ->whereYear('transaction_date', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('total', 'month')
            ->toArray();
        
        // Fill missing months with 0
        $savingsChart = [];
        for ($i = 1; $i <= 12; $i++) {
            $savingsChart[] = $monthlySavings[$i] ?? 0;
        }
        
        return view('dashboard.member', compact(
            'member',
            'stats',
            'recentSavings',
            'activeLoans',
            'upcomingPayments',
            'announcements',
            'savingsChart'
        ));
    }
}
