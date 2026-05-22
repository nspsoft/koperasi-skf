<?php

namespace App\Http\Controllers;

use App\Models\ConsignmentSettlement;
use App\Models\ConsignmentInboundItem;
use App\Models\ConsignmentReturnItem;
use App\Models\Product;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ConsignmentDashboardController extends Controller
{
    public function index(Request $request)
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // 1. KPI Data (This Month)
        // Total Hutang (Unpaid) - All time pending (Unsettled TransactionItems)
        $totalUnpaid = TransactionItem::join('transactions', 'transactions.id', '=', 'transaction_items.transaction_id')
            ->join('products', 'products.id', '=', 'transaction_items.product_id')
            ->where('transactions.status', 'completed')
            ->where('products.is_consignment', true)
            ->whereNull('transaction_items.consignment_settlement_id')
            ->sum(DB::raw('transaction_items.quantity * products.consignment_price'));

        // Total Dibayar (Settled) This Month
        $totalSettledMonth = ConsignmentSettlement::where('status', 'paid')
            ->whereMonth('paid_at', $currentMonth)
            ->whereYear('paid_at', $currentYear)
            ->sum('total_payable_amount');

        // Nilai Stok Konsinyasi (All Active Stock)
        $stockValue = Product::where('is_consignment', true)
            ->where('is_active', true)
            ->selectRaw('SUM(stock * cost) as total_value')
            ->value('total_value') ?? 0;

        // Total Inbound (Quantity) This Month
        $inboundMonth = ConsignmentInboundItem::join('consignment_inbounds', 'consignment_inbounds.id', '=', 'consignment_inbound_items.consignment_inbound_id')
            ->whereMonth('consignment_inbounds.inbound_date', $currentMonth)
            ->whereYear('consignment_inbounds.inbound_date', $currentYear)
            ->sum('consignment_inbound_items.quantity');

        // Total Return (Quantity) This Month
        $returnMonth = ConsignmentReturnItem::join('consignment_returns', 'consignment_returns.id', '=', 'consignment_return_items.consignment_return_id')
            ->whereMonth('consignment_returns.return_date', $currentMonth)
            ->whereYear('consignment_returns.return_date', $currentYear)
            ->sum('consignment_return_items.quantity');

        // Real-time Consignment Profit This Month
        $realtimeConsignment = TransactionItem::join('transactions', 'transactions.id', '=', 'transaction_items.transaction_id')
            ->join('products', 'products.id', '=', 'transaction_items.product_id')
            ->where('transactions.status', 'completed')
            ->where('products.is_consignment', true)
            ->whereMonth('transactions.created_at', $currentMonth)
            ->whereYear('transactions.created_at', $currentYear)
            ->selectRaw('SUM(transaction_items.quantity * transaction_items.price) as total_sales, SUM(transaction_items.quantity * (transaction_items.price - products.cost)) as total_profit')
            ->first();

        // 2. Chart Data (This Year Trend)
        $monthlyData = TransactionItem::join('transactions', 'transactions.id', '=', 'transaction_items.transaction_id')
            ->join('products', 'products.id', '=', 'transaction_items.product_id')
            ->where('transactions.status', 'completed')
            ->where('products.is_consignment', true)
            ->whereYear('transactions.created_at', $currentYear)
            ->selectRaw('
                MONTH(transactions.created_at) as month,
                SUM(transaction_items.quantity * transaction_items.price) as total_sales,
                SUM(transaction_items.quantity * (transaction_items.price - products.cost)) as total_profit
            ')
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        $monthlySales = array_fill(1, 12, 0);
        $monthlyProfit = array_fill(1, 12, 0);

        foreach ($monthlyData as $month => $data) {
            $monthlySales[$month] = $data->total_sales;
            $monthlyProfit[$month] = $data->total_profit;
        }

        // 3. Top Suppliers (Real-time from Transactions to get accurate sales/profit)
        $topSuppliersQuery = TransactionItem::join('transactions', 'transactions.id', '=', 'transaction_items.transaction_id')
            ->join('products', 'products.id', '=', 'transaction_items.product_id')
            ->where('transactions.status', 'completed')
            ->where('products.is_consignment', true)
            ->selectRaw('
                products.consignor_type,
                products.consignor_id,
                SUM(transaction_items.quantity * transaction_items.price) as total_sales,
                SUM(transaction_items.quantity * (transaction_items.price - products.cost)) as total_profit
            ')
            ->groupBy('products.consignor_type', 'products.consignor_id');

        // We fetch all grouped data then map relations to get names.
        $topSuppliersData = $topSuppliersQuery->get();
        $topSuppliers = [];

        foreach ($topSuppliersData as $supplierRow) {
            $name = 'Unknown';
            if ($supplierRow->consignor_type === 'supplier') {
                $supplier = \App\Models\Supplier::find($supplierRow->consignor_id);
                if ($supplier) $name = $supplier->name . ' (Supplier)';
            } elseif ($supplierRow->consignor_type === 'member') {
                $member = \App\Models\Member::with('user')->find($supplierRow->consignor_id);
                if ($member && $member->user) $name = $member->user->name . ' (Anggota)';
            }
            
            $topSuppliers[] = [
                'name' => $name,
                'sales' => (float)$supplierRow->total_sales,
                'profit' => (float)$supplierRow->total_profit,
            ];
        }

        // Sort by sales descending
        $topSuppliersBySales = collect($topSuppliers)->sortByDesc('sales')->take(5)->values()->all();
        
        // Sort by profit descending
        $topSuppliersByProfit = collect($topSuppliers)->sortByDesc('profit')->take(5)->values()->all();

        // 4. Data Tables
        // Tagihan Belum Dibayar (Unsettled Consignments grouped by Consignor)
        $unsettledQuery = TransactionItem::join('transactions', 'transactions.id', '=', 'transaction_items.transaction_id')
            ->join('products', 'products.id', '=', 'transaction_items.product_id')
            ->where('transactions.status', 'completed')
            ->where('products.is_consignment', true)
            ->whereNull('transaction_items.consignment_settlement_id')
            ->selectRaw('
                products.consignor_type,
                products.consignor_id,
                SUM(transaction_items.quantity * products.consignment_price) as total_payable,
                MIN(transactions.created_at) as oldest_transaction
            ')
            ->groupBy('products.consignor_type', 'products.consignor_id')
            ->orderBy('oldest_transaction', 'asc')
            ->take(5)
            ->get();

        $pendingSettlements = [];
        foreach ($unsettledQuery as $row) {
            $name = 'Unknown';
            if ($row->consignor_type === 'supplier') {
                $supplier = \App\Models\Supplier::find($row->consignor_id);
                if ($supplier) $name = $supplier->name . ' (Supplier)';
            } elseif ($row->consignor_type === 'member') {
                $member = \App\Models\Member::with('user')->find($row->consignor_id);
                if ($member && $member->user) $name = $member->user->name . ' (Anggota)';
            }
            
            // Create a fake object to match the view's expected format
            $pendingSettlements[] = (object)[
                'consignor' => (object)['name' => $name],
                'transaction_number' => 'Menunggu Settlement',
                'period_end' => Carbon::parse($row->oldest_transaction),
                'total_payable_amount' => (float)$row->total_payable
            ];
        }

        $pendingSettlementsCount = TransactionItem::join('transactions', 'transactions.id', '=', 'transaction_items.transaction_id')
            ->join('products', 'products.id', '=', 'transaction_items.product_id')
            ->where('transactions.status', 'completed')
            ->where('products.is_consignment', true)
            ->whereNull('transaction_items.consignment_settlement_id')
            ->select('products.consignor_type', 'products.consignor_id')
            ->groupBy('products.consignor_type', 'products.consignor_id')
            ->get()
            ->count();

        // Slow Moving Stock (Stock > 0, hasn't sold in the last 30 days)
        // Actually, getting accurate slow moving requires complex query. We can show products with highest stock value.
        $highestStockValue = Product::where('is_consignment', true)
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->selectRaw('*, (stock * cost) as total_value')
            ->orderByDesc('total_value')
            ->take(5)
            ->get();

        // 5. Supplier Data Per Month for Drilldown
        $monthlySupplierDataQuery = TransactionItem::join('transactions', 'transactions.id', '=', 'transaction_items.transaction_id')
            ->join('products', 'products.id', '=', 'transaction_items.product_id')
            ->where('transactions.status', 'completed')
            ->where('products.is_consignment', true)
            ->whereYear('transactions.created_at', $currentYear)
            ->selectRaw('
                MONTH(transactions.created_at) as month,
                products.consignor_type,
                products.consignor_id,
                SUM(transaction_items.quantity * transaction_items.price) as total_sales,
                SUM(transaction_items.quantity * (transaction_items.price - products.cost)) as total_profit
            ')
            ->groupBy('month', 'products.consignor_type', 'products.consignor_id')
            ->get();
            
        $monthlySupplierData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlySupplierData[$i] = [];
        }

        foreach ($monthlySupplierDataQuery as $row) {
            $name = 'Unknown';
            if ($row->consignor_type === 'supplier') {
                $supplier = \App\Models\Supplier::find($row->consignor_id);
                if ($supplier) $name = $supplier->name . ' (Supplier)';
            } elseif ($row->consignor_type === 'member') {
                $member = \App\Models\Member::with('user')->find($row->consignor_id);
                if ($member && $member->user) $name = $member->user->name . ' (Anggota)';
            }
            $monthlySupplierData[$row->month][] = [
                'name' => $name,
                'sales' => (float)$row->total_sales,
                'profit' => (float)$row->total_profit
            ];
        }

        foreach ($monthlySupplierData as $month => &$suppliers) {
            usort($suppliers, function($a, $b) {
                return $b['profit'] <=> $a['profit'];
            });
            $suppliers = array_slice($suppliers, 0, 10);
        }

        return view('commerce.consignment.dashboard', compact(
            'totalUnpaid',
            'totalSettledMonth',
            'stockValue',
            'inboundMonth',
            'returnMonth',
            'realtimeConsignment',
            'monthlySales',
            'monthlyProfit',
            'topSuppliersBySales',
            'topSuppliersByProfit',
            'pendingSettlements',
            'pendingSettlementsCount',
            'highestStockValue',
            'monthlySupplierData'
        ));
    }
}
