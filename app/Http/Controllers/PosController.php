<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\JournalEntry;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Carbon\Carbon;


class PosController extends Controller
{
    public function index()
    {
        // Load all products for client-side search (assuming reasonable dataset)
        // For larger datasets, implement server-side search
        $products = Product::with('category')->where('is_active', true)->where('stock', '>', 0)->get();
        $qrisImage = \App\Models\Setting::where('key', 'payment_qris_image')->value('value');
        
        return view('commerce.pos.index', compact('products', 'qrisImage'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
            'payment_method' => 'required|string',
            'paid_amount' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
        ]);

        try {
            return \DB::transaction(function () use ($request) {
                // Calculate Totals
                $total_amount = 0;
                $items_data = [];

                foreach ($request->items as $item) {
                    $product = Product::lockForUpdate()->find($item['id']);
                    
                    if ($product->stock < $item['qty']) {
                        throw new \Exception("Stok {$product->name} tidak mencukupi. Tersisa: {$product->stock}");
                    }

                    $subtotal = $product->price * $item['qty'];
                    $total_amount += $subtotal;
                    
                    $items_data[] = [
                        'product' => $product,
                        'qty' => $item['qty'],
                        'price' => $product->price,
                        'subtotal' => $subtotal
                    ];
                }

                $discount = floatval($request->discount ?? 0);
                $total_amount = max(0, $total_amount - $discount);

                $paid_amount = $request->paid_amount;
                $change_amount = $paid_amount - $total_amount;

                // For cash payment, validate paid amount
                if ($request->payment_method === 'cash' && $change_amount < 0) {
                    throw new \Exception("Uang pembayaran kurang.");
                }

                // For kredit/saldo, require member
                if (in_array($request->payment_method, ['kredit', 'saldo']) && !$request->member_id) {
                    throw new \Exception("Pembayaran kredit/saldo memerlukan member.");
                }

                $member = null;
                if ($request->member_id) {
                    $member = \App\Models\Member::find($request->member_id);
                    if (!$member && in_array($request->payment_method, ['kredit', 'saldo'])) {
                         throw new \Exception("Data member tidak ditemukan.");
                    }
                }

                // Verify Balance for Saldo Payment
                if ($request->payment_method === 'saldo' && $member) {
                    $balance = \App\Models\Saving::where('member_id', $member->id)
                        ->where('type', 'sukarela')
                        ->sum('amount');

                    if ($balance < $total_amount) {
                         throw new \Exception('Saldo Simpanan Sukarela tidak mencukupi. Saldo: Rp ' . number_format($balance, 0, ',', '.'));
                    }
                }

                // Determine transaction status based on payment method
                $status = $request->payment_method === 'kredit' ? 'credit' : 'completed';
                
                // For kredit/saldo, paid amount handling
                if ($request->payment_method === 'kredit') {
                    $paid_amount = 0;
                    $change_amount = 0;
                } elseif ($request->payment_method === 'saldo') {
                    $paid_amount = $total_amount;
                    $change_amount = 0;
                }

                // Create Transaction
                $transaction = Transaction::create([
                    'invoice_number' => 'TRX-' . date('Ymd') . '-' . strtoupper(\Str::random(4)),
                    'user_id' => $member ? $member->user_id : null,
                    'type' => 'offline',
                    'status' => $status,
                    'cashier_id' => auth()->id(),
                    'payment_method' => $request->payment_method,
                    'total_amount' => $total_amount,
                    'paid_amount' => $paid_amount,
                    'change_amount' => $change_amount,
                    'notes' => $discount > 0 ? "[DISKON MANUAL: Rp " . number_format($discount, 0, ',', '.') . "]" : null,
                ]);

                // Create Items & Deduct Stock
                foreach ($items_data as $data) {
                    TransactionItem::create([
                        'transaction_id' => $transaction->id,
                        'product_id' => $data['product']->id,
                        'quantity' => $data['qty'],
                        'price' => $data['price'],
                        'subtotal' => $data['subtotal']
                    ]);

                    $data['product']->decrement('stock', $data['qty']);
                }

                // Deduct Balance if Saldo
                if ($request->payment_method === 'saldo' && $member) {
                     $saving = \App\Models\Saving::create([
                        'member_id' => $member->id,
                        'type' => 'sukarela',
                        'transaction_type' => 'withdrawal',
                        'amount' => $total_amount, // Positive absolute value for consistency
                        'transaction_date' => now(),
                        'description' => 'Pembayaran Belanja: ' . $transaction->invoice_number,
                        'created_by' => auth()->id(),
                    ]);

                    // Auto-journal Withdrawal
                    \App\Services\JournalService::journalSavingWithdrawal($saving);
                }

                // Auto-journal Sale (reload items for COGS calculation)
                $transaction->load('items.product');
                \App\Services\JournalService::journalSale($transaction);

                // Award Points for member transactions
                if ($member && in_array($status, ['paid', 'completed', 'credit'])) { // 'completed' is used for paid POS
                    $earnRate = \App\Models\Setting::get('point_earn_rate', 10000);
                    $earnedPoints = floor($total_amount / $earnRate);
                    if ($earnedPoints > 0) {
                        $member->increment('points', $earnedPoints);

                        // Log to PerformanceHistory
                        \App\Models\PerformanceHistory::create([
                            'user_id' => $member->user_id,
                            'points_change' => $earnedPoints,
                            'type' => 'loyalty',
                            'reason' => 'Poin Belanja (Invoice: ' . $transaction->invoice_number . ')',
                        ]);
                    }
                }

                return response()->json([
                    'success' => true, 
                    'message' => $request->payment_method === 'kredit' ? 'Transaksi kredit berhasil!' : 'Transaksi berhasil!',
                    'invoice' => $transaction->invoice_number,
                    'transaction_id' => $transaction->id,
                    'change' => $change_amount,
                    'is_credit' => $request->payment_method === 'kredit',
                    'points_earned' => $earnedPoints ?? 0
                ]);
            });

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function history(Request $request)
    {
        $query = Transaction::with(['items.product', 'cashier', 'user', 'journalEntry'])->latest();

        // Filter by date range
        if ($request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Filter by type
        if ($request->type) {
            $query->where('type', $request->type);
        }

        // Filter by cashier
        if ($request->cashier_id) {
            $query->where('cashier_id', $request->cashier_id);
        }

        // Filter by payment method
        if ($request->payment_method) {
            $query->where('payment_method', $request->payment_method);
        }

        $transactions = $query->paginate(20);

        // Get list of cashiers for filter (staff with roles admin, pengurus, manager_toko, kasir)
        $cashiers = User::whereIn('role', ['admin', 'pengurus', 'manager_toko', 'kasir'])
            ->orWhereHas('roleModel', function($q) {
                $q->whereIn('name', ['admin', 'pengurus', 'manager_toko', 'kasir']);
            })
            ->orderBy('name')
            ->get();

        // Get summary (Today vs Yesterday)
        $todaySales = Transaction::whereDate('created_at', now())->whereNot('status', 'cancelled')->sum('total_amount');
        $todayCount = Transaction::whereDate('created_at', now())->whereNot('status', 'cancelled')->count();
        
        $yesterdaySales = Transaction::whereDate('created_at', now()->subDay())->whereNot('status', 'cancelled')->sum('total_amount');
        $yesterdayCount = Transaction::whereDate('created_at', now()->subDay())->whereNot('status', 'cancelled')->count();

        // Trends (%)
        $salesTrend = $yesterdaySales > 0 ? (($todaySales - $yesterdaySales) / $yesterdaySales) * 100 : ($todaySales > 0 ? 100 : 0);
        $trxTrend = $yesterdayCount > 0 ? (($todayCount - $yesterdayCount) / $yesterdayCount) * 100 : ($todayCount > 0 ? 100 : 0);

        // Weekly sparkline data (Last 7 days)
        $weeklyStats = Transaction::where('created_at', '>=', now()->subDays(6))
            ->whereNot('status', 'cancelled')
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $sparklineSales = [];
        $sparklineTrx = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $found = $weeklyStats->firstWhere('date', $date);
            $sparklineSales[] = $found ? (float)$found->total : 0;
            $sparklineTrx[] = $found ? (int)$found->count : 0;
        }

        // Get hourly stats for chart (Timeline)
        // Group by hour from filtered transactions
        $hourlyQuery = Transaction::selectRaw('HOUR(created_at) as hour, COUNT(*) as count, SUM(total_amount) as total')
            ->whereNot('status', 'cancelled');

        // Apply same filters to chart data
        if ($request->start_date) {
            $hourlyQuery->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $hourlyQuery->whereDate('created_at', '<=', $request->end_date);
        }
        if ($request->type) {
            $hourlyQuery->where('type', $request->type);
        }
        if ($request->cashier_id) {
            $hourlyQuery->where('cashier_id', $request->cashier_id);
        }
        if ($request->payment_method) {
            $hourlyQuery->where('payment_method', $request->payment_method);
        }

        $rawHourlyStats = $hourlyQuery->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // Initialize 24-hour array
        $hourlyLabels = [];
        $hourlyCounts = [];
        $hourlyAmounts = [];

        for ($i = 0; $i < 24; $i++) {
            $hourlyLabels[] = sprintf('%02d:00', $i);
            $found = $rawHourlyStats->firstWhere('hour', $i);
            $hourlyCounts[] = $found ? $found->count : 0;
            $hourlyAmounts[] = $found ? (float)$found->total : 0;
        }

        // Get Top Products for Chart
        $topProductsQuery = \App\Models\TransactionItem::query()
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->join('products', 'transaction_items.product_id', '=', 'products.id')
            ->whereNotIn('transactions.status', ['cancelled'])
            ->select('products.name', \DB::raw('SUM(transaction_items.quantity) as total_qty'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_qty')
            ->limit(10);

        // Apply same filters 
        if ($request->start_date) $topProductsQuery->whereDate('transactions.created_at', '>=', $request->start_date);
        if ($request->end_date) $topProductsQuery->whereDate('transactions.created_at', '<=', $request->end_date);
        if ($request->type) $topProductsQuery->where('transactions.type', $request->type);
        if ($request->cashier_id) $topProductsQuery->where('transactions.cashier_id', $request->cashier_id);
        if ($request->payment_method) $topProductsQuery->where('transactions.payment_method', $request->payment_method);

        $topProducts = $topProductsQuery->get();
        $topProductsLabels = $topProducts->pluck('name');
        $topProductsCounts = $topProducts->pluck('total_qty');

        // Get Top Revenue Products
        $topRevenueQuery = \App\Models\TransactionItem::query()
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->join('products', 'transaction_items.product_id', '=', 'products.id')
            ->whereNotIn('transactions.status', ['cancelled'])
            ->select('products.name', \DB::raw('SUM(transaction_items.subtotal) as total_revenue'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_revenue')
            ->limit(10);

        if ($request->start_date) $topRevenueQuery->whereDate('transactions.created_at', '>=', $request->start_date);
        if ($request->end_date) $topRevenueQuery->whereDate('transactions.created_at', '<=', $request->end_date);
        if ($request->type) $topRevenueQuery->where('transactions.type', $request->type);
        if ($request->cashier_id) $topRevenueQuery->where('transactions.cashier_id', $request->cashier_id);

        $topRevenue = $topRevenueQuery->get();
        $topRevenueLabels = $topRevenue->pluck('name');
        $topRevenueAmounts = $topRevenue->pluck('total_revenue');

        // Get Top Profit Products
        $topProfitQuery = \App\Models\TransactionItem::query()
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->join('products', 'transaction_items.product_id', '=', 'products.id')
            ->whereNotIn('transactions.status', ['cancelled'])
            ->select(
                'products.name', 
                \DB::raw('SUM(transaction_items.subtotal - ((COALESCE(products.cost, 0) / GREATEST(COALESCE(products.conversion_factor, 1), 1)) * transaction_items.quantity)) as total_profit')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_profit')
            ->limit(10);

        if ($request->start_date) $topProfitQuery->whereDate('transactions.created_at', '>=', $request->start_date);
        if ($request->end_date) $topProfitQuery->whereDate('transactions.created_at', '<=', $request->end_date);
        if ($request->type) $topProfitQuery->where('transactions.type', $request->type);
        if ($request->cashier_id) $topProfitQuery->where('transactions.cashier_id', $request->cashier_id);

        $topProfit = $topProfitQuery->get();
        $topProfitLabels = $topProfit->pluck('name');
        $topProfitAmounts = $topProfit->pluck('total_profit');

        // Payment Method Stats (Donut Chart)
        $paymentMethodQuery = Transaction::whereNot('status', 'cancelled')
            ->select('payment_method', \DB::raw('SUM(total_amount) as total'), \DB::raw('COUNT(*) as count'))
            ->groupBy('payment_method');
        
        if ($request->start_date) $paymentMethodQuery->whereDate('created_at', '>=', $request->start_date);
        if ($request->end_date) $paymentMethodQuery->whereDate('created_at', '<=', $request->end_date);
        if ($request->type) $paymentMethodQuery->where('type', $request->type);
        if ($request->cashier_id) $paymentMethodQuery->where('cashier_id', $request->cashier_id);
        
        $paymentMethodStats = $paymentMethodQuery->get()->map(function($item) {
            $labels = [
                'cash' => 'Tunai',
                'kredit' => 'Kredit',
                'transfer' => 'Transfer',
                'poin' => 'Poin',
                'saldo' => 'Saldo Simpanan',
                'cash_pickup' => 'Ambil di Toko (Tunai)',
                'qris' => 'QRIS'
            ];
            
            return [
                'method' => $labels[$item->payment_method] ?? ucfirst($item->payment_method ?: 'Tunai'),
                'total' => (float)$item->total,
                'count' => (int)$item->count
            ];
        });

        // Cashier Stats (Donut Chart)
        $cashierStatsQuery = Transaction::whereNot('status', 'cancelled')
            ->join('users', 'transactions.cashier_id', '=', 'users.id')
            ->select('users.name', \DB::raw('SUM(transactions.total_amount) as total'))
            ->groupBy('users.id', 'users.name');

        if ($request->start_date) $cashierStatsQuery->whereDate('transactions.created_at', '>=', $request->start_date);
        if ($request->end_date) $cashierStatsQuery->whereDate('transactions.created_at', '<=', $request->end_date);
        if ($request->type) $cashierStatsQuery->where('transactions.type', $request->type);
        if ($request->payment_method) $cashierStatsQuery->where('transactions.payment_method', $request->payment_method);
        
        $cashierStats = $cashierStatsQuery->get()->map(function($item) {
            return [
                'name' => $item->name,
                'total' => (float)$item->total
            ];
        });

        return view('commerce.pos.history', compact(
            'transactions', 'todaySales', 'todayCount', 'cashiers', 
            'hourlyLabels', 'hourlyCounts', 'hourlyAmounts',
            'topProductsLabels', 'topProductsCounts',
            'topRevenueLabels', 'topRevenueAmounts',
            'topProfitLabels', 'topProfitAmounts',
            'salesTrend', 'trxTrend', 'sparklineSales', 'sparklineTrx', 'paymentMethodStats', 'cashierStats'
        ));
    }

    public function generateJournal(Transaction $transaction)
    {
        if (! auth()->user()->hasStoreAccess()) {
            abort(403);
        }

        if (! in_array($transaction->status, ['paid', 'completed', 'delivered'])) {
            return redirect()->back()->with('error', 'Transaksi belum selesai, jurnal tidak dapat dibuat.');
        }

        $hasJournal = JournalEntry::where('reference_id', $transaction->id)
            ->whereIn('reference_type', [Transaction::class, 'transaction'])
            ->exists();

        if ($hasJournal) {
            return redirect()->back()->with('info', 'Jurnal sudah ada untuk transaksi ini.');
        }

        try {
            $transaction->load('items.product');
            \App\Services\JournalService::journalSale($transaction);
            return redirect()->back()->with('success', 'Jurnal penjualan berhasil dibuat.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membuat jurnal: ' . $e->getMessage());
        }
    }

    public function printHistory(Request $request)
    {
        $query = Transaction::latest();

        // Use same filters as history
        if ($request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        if (!$request->start_date && !$request->end_date) {
             // Default to today if no date filter is applied
             $query->whereDate('created_at', \Carbon\Carbon::today());
        }

        if ($request->type) {
            $query->where('type', $request->type);
        }

        if ($request->cashier_id) {
            $query->where('cashier_id', $request->cashier_id);
        }

        if ($request->payment_method) {
            $query->where('payment_method', $request->payment_method);
        }
        
        // No pagination for print, get all matching
        $transactions = $query->get();

        return view('commerce.pos.print_history', compact('transactions'));
    }

    public function receipt(Transaction $transaction)
    {
        $transaction->load(['items.product', 'cashier', 'user']);
        return view('commerce.pos.receipt', compact('transaction'));
    }

    /**
     * Process online order status
     */
    public function processOrder(Request $request, Transaction $transaction)
    {
        $request->validate([
            'status' => 'required|in:paid,processing,ready,completed,cancelled,delivered'
        ]);

        $transaction->update([
            'status' => $request->status,
            'cashier_id' => auth()->id()
        ]);

        if (in_array($request->status, ['paid', 'completed', 'delivered'])) {
            $hasJournal = JournalEntry::where('reference_id', $transaction->id)
                ->whereIn('reference_type', [Transaction::class, 'transaction'])
                ->exists();

            if (! $hasJournal) {
                try {
                    $transaction->load('items.product');
                    \App\Services\JournalService::journalSale($transaction);
                } catch (\Exception $e) {
                    Log::error('Process Order: Failed to create journal', [
                        'invoice' => $transaction->invoice_number,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }

        // Notify member when order is ready
        if ($request->status == 'ready' && $transaction->user) {
            $transaction->user->notify(new \App\Notifications\OrderReadyNotification($transaction));
        }

        $message = 'Status pesanan berhasil diperbarui!';
        if ($request->status == 'paid') $message = 'Pembayaran pesanan berhasil dikonfirmasi!';
        if ($request->status == 'processing') $message = 'Pesanan mulai diproses/disiapkan!';
        if ($request->status == 'ready') $message = 'Pesanan siap diambil/diantar!';
        if ($request->status == 'completed') $message = 'Pesanan selesai (Lunas)!';
        if ($request->status == 'delivered') $message = 'Pesanan diterima (Kredit Belum Lunas)!';
        if ($request->status == 'cancelled') $message = 'Pesanan berhasil dibatalkan!';

        return redirect()->back()->with('success', $message);
    }

    /**
     * Display credit transactions report
     */
    public function credits(Request $request)
    {
        $query = Transaction::with(['items.product', 'user.member', 'cashier'])
            ->where('payment_method', 'kredit')
            ->latest();

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        } else {
            // Default show all unpaid credits (credit, processing, ready, delivered)
            // Anything that is NOT completed
            $query->where('status', '!=', 'completed');
        }

        // Filter by member
        if ($request->member_id) {
            $query->where('user_id', $request->member_id);
        }

        // Filter by date range
        if ($request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $credits = $query->paginate(20);

        // Summary
        $totalPending = Transaction::where('payment_method', 'kredit')
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->sum(\DB::raw('total_amount - paid_amount'));
        
        $totalPaid = Transaction::where('payment_method', 'kredit')
            ->where('status', 'completed')
            ->sum('total_amount');

        $pendingCount = Transaction::where('payment_method', 'kredit')
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->count();

        return view('commerce.pos.credits', compact('credits', 'totalPending', 'totalPaid', 'pendingCount'));
    }

    /**
     * Process credit payment
     */
    public function payCredit(Request $request, Transaction $transaction)
    {
        $request->validate([
            'payment_method' => 'required|in:cash,transfer,saldo',
            'amount' => 'nullable|numeric|min:1',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($transaction->payment_method !== 'kredit' || in_array($transaction->status, ['completed', 'cancelled'])) {
            return redirect()->back()->with('error', 'Transaksi ini sudah dilunasi atau bukan transaksi kredit.');
        }

        try {
            \DB::transaction(function () use ($request, $transaction) {
                $remaining = (float) $transaction->total_amount - (float) $transaction->paid_amount;
                if ($remaining <= 0) {
                    throw new \Exception('Tagihan sudah lunas.');
                }

                $amount = (float) ($request->amount ?? 0);
                if ($amount <= 0) {
                    $amount = $remaining;
                }
                if ($amount > $remaining) {
                    throw new \Exception('Jumlah pembayaran melebihi sisa tagihan.');
                }

                // If paying with saldo, deduct from savings
                if ($request->payment_method === 'saldo' && $transaction->user_id) {
                    $balance = \App\Models\Saving::where('member_id', $transaction->user->member->id ?? 0)
                        ->where('type', 'sukarela')
                        ->sum('amount');

                    if ($balance < $amount) {
                        throw new \Exception('Saldo tidak mencukupi. Saldo saat ini: Rp ' . number_format($balance, 0, ',', '.'));
                    }

                    // Deduct from savings
                    $saving = \App\Models\Saving::create([
                        'member_id' => $transaction->user->member->id,
                        'type' => 'sukarela',
                        'transaction_type' => 'withdrawal',
                        'amount' => $amount, // Positive for consistency
                        'transaction_date' => now(),
                        'description' => 'Pelunasan kredit: ' . $transaction->invoice_number,
                        'created_by' => auth()->id(),
                    ]);

                    // Journal Saving Withdrawal
                    \App\Services\JournalService::journalSavingWithdrawal($saving);
                }

                $installments = $transaction->creditInstallments()
                    ->whereNotIn('status', ['paid'])
                    ->orderBy('due_date')
                    ->get();

                if ($installments->isNotEmpty()) {
                    $amountLeft = $amount;
                    foreach ($installments as $installment) {
                        if ($amountLeft + 0.01 < (float) $installment->amount) {
                            throw new \Exception('Jumlah pembayaran harus sesuai nilai angsuran.');
                        }
                        if ($amountLeft >= (float) $installment->amount - 0.01) {
                            $installment->update([
                                'status' => 'paid',
                                'paid_at' => now(),
                                'payment_method' => $request->payment_method,
                                'notes' => $request->notes,
                            ]);
                            $amountLeft -= (float) $installment->amount;
                        }
                        if ($amountLeft <= 0.01) {
                            break;
                        }
                    }
                    if ($amountLeft > 0.01) {
                        throw new \Exception('Jumlah pembayaran melebihi angsuran yang tersedia.');
                    }
                }

                $newPaid = (float) $transaction->paid_amount + $amount;
                $isCompleted = $newPaid >= (float) $transaction->total_amount - 0.01;
                $transaction->update([
                    'status' => $isCompleted ? 'completed' : $transaction->status,
                    'paid_amount' => $isCompleted ? $transaction->total_amount : $newPaid,
                    'notes' => ($isCompleted ? 'Dilunasi' : 'Cicilan') . ' via ' . strtoupper($request->payment_method) . 
                              ($request->notes ? ' - ' . $request->notes : '') .
                              ' pada ' . now()->format('d/m/Y H:i') . 
                              ' oleh ' . auth()->user()->name,
                ]);

                \App\Services\JournalService::journalTransactionCreditPayment($transaction, $amount, $request->payment_method);
            });

            return redirect()->route('pos.credits')->with('success', 'Pembayaran kredit berhasil diproses!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Export sales history data to Excel based on search/filter.
     */
    public function export(Request $request)
    {
        if (!auth()->user()->hasAdminAccess()) {
            abort(403);
        }

        $query = Transaction::with(['items.product', 'cashier'])->latest();

        // Filter by date range
        if ($request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->type) {
            $query->where('type', $request->type);
        }

        if ($request->cashier_id) {
            $query->where('cashier_id', $request->cashier_id);
        }

        if ($request->payment_method) {
            $query->where('payment_method', $request->payment_method);
        }

        $transactions = $query->get();

        // Create Excel file
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Riwayat Penjualan');

        // Style Settings
        $titleStyle = [
            'font' => ['bold' => true, 'size' => 16],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        ];
        $subtitleStyle = [
            'font' => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '666666']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];

        // Report Title
        $sheet->mergeCells('A1:I1');
        $sheet->setCellValue('A1', 'LAPORAN RIWAYAT PENJUALAN');
        $sheet->getStyle('A1')->applyFromArray($titleStyle);

        // Filter Info
        $filterInfo = [];
        if ($request->start_date) $filterInfo[] = "Mulai: " . Carbon::parse($request->start_date)->format('d/m/Y');
        if ($request->end_date) $filterInfo[] = "Sampai: " . Carbon::parse($request->end_date)->format('d/m/Y');
        if ($request->type) $filterInfo[] = "Tipe: " . ucfirst($request->type);
        
        $sheet->mergeCells('A2:I2');
        $sheet->setCellValue('A2', empty($filterInfo) ? 'Semua Data - Diunduh: ' . date('d/m/Y H:i') : implode(' | ', $filterInfo) . ' | Diunduh: ' . date('d/m/Y H:i'));
        $sheet->getStyle('A2')->applyFromArray($subtitleStyle);

        // Empty row
        $sheet->setCellValue('A3', '');

        // Column Headers (at row 4)
        $headers = ['No', 'Invoice', 'Tanggal & Waktu', 'Anggota', 'Tipe', 'Total Item', 'Total Transaksi', 'Metode Bayar', 'Kasir'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '4', $header);
            $col++;
        }
        $sheet->getStyle('A4:I4')->applyFromArray($headerStyle);

        // Data (starting at row 5)
        $row = 5;
        foreach ($transactions as $index => $transaction) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $transaction->invoice_number);
            $sheet->setCellValue('C' . $row, $transaction->created_at->format('d/m/Y H:i'));
            $sheet->setCellValue('D' . $row, $transaction->user->name ?? '-');
            $sheet->setCellValue('E' . $row, ucfirst($transaction->type));
            $sheet->setCellValue('F' . $row, $transaction->items->sum('quantity'));
            $sheet->setCellValue('G' . $row, $transaction->total_amount);
            $methodLabels = [
                'cash' => 'Tunai',
                'kredit' => 'Kredit',
                'transfer' => 'Transfer',
                'poin' => 'Poin',
                'saldo' => 'Saldo',
                'cash_pickup' => 'Ambil di Toko',
                'qris' => 'QRIS'
            ];
            $methodLabel = $methodLabels[$transaction->payment_method] ?? strtoupper($transaction->payment_method);
            $sheet->setCellValue('H' . $row, $methodLabel);
            $sheet->setCellValue('I' . $row, $transaction->cashier->name ?? '-');
            $row++;
        }

        // Totals row
        $sheet->mergeCells('A' . $row . ':F' . $row);
        $sheet->setCellValue('A' . $row, 'TOTAL PENJUALAN');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $sheet->setCellValue('G' . $row, $transactions->sum('total_amount'));
        $sheet->getStyle('G' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row . ':I' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('F3F4F6');

        // Format amount columns
        $sheet->getStyle('G5:G' . $row)->getNumberFormat()->setFormatCode('#,##0');

        // Auto-size columns
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Add borders to data
        $sheet->getStyle('A4:I' . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Download
        $filename = 'Riwayat_Penjualan_' . date('Y-m-d_His') . '.xlsx';
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    /**
     * Show scanner page
     */
    public function scan()
    {
        return view('commerce.pos.scan');
    }

    /**
     * Process scanned invoice/QR code
     */
    public function processScan(Request $request)
    {
        $request->validate([
            'invoice' => 'required|string',
        ]);

        $transaction = Transaction::where('invoice_number', $request->invoice)->first();

        if (!$transaction) {
            return redirect()->back()->with('error', 'Pesanan tidak ditemukan: ' . $request->invoice);
        }

        return redirect()->route('pos.manage', $transaction->id);
    }

    /**
     * Show order management page (Result of scan)
     */
    public function manage(Transaction $transaction)
    {
        return view('commerce.pos.manage', compact('transaction'));
    }
    /**
     * Send bulk reminders to all members with pending credit
     */
    public function remindAll()
    {
        if (!auth()->user()->hasAdminAccess()) {
            abort(403);
        }

        // Get all unpaid credit transactions
        $transactions = Transaction::where('payment_method', 'kredit')
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->with(['user'])
            ->get();

        if ($transactions->isEmpty()) {
            return redirect()->back()->with('info', 'Tidak ada tagihan kredit yang perlu ditagih.');
        }

        // Group by User ID to send one email per person
        $bills = $transactions->groupBy('user_id');
        $sentCount = 0;

        foreach ($bills as $userId => $userTransactions) {
            $user = $userTransactions->first()->user;

            if ($user && $user->email) {
                $totalDebt = $userTransactions->sum('total_amount');
                $invoiceCount = $userTransactions->count();

                $details = [
                    'total_debt' => $totalDebt,
                    'invoice_count' => $invoiceCount
                ];

                try {
                    $user->notify(new \App\Notifications\CreditBillNotification($details));
                    $sentCount++;
                } catch (\Exception $e) {
                    \Log::error("Gagal kirim tagihan ke user {$userId}: " . $e->getMessage());
                }
            }
        }

        return redirect()->back()->with('success', "Berhasil mengirim {$sentCount} email tagihan ke anggota.");
    }

    /**
     * Cancel a transaction
     */
    public function cancel(Request $request, Transaction $transaction)
    {
        if (!auth()->user()->hasAdminAccess() && !auth()->user()->hasStoreAccess()) {
            abort(403);
        }

        if ($transaction->status === 'cancelled') {
            return redirect()->back()->with('error', 'Transaksi sudah dibatalkan sebelumnya.');
        }

        try {
            \DB::transaction(function () use ($transaction, $request) {
                // 1. Restore Stock
                foreach ($transaction->items as $item) {
                    if ($item->product) {
                        $item->product->increment('stock', $item->quantity);
                    }
                }

                // 2. Reverse Points (if member transaction)
                if ($transaction->user_id) {
                    $member = \App\Models\Member::where('user_id', $transaction->user_id)->first();
                    if ($member) {
                        $earnRate = \App\Models\Setting::get('point_earn_rate', 10000);
                        $pointsToDeduct = floor($transaction->total_amount / $earnRate);
                        if ($pointsToDeduct > 0) {
                            $member->decrement('points', min($member->points, $pointsToDeduct));
                        }
                    }
                }

                // 3. Refund Savings (if paid via saldo)
                if ($transaction->payment_method === 'saldo' && $transaction->user_id) {
                    $member = \App\Models\Member::where('user_id', $transaction->user_id)->first();
                    if ($member) {
                        $refund = \App\Models\Saving::create([
                            'member_id' => $member->id,
                            'type' => 'sukarela',
                            'transaction_type' => 'deposit',
                            'amount' => $transaction->total_amount,
                            'transaction_date' => now(),
                            'description' => 'Refund Pembatalan Transaksi: ' . $transaction->invoice_number,
                            'created_by' => auth()->id(),
                        ]);

                        // Journal Refund Deposit
                        \App\Services\JournalService::journalSavingDeposit($refund);
                    }
                }

                // 4. Reverse Journal Entry
                if ($transaction->journalEntry) {
                    \App\Services\JournalService::reverseJournal(
                        $transaction->journalEntry, 
                        "Pembatalan Transaksi - {$transaction->invoice_number}"
                    );
                }

                // 5. Cancel Credit Installments
                if ($transaction->payment_method === 'kredit') {
                    $transaction->creditInstallments()->update(['status' => 'cancelled']);
                }

                // 6. Update Transaction Status
                $transaction->update([
                    'status' => 'cancelled',
                    'notes' => ($transaction->notes ? $transaction->notes . ' | ' : '') . 
                               'Dibatalkan oleh ' . auth()->user()->name . ' pada ' . now()->format('d/m/Y H:i') . 
                               ($request->reason ? ' Alasan: ' . $request->reason : '')
                ]);

                \App\Models\AuditLog::log(
                    'cancel_transaction',
                    "Membatalkan transaksi {$transaction->invoice_number}",
                    $transaction
                );
            });

            return redirect()->back()->with('success', 'Transaksi berhasil dibatalkan dan stok telah dikembalikan.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membatalkan transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Export detailed product sales to Excel.
     */
    public function exportItems(Request $request)
    {
        if (!auth()->user()->hasAdminAccess()) {
            abort(403);
        }

        $query = \App\Models\TransactionItem::query()
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->join('products', 'transaction_items.product_id', '=', 'products.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->whereIn('transactions.status', ['completed', 'paid', 'delivered', 'credit'])
            ->select(
                'transactions.created_at as transaction_date',
                'transactions.invoice_number',
                'products.name as product_name',
                'products.code as product_code',
                'categories.name as category_name',
                'transaction_items.quantity',
                'transaction_items.price',
                'transaction_items.subtotal'
            )
            ->orderBy('transactions.created_at', 'desc');

        // Filter by date range
        if ($request->start_date) {
            $query->whereDate('transactions.created_at', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate('transactions.created_at', '<=', $request->end_date);
        }

        // Filter by type
        if ($request->type) {
            $query->where('transactions.type', $request->type);
        }

        // Filter by cashier
        if ($request->cashier_id) {
            $query->where('transactions.cashier_id', $request->cashier_id);
        }

        $items = $query->get();

        // Create Excel file
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Detail Produk Terjual');

        // Style Settings
        $titleStyle = [
            'font' => ['bold' => true, 'size' => 16],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        ];
        $subtitleStyle = [
            'font' => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '666666']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];

        // Report Title
        $sheet->mergeCells('A1:H1');
        $sheet->setCellValue('A1', 'LAPORAN DETAIL PRODUK TERJUAL');
        $sheet->getStyle('A1')->applyFromArray($titleStyle);

        // Filter Info
        $filterInfo = [];
        if ($request->start_date) $filterInfo[] = "Mulai: " . Carbon::parse($request->start_date)->format('d/m/Y');
        if ($request->end_date) $filterInfo[] = "Sampai: " . Carbon::parse($request->end_date)->format('d/m/Y');
        if ($request->type) $filterInfo[] = "Tipe: " . ucfirst($request->type);
        
        $sheet->mergeCells('A2:H2');
        $sheet->setCellValue('A2', empty($filterInfo) ? 'Semua Data - Diunduh: ' . date('d/m/Y H:i') : implode(' | ', $filterInfo) . ' | Diunduh: ' . date('d/m/Y H:i'));
        $sheet->getStyle('A2')->applyFromArray($subtitleStyle);

        // Column Headers
        $headers = ['No', 'Tanggal', 'Invoice', 'Nama Produk', 'Kode/SKU', 'Kategori', 'Qty', 'Harga Satuan', 'Subtotal'];
        $col = 'A';
        foreach (['No', 'Tanggal', 'Invoice', 'Nama Produk', 'SKU', 'Kategori', 'Qty', 'Harga', 'Subtotal'] as $header) {
            $sheet->setCellValue($col . '4', $header);
            $col++;
        }
        $sheet->getStyle('A4:I4')->applyFromArray($headerStyle);

        // Data
        $row = 5;
        foreach ($items as $index => $item) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, Carbon::parse($item->transaction_date)->format('d/m/Y H:i'));
            $sheet->setCellValue('C' . $row, $item->invoice_number);
            $sheet->setCellValue('D' . $row, $item->product_name);
            $sheet->setCellValue('E' . $row, $item->product_code);
            $sheet->setCellValue('F' . $row, $item->category_name ?? '-');
            $sheet->setCellValue('G' . $row, $item->quantity);
            $sheet->setCellValue('H' . $row, $item->price);
            $sheet->setCellValue('I' . $row, $item->subtotal);
            $row++;
        }

        // Totals
        $sheet->mergeCells('A' . $row . ':F' . $row);
        $sheet->setCellValue('A' . $row, 'TOTAL KESELURUHAN');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $sheet->setCellValue('G' . $row, $items->sum('quantity'));
        $sheet->setCellValue('I' . $row, $items->sum('subtotal'));
        $sheet->getStyle('G' . $row . ':I' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row . ':I' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('F3F4F6');

        // Formats
        $sheet->getStyle('H5:I' . $row)->getNumberFormat()->setFormatCode('#,##0');
        
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'Detail_Produk_Terjual_' . date('Y-m-d_His') . '.xlsx';
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }
}
