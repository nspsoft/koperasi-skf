<?php

namespace App\Http\Controllers;

use App\Models\ConsignmentReturn;
use App\Models\ConsignmentReturnItem;
use App\Models\Product;
use App\Models\Member;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConsignmentReturnController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $returns = ConsignmentReturn::with(['consignor', 'creator'])
            ->latest()
            ->paginate(15);
            
        return view('commerce.consignment.returns.index', compact('returns'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $members = Member::where('status', 'aktif')->orderBy('name')->get();
        
        $consignmentProducts = Product::where('is_consignment', true)
            ->where('is_active', true)
            ->with('consignor')
            ->get();

        return view('commerce.consignment.returns.create', compact('suppliers', 'members', 'consignmentProducts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'consignor_type' => 'required|in:supplier,member',
            'consignor_id' => 'required',
            'return_date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            $totalItems = 0;
            
            $return = ConsignmentReturn::create([
                'return_date' => $request->return_date,
                'consignor_type' => $request->consignor_type,
                'consignor_id' => $request->consignor_id,
                'notes' => $request->notes,
                'created_by' => auth()->id(),
                'total_items' => 0, // will calculate below
            ]);

            foreach ($request->items as $itemData) {
                $product = Product::findOrFail($itemData['product_id']);
                
                // Cek apakah stok mencukupi
                if ($product->stock < $itemData['quantity']) {
                    throw new \Exception("Stok tidak mencukupi untuk produk: {$product->name}. Stok tersedia: {$product->stock}");
                }

                $return->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $itemData['quantity'],
                    'notes' => $itemData['notes'] ?? null,
                ]);

                // Kurangi stok produk
                $product->decrement('stock', $itemData['quantity']);
                
                $totalItems += $itemData['quantity'];
            }

            $return->update(['total_items' => $totalItems]);

            \App\Models\AuditLog::log(
                'create',
                "Membuat retur konsinyasi: {$return->transaction_number}",
                $return
            );

            DB::commit();

            return redirect()->route('consignment.returns.show', $return)
                ->with('success', 'Retur konsinyasi berhasil disimpan dan stok produk telah dikurangi.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $return = ConsignmentReturn::with(['consignor', 'items.product', 'creator'])->findOrFail($id);
        return view('commerce.consignment.returns.show', compact('return'));
    }

    /**
     * Print the specified resource.
     */
    public function print($id)
    {
        $return = ConsignmentReturn::with(['consignor', 'items.product', 'creator'])->findOrFail($id);
        return view('commerce.consignment.returns.print', compact('return'));
    }
}
