<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\MemberAspiration;
use Illuminate\Support\Facades\Auth;

class MemberAspirationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->hasAdminAccess()) {
            $aspirations = MemberAspiration::with('member.user')->latest()->paginate(20);
            
            // Statistics for Admin Dashboard
            $systemEvalData = MemberAspiration::where('type', 'system_eval')->get();
            $itemRequestData = MemberAspiration::where('type', 'item_request')->get();

            $stats = [
                'system_pref' => [
                    'digital' => 0,
                    'manual' => 0,
                ],
                'payment_pref' => [
                    'digital' => 0,
                    'cash' => 0,
                ],
                'top_items' => []
            ];

            foreach ($systemEvalData as $eval) {
                if (isset($eval->data['system_choice'])) {
                    $stats['system_pref'][$eval->data['system_choice']]++;
                }
                if (isset($eval->data['payment_choice'])) {
                    $stats['payment_pref'][$eval->data['payment_choice']]++;
                }
            }

            $items = [];
            foreach ($itemRequestData as $req) {
                if (isset($req->data['item_name'])) {
                    $itemName = ucwords(strtolower(trim($req->data['item_name'])));
                    
                    if (!isset($items[$itemName])) {
                        $items[$itemName] = [
                            'count' => 0,
                            'total_potential' => 0
                        ];
                    }
                    
                    $items[$itemName]['count']++;
                    
                    // Calculate Potential
                    $qty = (int)($req->data['qty'] ?? 1);
                    $price = (int)($req->data['estimated_price'] ?? 0);
                    $freq = $req->data['frequency'] ?? 'Bulanan';
                    
                    $multiplier = match($freq) {
                        'Harian' => 30,
                        'Mingguan' => 4,
                        'Bulanan' => 1,
                        default => 1
                    };
                    
                    $items[$itemName]['total_potential'] += ($qty * $price * $multiplier);
                }
            }
            // Sort by Total Potential (Omset) DESC
            uasort($items, function($a, $b) {
                return $b['total_potential'] <=> $a['total_potential'];
            });
            
            $stats['top_items'] = array_slice($items, 0, 10);

            return view('aspirations.admin_index', compact('aspirations', 'stats'));
        }

        $aspirations = MemberAspiration::where('member_id', $user->member->id)->latest()->get();
        return view('aspirations.index', compact('aspirations'));
    }

    public function create()
{
    $categories = \App\Models\Category::orderBy('name')->get();
    // Ambil Nama Produk beserta Kategori-nya untuk fitur Auto-Fill
    $products = \App\Models\Product::join('categories', 'products.category_id', '=', 'categories.id')
        ->select('products.name', 'categories.name as category_name')
        ->orderBy('products.name')
        ->get();
    
    return view('aspirations.create', compact('categories', 'products'));
}
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->member) {
            return back()->with('error', 'Hanya anggota yang dapat memberikan aspirasi.');
        }

        $request->validate([
            'type' => 'required|in:item_request,system_eval',
            'data' => 'required|array',
        ]);

        MemberAspiration::create([
            'member_id' => $user->member->id,
            'type' => $request->type,
            'data' => $request->data,
        ]);

        return redirect()->route('aspirations.index')->with('success', 'Terima kasih! Aspirasi Anda telah kami terima.');
    }
}
