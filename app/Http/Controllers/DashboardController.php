<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Saving;
use App\Models\Loan;
use App\Models\LoanPayment;
use App\Models\Announcement;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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

        $data = Cache::remember("dashboard_admin_{$currentYear}_v4", 60, function () use ($currentYear) {
            $stats = [
                'total_members' => Member::where('status', 'active')->count(),
                'total_savings' => Saving::where('transaction_type', 'deposit')->sum('amount') -
                                  Saving::where('transaction_type', 'withdrawal')->sum('amount'),
                'total_loans' => Loan::whereIn('status', ['active', 'approved'])->sum('amount'),
                'total_outstanding' => Loan::where('status', 'active')->sum('remaining_amount'),
                'pending_orders' => Transaction::where('type', 'online')->where('status', 'pending')->count(),
                'total_kredit' => Transaction::where('payment_method', 'kredit')->whereNotIn('status', ['completed', 'cancelled'])->sum('total_amount'),
                'kredit_member_count' => Transaction::where('payment_method', 'kredit')->whereNotIn('status', ['completed', 'cancelled'])->distinct('user_id')->count('user_id'),
            ];
            
            $topKreditDebtors = Transaction::where('payment_method', 'kredit')
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->select('user_id', DB::raw('SUM(total_amount) as total_tagihan'))
                ->groupBy('user_id')
                ->orderBy('total_tagihan', 'desc')
                ->with('user.member')
                ->take(5)
                ->get();
            
            $topProducts = TransactionItem::select('product_id', DB::raw('SUM(quantity) as total_qty'))
                ->with('product')
                ->groupBy('product_id')
                ->orderBy('total_qty', 'desc')
                ->take(5)
                ->get();

            $topCustomers = Transaction::select('user_id', DB::raw('SUM(total_amount) as total_spent'))
                ->where('status', 'completed')
                ->whereNotNull('user_id')
                ->groupBy('user_id')
                ->orderByDesc('total_spent')
                ->with('user.member')
                ->take(5)
                ->get();

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

            $monthlyRevenue = array_fill(0, 12, 0);
            $monthlyExpense = array_fill(0, 12, 0);
            $monthlyConsignmentExpense = array_fill(0, 12, 0);

            $monthlyMovements = \App\Models\JournalEntryLine::query()
                ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
                ->join('accounts', 'accounts.id', '=', 'journal_entry_lines.account_id')
                ->where('journal_entries.status', 'posted')
                ->whereYear('journal_entries.transaction_date', $currentYear)
                ->where(function ($q) {
                    $q->where('accounts.code', 'like', '4%')
                        ->orWhere('accounts.code', 'like', '5%');
                })
                ->selectRaw("MONTH(journal_entries.transaction_date) as month, CASE WHEN accounts.code LIKE '4%' THEN 'revenue' ELSE 'expense' END as account_group, CASE WHEN accounts.code = '5201' THEN 1 ELSE 0 END as is_consignment, SUM(journal_entry_lines.debit) as debit, SUM(journal_entry_lines.credit) as credit")
                ->groupBy('month', DB::raw("CASE WHEN accounts.code LIKE '4%' THEN 'revenue' ELSE 'expense' END"), DB::raw("CASE WHEN accounts.code = '5201' THEN 1 ELSE 0 END"))
                ->get();

            foreach ($monthlyMovements as $row) {
                $index = (int) $row->month - 1;
                if ($row->account_group === 'revenue') {
                    $monthlyRevenue[$index] = (float) $row->credit - (float) $row->debit;
                } else {
                    $expenseValue = (float) $row->debit - (float) $row->credit;
                    $monthlyExpense[$index] += $expenseValue;
                    if ((int) $row->is_consignment === 1) {
                        $monthlyConsignmentExpense[$index] += $expenseValue;
                    }
                }
            }

            $monthlyProfit = [];
            $monthlyOperationalProfit = [];
            $monthlyOperationalExpense = [];
            for ($i = 0; $i < 12; $i++) {
                $monthlyProfit[] = $monthlyRevenue[$i] - $monthlyExpense[$i];
                $monthlyOperationalProfit[] = ($monthlyRevenue[$i] - $monthlyExpense[$i]) + $monthlyConsignmentExpense[$i];
                $monthlyOperationalExpense[] = $monthlyExpense[$i] - $monthlyConsignmentExpense[$i];
            }

            $dailyRevenueProfit = [];
            for ($month = 1; $month <= 12; $month++) {
                $daysInMonth = Carbon::createFromDate((int) $currentYear, $month, 1)->daysInMonth;
                $dailyRevenueProfit[$month - 1] = [
                    'labels' => array_map(fn ($day) => (string) $day, range(1, $daysInMonth)),
                    'revenue' => array_fill(0, $daysInMonth, 0),
                    'expense' => array_fill(0, $daysInMonth, 0),
                    'consignment_expense' => array_fill(0, $daysInMonth, 0),
                    'profit' => array_fill(0, $daysInMonth, 0),
                    'operational_profit' => array_fill(0, $daysInMonth, 0),
                ];
            }

            $dailyMovements = \App\Models\JournalEntryLine::query()
                ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
                ->join('accounts', 'accounts.id', '=', 'journal_entry_lines.account_id')
                ->where('journal_entries.status', 'posted')
                ->whereYear('journal_entries.transaction_date', $currentYear)
                ->where(function ($q) {
                    $q->where('accounts.code', 'like', '4%')
                        ->orWhere('accounts.code', 'like', '5%');
                })
                ->selectRaw("MONTH(journal_entries.transaction_date) as month, DAY(journal_entries.transaction_date) as day, CASE WHEN accounts.code LIKE '4%' THEN 'revenue' ELSE 'expense' END as account_group, CASE WHEN accounts.code = '5201' THEN 1 ELSE 0 END as is_consignment, SUM(journal_entry_lines.debit) as debit, SUM(journal_entry_lines.credit) as credit")
                ->groupBy('month', 'day', DB::raw("CASE WHEN accounts.code LIKE '4%' THEN 'revenue' ELSE 'expense' END"), DB::raw("CASE WHEN accounts.code = '5201' THEN 1 ELSE 0 END"))
                ->get();

            foreach ($dailyMovements as $row) {
                $monthIndex = (int) $row->month - 1;
                $dayIndex = (int) $row->day - 1;
                if (! isset($dailyRevenueProfit[$monthIndex])) {
                    continue;
                }
                if ($row->account_group === 'revenue') {
                    $dailyRevenueProfit[$monthIndex]['revenue'][$dayIndex] = (float) $row->credit - (float) $row->debit;
                } else {
                    $expenseValue = (float) $row->debit - (float) $row->credit;
                    $dailyRevenueProfit[$monthIndex]['expense'][$dayIndex] += $expenseValue;
                    if ((int) $row->is_consignment === 1) {
                        $dailyRevenueProfit[$monthIndex]['consignment_expense'][$dayIndex] += $expenseValue;
                    }
                }
            }

            foreach ($dailyRevenueProfit as $monthIndex => $dataset) {
                foreach ($dataset['revenue'] as $dayIndex => $revenue) {
                    $dailyRevenueProfit[$monthIndex]['profit'][$dayIndex] = $revenue - $dataset['expense'][$dayIndex];
                    $dailyRevenueProfit[$monthIndex]['operational_profit'][$dayIndex] = ($revenue - $dataset['expense'][$dayIndex]) + $dataset['consignment_expense'][$dayIndex];
                }
                unset($dailyRevenueProfit[$monthIndex]['expense']);
                unset($dailyRevenueProfit[$monthIndex]['consignment_expense']);
            }
            
            $recentMembers = Member::with('user')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
            
            $pendingLoans = Loan::with('member.user')
                ->where('status', 'pending')
                ->orderBy('application_date', 'desc')
                ->take(5)
                ->get();
            
            $announcements = Announcement::active()
                ->take(3)
                ->get();
                
            $recentActivities = \App\Models\AuditLog::with('user')
                ->latest()
                ->take(10)
                ->get();
            
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
            
            $savingsChart = [];
            for ($i = 1; $i <= 12; $i++) {
                $savingsChart[] = $monthlySavings[$i] ?? 0;
            }
            
            $loanDistribution = Loan::select('loan_type', DB::raw('COUNT(*) as count'))
                ->where('status', 'active')
                ->groupBy('loan_type')
                ->get();
 
            return compact(
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
                'monthlyOperationalProfit',
                'monthlyOperationalExpense',
                'dailyRevenueProfit',
                'topCustomers'
            );
        });

        return view('dashboard.admin', $data);
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
                ->sum(\DB::raw('total_amount - paid_amount')),
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

        $creditUpcoming = \App\Models\Transaction::where('user_id', $user->id)
            ->where('payment_method', 'kredit')
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->orderBy('created_at', 'desc')
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
            'creditUpcoming',
            'announcements',
            'savingsChart'
        ));
    }
}
