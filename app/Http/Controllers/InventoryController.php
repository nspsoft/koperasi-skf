<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
     * Get low stock count (for AJAX/badge)
     */
    public function getLowStockCount()
    {
        return response()->json([
            'count' => \App\Models\Product::lowStock()->count()
        ]);
    }
}
