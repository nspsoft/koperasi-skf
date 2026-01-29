<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Voucher;
use App\Models\Review;
use App\Models\Setting;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category')->where('is_active', true);
        
        if ($request->category) {
            $query->where('category_id', $request->category);
        }

        if ($request->search) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        $products = $query->latest()->paginate(12);
        $categories = Category::has('products')->get();

        return view('commerce.shop.index', compact('products', 'categories'));
    }

    public function show(Product $product)
    {
        $product->load(['reviews.user']);
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->take(4)
            ->get();

        return view('commerce.shop.show', compact('product', 'relatedProducts'));
    }

    public function storeReview(Request $request, Product $product)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000'
        ]);

        // Check if user has bought this product
        $hasBought = Transaction::where('user_id', auth()->id())
            ->whereHas('items', function($q) use ($product) {
                $q->where('product_id', $product->id);
            })
            ->where('status', 'completed')
            ->exists();

        if (!$hasBought) {
            return back()->with('error', 'Anda harus membeli produk ini terlebih dahulu untuk memberikan ulasan.');
        }

        // Check if already reviewed
        $existingReview = Review::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->first();

        if ($existingReview) {
            $existingReview->update([
                'rating' => $request->rating,
                'comment' => $request->comment
            ]);
            return back()->with('success', 'Ulasan Anda berhasil diperbarui!');
        }

        Review::create([
            'product_id' => $product->id,
            'user_id' => auth()->id(),
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);

        return back()->with('success', 'Terima kasih atas ulasan Anda!');
    }

    public function addToCart(Request $request, Product $product)
    {
        $quantity = $request->input('quantity', 1);
        $cart = session()->get('cart', []);
        
        // Initial quantity in cart
        $currentQty = isset($cart[$product->id]) ? $cart[$product->id]['quantity'] : 0;
        $newQty = $currentQty + $quantity;

        if (!$product->is_preorder && $product->stock < $newQty) {
            return redirect()->back()->with('error', 'Stok tidak mencukupi untuk jumlah tersebut.');
        }

        if(isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] = $newQty;
        } else {
            $cart[$product->id] = [
                "name" => $product->name,
                "quantity" => $quantity,
                "price" => $product->price,
                "image" => $product->image,
                "is_preorder" => $product->is_preorder,
                "preorder_eta" => $product->preorder_eta
            ];
        }
        
        session()->put('cart', $cart);
        
        // Reset voucher as cart total changed
        session()->forget('voucher');

        return redirect()->back()->with('success', 'Produk ditambahkan ke keranjang!');
    }

    public function cart()
    {
        $cart = session()->get('cart', []);
        $total = 0;
        foreach($cart as $id => $details) {
            $total += $details['price'] * $details['quantity'];
        }

        $voucher = session()->get('voucher');
        $discount = 0;
        if ($voucher) {
            $vModel = Voucher::where('code', $voucher['code'])->first();
            if ($vModel && $vModel->isValidFor($total)) {
                $discount = $vModel->calculateDiscount($total);
                $total -= $discount;
            } else {
                session()->forget('voucher');
                $voucher = null;
            }
        }

        return view('commerce.shop.cart', compact('cart', 'total', 'voucher', 'discount'));
    }

    public function applyVoucher(Request $request)
    {
        $request->validate(['code' => 'required|string']);
        
        $voucher = Voucher::where('code', strtoupper($request->code))->first();

        if (!$voucher) {
            return back()->with('error', 'Kode voucher tidak valid.');
        }

        $cart = session()->get('cart', []);
        $total = 0;
        foreach($cart as $id => $details) {
            $total += $details['price'] * $details['quantity'];
        }

        if (!$voucher->isValidFor($total)) {
            return back()->with('error', 'Voucher tidak dapat digunakan (Cek min. belanja atau masa berlaku).');
        }

        session()->put('voucher', [
            'code' => $voucher->code,
            'discount' => $voucher->calculateDiscount($total)
        ]);

        return back()->with('success', 'Voucher berhasil digunakan!');
    }

    public function updateCart(Request $request)
    {
        if($request->id && $request->quantity){
            $cart = session()->get('cart');
            $cart[$request->id]["quantity"] = $request->quantity;
            session()->put('cart', $cart);
            session()->flash('success', 'Keranjang diperbarui');
        }
    }

    public function removeFromCart(Request $request)
    {
        if($request->id) {
            $cart = session()->get('cart');
            if(isset($cart[$request->id])) {
                unset($cart[$request->id]);
                session()->put('cart', $cart);
            }
            session()->flash('success', 'Produk dihapus dari keranjang');
        }
    }

    public function checkout()
    {
        $cart = session()->get('cart');
        if(!$cart || count($cart) == 0) return redirect()->route('shop.index');

        $user = auth()->user();
        $member = $user->member;
        
        // Calculate total
        $originalTotal = 0;
        foreach($cart as $id => $details) {
            $originalTotal += $details['price'] * $details['quantity'];
        }

        $voucher = session()->get('voucher');
        $discount = 0;
        $total = $originalTotal;

        if ($voucher) {
            $vModel = Voucher::where('code', $voucher['code'])->first();
            if ($vModel && $vModel->isValidFor($originalTotal)) {
                $discount = $vModel->calculateDiscount($originalTotal);
                $total = $originalTotal - $discount;
            } else {
                session()->forget('voucher');
            }
        }

        $settings = \App\Models\Setting::whereIn('key', ['bank_name', 'bank_account_number', 'bank_account_name', 'payment_qris_image'])->pluck('value', 'key');

        return view('commerce.shop.checkout', compact('cart', 'total', 'member', 'settings', 'discount', 'originalTotal'));
    }

    public function processCheckout(Request $request)
    {
        $cart = session()->get('cart');
        if(!$cart) return redirect()->route('shop.index');

        $user = auth()->user();
        $member = $user->member;
        if(!$member) return back()->with('error', 'Hanya anggota yang bisa belanja online.');

        $originalTotal = 0;
        foreach($cart as $id => $details) {
            $originalTotal += $details['price'] * $details['quantity'];
        }

        $voucher = session()->get('voucher');
        $discount = 0;
        $total = $originalTotal;
        $voucherModel = null;

        if ($voucher) {
            $voucherModel = Voucher::where('code', $voucher['code'])->first();
            if ($voucherModel && $voucherModel->isValidFor($originalTotal)) {
                $discount = $voucherModel->calculateDiscount($originalTotal);
                $total = $originalTotal - $discount;
            }
        }

        // Points discount
        $pointsDiscount = 0;
        $pointsUsed = 0;
        if ($request->has('use_points') && $member->points > 0) {
            $pointsValue = $member->points_value;
            $pointsDiscount = min($pointsValue, $total);
            $pointsUsed = ceil($pointsDiscount / Setting::get('point_conversion_rate', 1));
            $total -= $pointsDiscount;
        }

        // Validation based on payment method
        if($request->payment_method == 'saldo_sukarela') {
             if($member->balance < $total) {
                 return back()->with('error', 'Saldo Simpanan Sukarela tidak mencukupi.');
             }
        } elseif($request->payment_method == 'kredit') {
            if($member->credit_available < $total) {
                return back()->with('error', 'Limit kredit belanja tidak mencukupi.');
            }
        }

        $transaction = null;
        $lowStockProducts = [];

        try {
            $transaction = \DB::transaction(function () use ($cart, $total, $request, $user, $member, $voucherModel, $discount, $pointsDiscount, $pointsUsed, &$lowStockProducts) {
                // Create Transaction
                $invoice = 'INV-' . date('Ymd') . '-' . strtoupper(\Str::random(4));
                
                // Status Handling
                $status = 'paid';
                $paid_amount = $total;
                
                if($request->payment_method == 'kredit') {
                    $status = 'credit';
                    $paid_amount = 0;
                } elseif(in_array($request->payment_method, ['cash_pickup', 'transfer', 'qris', 'va'])) {
                    $status = 'pending';
                    $paid_amount = 0;
                }

                // Delivery Info handling
                $deliveryInfo = ($request->delivery_method == 'delivery') 
                    ? "[ANTAR: {$request->delivery_location}]" 
                    : "[AMBIL SENDIRI]";
                
                $voucherInfo = $voucherModel ? "[VOUCHER: {$voucherModel->code} (-Rp ".number_format($discount, 0, ',', '.').")]" : "";
                
                $pointsInfo = $pointsUsed > 0 ? "[POIN: {$pointsUsed} poin (-Rp ".number_format($pointsDiscount, 0, ',', '.').")]" : "";
                
                $finalNotes = $deliveryInfo . $voucherInfo . $pointsInfo . ($request->notes ? " - " . $request->notes : "");

                $txn = Transaction::create([
                    'invoice_number' => $invoice,
                    'user_id' => $user->id,
                    'type' => 'online',
                    'status' => $status,
                    'payment_method' => $request->payment_method,
                    'total_amount' => $total,
                    'paid_amount' => $paid_amount,
                    'change_amount' => 0,
                    'notes' => $finalNotes
                ]);

                foreach($cart as $id => $details) {
                    $product = Product::lockForUpdate()->find($id);
                    
                    if(!$product->is_preorder && $product->stock < $details['quantity']) {
                        throw new \Exception("Stok {$product->name} tidak mencukupi.");
                    }

                    TransactionItem::create([
                        'transaction_id' => $txn->id,
                        'product_id' => $id,
                        'quantity' => $details['quantity'],
                        'price' => $details['price'],
                        'subtotal' => $details['price'] * $details['quantity']
                    ]);

                    if(!$product->is_preorder) {
                        $product->decrement('stock', $details['quantity']);
                        
                        // Check for low stock after decrement
                        $product->refresh();
                        if ($product->min_stock > 0 && $product->stock <= $product->min_stock) {
                            $lowStockProducts[] = $product;
                        }
                    }
                }

                // If Paid by Saldo, Create Saving Withdrawal
                if($request->payment_method == 'saldo_sukarela') {
                    $saving = \App\Models\Saving::create([
                        'member_id' => $member->id,
                        'type' => 'sukarela',
                        'transaction_date' => now(),
                        'transaction_type' => 'withdrawal',
                        'amount' => $total, // Positive absolute value
                        'description' => 'Pembayaran Belanja Online ' . $invoice,
                        'created_by' => auth()->id(),
                    ]);

                    // Create journal entry for savings withdrawal
                    \App\Services\JournalService::journalSavingWithdrawal($saving);
                }

                // Increment voucher usage
                if ($voucherModel) {
                    $voucherModel->increment('used_count');
                }

                // Deduct used points
                if ($pointsUsed > 0) {
                    $member->decrement('points', $pointsUsed);
                }

                // Award Points (only if paid/credit)
                if (in_array($status, ['paid', 'credit'])) {
                    $earnRate = Setting::get('point_earn_rate', 10000);
                    $earnedPoints = floor($total / $earnRate);
                    if ($earnedPoints > 0) {
                        $member->increment('points', $earnedPoints);
                    }
                }

                // Create Journal Entry for Financial Reports
                // Must reload items relationship so COGS can be calculated
                $txn->load('items.product');
                \App\Services\JournalService::journalSale($txn);

                return $txn;
            });

            // --- Notifications (after successful transaction) ---
            
            // 1. Notify Admin/Pengurus about new online order
            $adminUsers = \App\Models\User::whereIn('role', ['admin', 'pengurus'])->get();
            foreach ($adminUsers as $admin) {
                $admin->notify(new \App\Notifications\NewOnlineOrderNotification($transaction));
            }

            // 2. Check and notify low stock products
            foreach ($lowStockProducts as $product) {
                foreach ($adminUsers as $admin) {
                    $admin->notify(new \App\Notifications\LowStockNotification($product));
                }
            }

            session()->forget(['cart', 'voucher']);
            return redirect()->route('shop.history')->with('success', 'Pesanan berhasil dibuat!');

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function history()
    {
        $transactions = Transaction::with('items.product')->where('user_id', auth()->id())->latest()->paginate(10);
        return view('commerce.shop.history', compact('transactions'));
    }

    public function trackOrder(Transaction $transaction)
    {
        if ($transaction->user_id != auth()->id()) {
            abort(403);
        }
        $transaction->load('items.product');
        return view('commerce.shop.tracking', compact('transaction'));
    }
}
