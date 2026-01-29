<?php

namespace App\Http\Controllers;

use App\Models\ConsignmentSettlement;
use App\Models\TransactionItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConsignmentSettlementController extends Controller
{
    /**
     * Display a listing of settlements.
     */
    public function index()
    {
        $settlements = ConsignmentSettlement::with(['consignor', 'paidBy'])
            ->latest()
            ->paginate(10);
            
        return view('commerce.consignment.settlements.index', compact('settlements'));
    }

    /**
     * Show report of pending settlements (to be processed).
     */
    public function create(Request $request)
    {
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate = $request->input('end_date', date('Y-m-d'));

        // Query pending items
        $pendingItems = TransactionItem::whereHas('product', function($q) {
                $q->where('is_consignment', true);
            })
            ->whereNull('consignment_settlement_id')
            ->whereHas('transaction', function($q) use ($startDate, $endDate) {
                $q->where('status', '!=', 'cancelled')
                  ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            })
            ->with(['product', 'transaction'])
            ->get();

        // Group by Consignor
        $grouped = $pendingItems->groupBy(function($item) {
            return $item->product->consignor_type . '_' . $item->product->consignor_id;
        });

        $report = [];
        foreach ($grouped as $key => $items) {
            $firstItem = $items->first();
            $consignor = $firstItem->product->consignor;
            
            $totalSales = 0;
            $totalCost = 0;
            $qty = 0;

            foreach ($items as $item) {
                $qty += $item->quantity;
                $totalSales += $item->quantity * $item->price;
                // Cost calculation: use recorded cost snapshot if available, else current consignment price
                // Ideally TransactionItem should record cost at time of sale. 
                // Currently `TransactionItem` has `price` (selling price). 
                // It does NOT have `cost` recorded. We rely on Product current cost or handle it.
                // NOTE: Best practice is to snap cost at sale. For now, we use Product's current consignment_price.
                $cost = $item->product->consignment_price; 
                $totalCost += $item->quantity * $cost;
            }

            $report[] = [
                'key' => $key,
                'consignor' => $consignor,
                'consignor_type' => $firstItem->product->consignor_type,
                'consignor_id' => $firstItem->product->consignor_id,
                'item_count' => $items->count(), // Count of rows (transactions)
                'total_qty' => $qty,
                'total_sales' => $totalSales,
                'total_payable' => $totalCost,
                'total_profit' => $totalSales - $totalCost,
            ];
        }

        return view('commerce.consignment.settlements.create', compact('report', 'startDate', 'endDate'));
    }

    /**
     * Store a newly created settlement.
     */
    public function store(Request $request)
    {
        $request->validate([
            'consignor_type' => 'required',
            'consignor_id' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        try {
            DB::beginTransaction();

            $startDate = $request->start_date;
            $endDate = $request->end_date;
            
            // Re-query items to lock them
            $items = TransactionItem::whereHas('product', function($q) use ($request) {
                    $q->where('is_consignment', true)
                      ->where('consignor_type', $request->consignor_type)
                      ->where('consignor_id', $request->consignor_id);
                })
                ->whereNull('consignment_settlement_id')
                ->whereHas('transaction', function($q) use ($startDate, $endDate) {
                    $q->where('status', '!=', 'cancelled')
                      ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                })
                ->get();
            
            if ($items->isEmpty()) {
                throw new \Exception(__('messages.consignment.settlement.error_no_data'));
            }

            $totalSales = 0;
            $totalPayable = 0;

            foreach ($items as $item) {
                $qty = $item->quantity;
                $sales = $qty * $item->price;
                $cost = $item->product->consignment_price * $qty; // Using current master price

                $totalSales += $sales;
                $totalPayable += $cost;
            }

            $totalProfit = $totalSales - $totalPayable;

            // Create Settlement
            $settlement = ConsignmentSettlement::create([
                'consignor_type' => $request->consignor_type,
                'consignor_id' => $request->consignor_id,
                'period_start' => $startDate,
                'period_end' => $endDate,
                'total_sales_amount' => $totalSales,
                'total_payable_amount' => $totalPayable,
                'total_profit_amount' => $totalProfit,
                'status' => 'paid',
                'paid_at' => now(),
                'paid_by' => auth()->id(),
                'notes' => 'Metode Pembayaran: ' . ucfirst($request->payment_method ?? 'cash'),
            ]);

            // Update Items
            foreach ($items as $item) {
                $item->consignment_settlement_id = $settlement->id;
                $item->save();
            }

            // HANDLE PAYMENT METHOD
            $method = $request->payment_method ?? 'cash';
            
            if ($method === 'savings' && $request->consignor_type === 'member') {
                // Auto-deposit to Voluntary Savings
                $saving = \App\Models\Saving::create([
                    'member_id' => $request->consignor_id, // Consignor ID is Member ID here
                    'type' => 'sukarela',
                    'transaction_type' => 'deposit',
                    'amount' => $totalPayable, // Net amount to partner
                    'transaction_date' => now(),
                    'reference_number' => \App\Models\Saving::generateReferenceNumber(),
                    'description' => "Bagi Hasil Konsinyasi (Settlement #{$settlement->transaction_number})",
                    'created_by' => auth()->id(),
                ]);

                // Auto-journal for Saving Deposit (Reclassification: Cash -> Savings ? No)
                // Wait, logic:
                // JournalService::journalSavingDeposit usually does: Dr Cash, Cr Savings Liability.
                // BUT here, we are NOT receiving cash. We are moving from Consignment Liability -> Savings Liability.
                // If we run `journalSavingDeposit`, it will Debit Cash. That is WRONG in this context (Cash didn't increase).
                // CORRECT ENTRY: Dr Consignment Liability, Cr Voluntary Savings.
                
                // However, the `settlement` creation logic inherently assumes we used Cash (if we strictly follow standard settlement).
                // Let's check `ConsignmentSettlement` journal logic again. 
                // Currently `ConsignmentSettlement` does NOT generate a journal entry itself in this controller.
                // So:
                // 1. If we pay by CASH: We need Dr Consignment Payable, Cr Cash. (Not yet implemented automatically, usually manual).
                // 2. If we pay by SAVINGS: We need Dr Consignment Payable, Cr Voluntary Savings.
                
                // IF `JournalService::journalSavingDeposit` creates "Dr Cash, Cr Savings", using it here would result in:
                // Dr Cash
                //    Cr Voluntary Savings
                // And we still have Consignment Payable sitting there.
                
                // Ideally, we need a custom Journal Entry here:
                // Reclassification.
                
                // FOR NOW: To avoid breaking the `JournalService` abstraction which might be rigid,
                // and since the user likely Manually Journals the Settlement Payout (Dr Payable, Cr Cash),
                // doing a "Deposit" technically implies "We took money from Payable and put it into Savings".
                // Visually: Consignment Payable -> Cash (Virtual) -> Savings.
                // So calling `journalSavingDeposit` will create: Dr Cash, Cr Savings.
                // And the User should record the Settlement as: Dr Consignment Payable, Cr Cash.
                // The net effect: Dr Consignment Payable, Cr Savings. (Cash cancels out).
                
                // So YES, safely call the service. It simulates "Cash In" for the saving, which matches the "Cash Out" from the settlement payout.
                \App\Services\JournalService::journalSavingDeposit($saving);
                
                $message = __('messages.consignment.settlement.success_savings');
            } else {
                $message = __('messages.consignment.settlement.success_manual');
            }

            // Create Journal for the Settlement Payout (Dr HPP, Cr Cash)
            // This balances the financial side.
            // If Savings: Dr HPP, Cr Cash. (Then Savings Svc: Dr Cash, Cr Savings). Net: Dr HPP, Cr Savings.
            // If Cash: Dr HPP, Cr Cash.
            \App\Services\JournalService::journalConsignmentSettlement($settlement, $method);

            \App\Models\AuditLog::log(
                'create',
                "Membuat settlement konsinyasi ({$method}): {$settlement->transaction_number}",
                $settlement
            );

            DB::commit();

            return redirect()->route('consignment.settlements.show', $settlement)
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', __('messages.consignment.settlement.error_process', ['error' => $e->getMessage()]));
        }
    }

    public function show(ConsignmentSettlement $settlement)
    {
        $settlement->load(['consignor', 'items.product', 'items.transaction']);
        return view('commerce.consignment.settlements.show', compact('settlement'));
    }
}
