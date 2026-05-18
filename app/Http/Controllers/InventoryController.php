<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\TransactionItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    /**
     * Display low stock products
     */
    public function lowStock(Request $request)
    {
        if (!auth()->user()->hasAdminAccess()) abort(403);

        $query = \App\Models\Product::with('category')
            ->lowStock()
            ->orderBy('stock', 'asc');

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%');
            });
        }

        $products = $query->paginate(20);
        $totalLowStock = \App\Models\Product::lowStock()->count();

        return view('inventory.low-stock', compact('products', 'totalLowStock'));
    }

    /**
     * Quick stock update
     */
    public function updateStock(Request $request, \App\Models\Product $product)
    {
        if (!auth()->user()->hasAdminAccess()) abort(403);

        $request->validate([
            'quantity' => 'required|integer',
            'type' => 'required|in:add,set',
        ]);

        $oldStock = $product->stock;

        if ($request->type === 'add') {
            $product->increment('stock', $request->quantity);
        } else {
            $product->update(['stock' => $request->quantity]);
        }

        // Log the action
        \App\Models\AuditLog::log('update', 
            'Update stok produk ' . $product->name . ': ' . $oldStock . ' → ' . $product->fresh()->stock,
            $product
        );

        return redirect()->back()->with('success', 'Stok produk ' . $product->name . ' berhasil diupdate.');
    }

    /**
     * Inventory Dashboard
     */
    public function dashboard(Request $request)
    {
        if (!auth()->user()->hasAdminAccess()) abort(403);

        $categoryId = $request->category_id;
        $categoryFilter = function($query) use ($categoryId) {
            if ($categoryId) {
                $query->where('category_id', $categoryId);
            }
        };

        // KPI Metrics
        $totalSku = Product::where('is_active', true)->where($categoryFilter)->count();
        $totalStockValue = Product::where('is_active', true)
            ->where($categoryFilter)
            ->select(DB::raw('SUM(stock * cost) as total_value'))
            ->value('total_value') ?? 0;
            
        $lowStockCount = Product::lowStock()->where($categoryFilter)->count();
        $outOfStockCount = Product::where('is_active', true)->where($categoryFilter)->where('stock', '<=', 0)->count();

        // Stock Value by Category (For Chart)
        $stockValueByCategoryQuery = Category::with(['products' => function($q) use ($categoryFilter) {
            $q->where('is_active', true)->where($categoryFilter);
        }]);
        
        if ($categoryId) {
            $stockValueByCategoryQuery->where('id', $categoryId);
        }

        $stockValueByCategory = $stockValueByCategoryQuery->get()->map(function($category) {
            return [
                'name' => $category->name,
                'value' => $category->products->sum(function($product) {
                    return $product->stock * $product->cost;
                })
            ];
        })->filter(fn($item) => $item['value'] > 0)->values();

        // Categories for Filter
        $categories = Category::orderBy('name')->get();

        // Purchase Recommendations (Last 30 Days context)
        $thirtyDaysAgo = Carbon::now()->subDays(30);
        $recommendations = Product::with(['category', 'transactionItems' => function($q) use ($thirtyDaysAgo) {
                $q->where('created_at', '>=', $thirtyDaysAgo);
            }])
            ->where('is_active', true)
            ->where($categoryFilter)
            ->where(function($query) {
                $query->whereColumn('stock', '<=', 'min_stock')
                      ->orWhere('stock', '<', 5); 
            })
            ->orderBy('stock', 'asc')
            ->take(10)
            ->get()
            ->map(function($product) {
                $sales30Days = $product->transactionItems->sum('quantity');
                $product->daily_avg = round($sales30Days / 30, 1);
                $product->weekly_avg = round($product->daily_avg * 7, 0);
                $product->days_remaining = $product->daily_avg > 0 ? floor($product->stock / $product->daily_avg) : 999;
                return $product;
            });

        // Stock In & Out Movement Trend (Last 7 Days)
        $stockMovementTrend = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $dateLabel = Carbon::now()->subDays($i)->format('d M');
            
            // Stock In: completed Purchases items quantity + Consignment Inbounds items quantity
            $purchasesIn = \App\Models\PurchaseItem::whereHas('purchase', function($q) {
                $q->where('status', 'completed');
            })->whereDate('created_at', $date)->sum('quantity');
            
            $consignmentIn = \App\Models\ConsignmentInboundItem::whereHas('inbound', function($q) {
                $q->where('status', 'completed');
            })->whereDate('created_at', $date)->sum('quantity');
            
            $stockIn = $purchasesIn + $consignmentIn;
            
            // Stock Out: TransactionItem quantity from non-cancelled transactions
            $stockOut = \App\Models\TransactionItem::whereHas('transaction', function($q) {
                $q->whereNotIn('status', ['cancelled']);
            })->whereDate('created_at', $date)->sum('quantity');
            
            $stockMovementTrend->push([
                'date' => $dateLabel,
                'in' => (int)$stockIn,
                'out' => (int)$stockOut,
            ]);
        }

        // Recent Stock Movements
        $recentSales = TransactionItem::with('product')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Slow Moving Products (Stock > 0, Sales < 5 in last 30 days)
        $thirtyDaysAgo = Carbon::now()->subDays(30);
        $slowMoving = Product::where('is_active', true)
            ->where('stock', '>', 0)
            ->where(function($query) use ($thirtyDaysAgo) {
                $query->has('transactionItems', '<', 5, 'and', function($q) use ($thirtyDaysAgo) {
                    $q->where('created_at', '>=', $thirtyDaysAgo);
                });
            })
            ->orderBy('stock', 'desc')
            ->take(5)
            ->get();

        // Overstock Analysis (Stock > 3x min_stock AND min_stock > 0)
        $overstock = Product::where('is_active', true)
            ->where('min_stock', '>', 0)
            ->whereRaw('stock > (min_stock * 3)')
            ->orderByRaw('stock / min_stock DESC')
            ->take(5)
            ->get();

        // Top Products Data for Combo Chart (Last 30 Days)
        $thirtyDaysAgo = Carbon::now()->subDays(30);
        $topProductsQuery = TransactionItem::select(
                'transaction_items.product_id',
                'products.name',
                DB::raw('SUM(transaction_items.quantity) as total_qty'),
                DB::raw('SUM(transaction_items.subtotal) as total_revenue'),
                DB::raw('SUM(transaction_items.subtotal - (COALESCE(products.cost, 0) * transaction_items.quantity)) as total_profit')
            )
            ->join('products', 'products.id', '=', 'transaction_items.product_id')
            ->where('transaction_items.created_at', '>=', $thirtyDaysAgo);

        if ($categoryId) {
            $topProductsQuery->where('products.category_id', $categoryId);
        }

        $topProductsDataRaw = $topProductsQuery->groupBy('transaction_items.product_id', 'products.name')
            ->orderBy('total_revenue', 'desc')
            ->take(10)
            ->get();

        $totalProfitSum = $topProductsDataRaw->sum('total_profit');

        $topProductsData = $topProductsDataRaw->map(function($item) use ($totalProfitSum) {
                return [
                    'id' => $item->product_id,
                    'name' => $item->name ?? 'Unknown',
                    'qty' => (int)$item->total_qty,
                    'revenue' => (float)$item->total_revenue,
                    'profit' => (float)$item->total_profit,
                    'profit_pct' => $totalProfitSum > 0 ? round(($item->total_profit / $totalProfitSum) * 100, 1) : 0
                ];
            });

        // ABC Analysis (Based on Profit in last 30 days)
        $allProductsProfit = TransactionItem::select(
                'product_id',
                DB::raw('SUM(subtotal - (COALESCE(products.cost, 0) * transaction_items.quantity)) as total_profit')
            )
            ->join('products', 'products.id', '=', 'transaction_items.product_id')
            ->where('transaction_items.created_at', '>=', $thirtyDaysAgo)
            ->groupBy('product_id')
            ->orderBy('total_profit', 'desc')
            ->get();

        $totalGlobalProfit = $allProductsProfit->sum('total_profit');
        $runningProfit = 0;
        $abcAnalysis = ['A' => 0, 'B' => 0, 'C' => 0];
        
        foreach ($allProductsProfit as $item) {
            $runningProfit += $item->total_profit;
            $percentage = $totalGlobalProfit > 0 ? ($runningProfit / $totalGlobalProfit) * 100 : 0;
            
            $group = 'C';
            if ($percentage <= 80) $group = 'A';
            elseif ($percentage <= 95) $group = 'B';
            
            $abcAnalysis['A_list'][] = $item->product_id; // For reference if needed
            $abcAnalysis[$group]++;
            
            // Assign to top products if they exist there
            foreach ($topProductsData as &$tp) {
                if ($tp['id'] == $item->product_id) $tp['abc'] = $group;
            }
            
            // Assign to recommendations if they exist there
            foreach ($recommendations as &$rec) {
                if ($rec->id == $item->product_id) $rec->abc = $group;
            }
        }
        
        // Handle products with no sales (automatically Group C)
        foreach ($recommendations as &$rec) {
            if (!isset($rec->abc)) $rec->abc = 'C';
        }
        foreach ($topProductsData as &$tp) {
            if (!isset($tp['abc'])) $tp['abc'] = 'C';
        }

        // Fill C with products with 0 sales
        $noSalesCount = Product::where('is_active', true)->whereDoesntHave('transactionItems', function($q) use ($thirtyDaysAgo) {
            $q->where('created_at', '>=', $thirtyDaysAgo);
        })->count();
        $abcAnalysis['C'] += $noSalesCount;

        return view('inventory.dashboard', compact(
            'totalSku', 'totalStockValue', 'lowStockCount', 'outOfStockCount',
            'stockValueByCategory', 'recommendations', 'recentSales', 'stockMovementTrend',
            'slowMoving', 'overstock', 'topProductsData', 'categories', 'categoryId',
            'abcAnalysis'
        ));
    }

    /**
     * Get low stock count (for AJAX/badge)
     */
    public function getLowStockCount()
    {
        return response()->json([
            'count' => \App\Models\Product::lowStock()->count()
        ]);
    }

    /**
     * Get dynamic in-out movement breakdown for a specific product
     */
    public function productMovementBreakdown(\App\Models\Product $product)
    {
        if (!auth()->user()->hasAdminAccess()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Fetch completed purchases for this product
        $purchases = \App\Models\PurchaseItem::with(['purchase.supplier'])
            ->where('product_id', $product->id)
            ->whereHas('purchase', function($q) {
                $q->where('status', 'completed');
            })
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function($item) {
                return [
                    'type' => 'Purchase',
                    'ref' => $item->purchase->reference_number,
                    'date' => $item->purchase->purchase_date ? $item->purchase->purchase_date->format('d M Y') : $item->created_at->format('d M Y'),
                    'source' => $item->purchase->supplier->name ?? 'Supplier Umum',
                    'qty' => $item->quantity,
                    'price' => (float)$item->cost,
                ];
            });

        // Fetch consignment inbounds for this product
        $consignments = \App\Models\ConsignmentInboundItem::with(['inbound.consignor'])
            ->where('product_id', $product->id)
            ->whereHas('inbound', function($q) {
                $q->where('status', 'completed');
            })
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function($item) {
                return [
                    'type' => 'Consignment',
                    'ref' => $item->inbound->transaction_number,
                    'date' => $item->inbound->inbound_date ? $item->inbound->inbound_date->format('d M Y') : $item->created_at->format('d M Y'),
                    'source' => $item->inbound->consignor->name ?? 'Consignor',
                    'qty' => $item->quantity,
                    'price' => (float)$item->unit_cost,
                ];
            });

        // Merge Stock In (Purchases & Consignments)
        $stockInDetails = $purchases->concat($consignments)->sortByDesc('date')->values();

        // Fetch completed sales for this product
        $stockOutDetails = \App\Models\TransactionItem::with(['transaction.user'])
            ->where('product_id', $product->id)
            ->whereHas('transaction', function($q) {
                $q->whereNotIn('status', ['cancelled']);
            })
            ->orderBy('created_at', 'desc')
            ->take(15)
            ->get()
            ->map(function($item) {
                return [
                    'ref' => $item->transaction->invoice_number ?? 'INV-' . $item->transaction_id,
                    'date' => $item->created_at->format('d M Y H:i'),
                    'customer' => $item->transaction->user->name ?? 'Kasir / POS',
                    'qty' => $item->quantity,
                    'price' => (float)$item->price,
                ];
            });

        return response()->json([
            'product' => [
                'name' => $product->name,
                'code' => $product->code,
                'category' => $product->category->name ?? 'Uncategorized',
                'stock' => $product->stock,
                'unit' => $product->unit,
                'cost' => $product->cost,
                'price' => $product->price,
            ],
            'in' => $stockInDetails,
            'out' => $stockOutDetails,
            'total_in' => $stockInDetails->sum('qty'),
            'total_out' => $stockOutDetails->sum('qty'),
        ]);
    }
}
