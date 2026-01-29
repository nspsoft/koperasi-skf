<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
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
        $query = Transaction::with(['items.product', 'cashier', 'user'])->latest();

        // Filter by date
        if ($request->date) {
            $query->whereDate('created_at', $request->date);
        }

        // Filter by type
        if ($request->type) {
            $query->where('type', $request->type);
        }

        $transactions = $query->paginate(20);

        // Get summary
        $todaySales = Transaction::whereDate('created_at', now())->sum('total_amount');
        $todayCount = Transaction::whereDate('created_at', now())->count();

        return view('commerce.pos.history', compact('transactions', 'todaySales', 'todayCount'));
    }

    public function printHistory(Request $request)
    {
        $query = Transaction::latest();

        // Use same filters as history
        if ($request->date) {
            $query->whereDate('created_at', $request->date);
        } else {
             // Default to today if no date filter is applied
             $query->whereDate('created_at', \Carbon\Carbon::today());
        }

        if ($request->type) {
            $query->where('type', $request->type);
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
            ->sum('total_amount');
        
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
            'notes' => 'nullable|string|max:500',
        ]);

        if ($transaction->status !== 'credit') {
            return redirect()->back()->with('error', 'Transaksi ini sudah dilunasi atau bukan transaksi kredit.');
        }

        try {
            \DB::transaction(function () use ($request, $transaction) {
                // If paying with saldo, deduct from savings
                if ($request->payment_method === 'saldo' && $transaction->user_id) {
                    $balance = \App\Models\Saving::where('member_id', $transaction->user->member->id ?? 0)
                        ->where('type', 'sukarela')
                        ->sum('amount');

                    if ($balance < $transaction->total_amount) {
                        throw new \Exception('Saldo tidak mencukupi. Saldo saat ini: Rp ' . number_format($balance, 0, ',', '.'));
                    }

                    // Deduct from savings
                    $saving = \App\Models\Saving::create([
                        'member_id' => $transaction->user->member->id,
                        'type' => 'sukarela',
                        'transaction_type' => 'withdrawal',
                        'amount' => $transaction->total_amount, // Positive for consistency
                        'transaction_date' => now(),
                        'description' => 'Pelunasan kredit: ' . $transaction->invoice_number,
                        'created_by' => auth()->id(),
                    ]);

                    // Journal Saving Withdrawal
                    \App\Services\JournalService::journalSavingWithdrawal($saving);
                }

                // Update transaction
                $transaction->update([
                    'status' => 'completed',
                    'paid_amount' => $transaction->total_amount,
                    'notes' => 'Dilunasi via ' . strtoupper($request->payment_method) . 
                              ($request->notes ? ' - ' . $request->notes : '') .
                              ' pada ' . now()->format('d/m/Y H:i') . 
                              ' oleh ' . auth()->user()->name,
                ]);

                // Journal Credit Payment
                \App\Services\JournalService::journalTransactionCreditPayment($transaction, $transaction->total_amount, $request->payment_method);
            });

            return redirect()->route('pos.credits')->with('success', 'Kredit berhasil dilunasi!');

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

        if ($request->date) {
            $query->whereDate('created_at', $request->date);
        }

        if ($request->type) {
            $query->where('type', $request->type);
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
        if ($request->date) $filterInfo[] = "Tanggal: " . Carbon::parse($request->date)->format('d/m/Y');
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
            $sheet->setCellValue('H' . $row, strtoupper($transaction->payment_method));
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
}
