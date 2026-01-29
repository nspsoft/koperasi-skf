<?php

namespace App\Http\Controllers;

use App\Models\ConsignmentInbound;
use App\Models\ConsignmentInboundItem;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User; // For Members
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConsignmentInboundController extends Controller
{
    public function index()
    {
        $inbounds = ConsignmentInbound::with(['consignor', 'creator', 'items'])
            ->latest()
            ->paginate(10);
            
        return view('commerce.consignment.inbounds.index', compact('inbounds'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        // For members, we might want a search or list. For now, we rely on manual ID/Name input or AJAX.
        // Let's pass populated Suppliers and maybe products for initial load?
        // Actually, products should be loaded via AJAX based on selected consignor for better UX.
        // Or pass all consignment products and filter in JS.
        $consignmentProducts = Product::where('is_consignment', true)->get();
        
        return view('commerce.consignment.inbounds.create', compact('suppliers', 'consignmentProducts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'consignor_type' => 'required|in:member,supplier',
            'consignor_id' => 'required|integer',
            'inbound_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            // Create Header
            $inbound = ConsignmentInbound::create([
                'consignor_type' => $request->consignor_type,
                'consignor_id' => $request->consignor_id,
                'inbound_date' => $request->inbound_date,
                'note' => $request->note,
                'status' => 'completed', // Direct completion
            ]);

            // Create Items & Update Stock
            foreach ($request->items as $itemData) {
                $product = Product::findOrFail($itemData['product_id']);
                
                // Safety check: specific consignor matching? 
                // For flexibility, we allow inbound even if product consignor mismatch (maybe overriding?), 
                // but ideally we should block. For now, trust the user selection.
                
                ConsignmentInboundItem::create([
                    'consignment_inbound_id' => $inbound->id,
                    'product_id' => $product->id,
                    'quantity' => $itemData['quantity'],
                    'unit_cost' => $product->consignment_price ?? 0, // Snapshot cost
                ]);

                // Update Stock
                $product->increment('stock', $itemData['quantity']);
            }

            // Audit
            \App\Models\AuditLog::log(
                'create',
                "Menerima barang konsinyasi: {$inbound->transaction_number}",
                $inbound
            );

            DB::commit();

            return redirect()->route('consignment.inbounds.index')
                ->with('success', __('messages.consignment.inbound.success_created'));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', __('messages.consignment.inbound.error_create', ['error' => $e->getMessage()]))->withInput();
        }
    }
    
    public function show(ConsignmentInbound $inbound)
    {
        $inbound->load(['items.product', 'consignor', 'creator']);
        return view('commerce.consignment.inbounds.show', compact('inbound'));
    }
}
