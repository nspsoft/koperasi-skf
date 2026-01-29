<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Display general settings.
     */
    public function index()
    {
        // Get all settings indexed by key for easy access in view
        $settings = Setting::all()->pluck('value', 'key');

        return view('settings.index', compact('settings'));
    }

    /**
     * Update settings.
     */
    public function update(Request $request)
    {
        $data = $request->except(['_token', 'logo', 'qris_image', 'hero_image', 'doc_logo_1', 'doc_logo_2']);

        try {
            DB::beginTransaction();

            // Handle Logo Upload specifically
            if ($request->hasFile('logo')) {
                $path = $request->file('logo')->store('settings', 'public');
                Setting::updateOrCreate(
                    ['key' => 'coop_logo'],
                    ['value' => $path, 'group' => 'general']
                );
            }

            // Handle Document Logo 1 Upload
            if ($request->hasFile('doc_logo_1')) {
                $path = $request->file('doc_logo_1')->store('settings', 'public');
                Setting::updateOrCreate(
                    ['key' => 'doc_logo_1'],
                    ['value' => $path, 'group' => 'document']
                );
            }

            // Handle Document Logo 2 Upload
            if ($request->hasFile('doc_logo_2')) {
                $path = $request->file('doc_logo_2')->store('settings', 'public');
                Setting::updateOrCreate(
                    ['key' => 'doc_logo_2'],
                    ['value' => $path, 'group' => 'document']
                );
            }

            // Handle QRIS Image Upload
            if ($request->hasFile('qris_image')) {
                $path = $request->file('qris_image')->store('settings', 'public');
                Setting::updateOrCreate(
                    ['key' => 'payment_qris_image'],
                    ['value' => $path, 'group' => 'payment']
                );
            }

            // Handle Landing Page Hero Image Upload
            if ($request->hasFile('hero_image')) {
                $path = $request->file('hero_image')->store('settings', 'public');
                Setting::updateOrCreate(
                    ['key' => 'landing_hero_image'],
                    ['value' => $path, 'group' => 'landing']
                );
            }

            // Loop through other fields
            foreach ($data as $key => $value) {
                Setting::updateOrCreate(
                    ['key' => $key],
                    [
                        'value' => $value,
                        'group' => $this->determineGroup($key)
                    ]
                );
            }

            DB::commit();

            \App\Models\AuditLog::log(
                'update', 
                "Memperbarui pengaturan sistem"
            );

            // Clear all caches to ensure settings update immediately
            \Cache::flush();
            \Artisan::call('cache:clear');

            return redirect()->back()->with('success', 'Pengaturan berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan pengaturan: ' . $e->getMessage());
        }
    }

    /**
     * Helper to determine group key
     */
    private function determineGroup($key)
    {
        if (str_starts_with($key, 'loan_')) return 'loan';
        if (str_starts_with($key, 'saving_')) return 'savings';
        if (str_starts_with($key, 'bank_')) return 'payment';
        if (str_starts_with($key, 'mail_')) return 'mail';
        return 'general';
    }

    /**
     * Send test email
     */
    public function testEmail(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email'
        ]);

        try {
            \Illuminate\Support\Facades\Mail::raw('Ini adalah email uji coba dari Sistem Koperasi Karyawan SPINDO. Jika Anda menerima email ini, konfigurasi Mail Server Anda sudah benar.', function ($message) use ($request) {
                $message->to($request->test_email)
                        ->subject('Test Email Konfigurasi - Koperasi SKF');
            });

            return redirect()->back()->with('success', 'Email uji coba berhasil dikirim ke ' . $request->test_email);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengirim email: ' . $e->getMessage());
        }
    }

    /**
     * Preview price changes before syncing
     */
    public function previewPrices()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $products = \App\Models\Product::with('category')->get();
        $ceiling = \App\Models\Setting::get('price_ceiling', 1000);
        
        $previews = [];
        foreach ($products as $product) {
            $costPerUnit = $product->cost_per_unit;
            $marginMultiplier = 1 + ($product->margin_percent / 100);
            $rawPrice = $costPerUnit * $marginMultiplier;
            $newPrice = ceil($rawPrice / $ceiling) * $ceiling;

            if ($newPrice != $product->price) {
                $previews[] = [
                    'id' => $product->id,
                    'code' => $product->code,
                    'name' => $product->name,
                    'category' => $product->category->name ?? '-',
                    'old_price' => $product->price,
                    'new_price' => $newPrice,
                    'diff' => $newPrice - $product->price
                ];
            }
        }

        return view('settings.preview-prices', compact('previews', 'ceiling'));
    }

    /**
     * Recalculate all product prices based on current system settings (Margin & Ceiling)
     */
    public function recalculatePrices(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            return back()->with('error', 'Hanya Admin yang dapat melakukan sinkronisasi harga massal.');
        }

        try {
            // Get selected IDs from form, or process all if empty
            $selectedIds = $request->input('product_ids', []);
            
            if (empty($selectedIds)) {
                return back()->with('error', 'Tidak ada produk yang dipilih untuk disinkronkan.');
            }

            \DB::transaction(function () use ($selectedIds) {
                $products = \App\Models\Product::whereIn('id', $selectedIds)->get();
                $ceiling = \App\Models\Setting::get('price_ceiling', 1000);

                foreach ($products as $product) {
                    $costPerUnit = $product->cost_per_unit;
                    $marginMultiplier = 1 + ($product->margin_percent / 100);
                    $rawPrice = $costPerUnit * $marginMultiplier;
                    
                    // Apply the new ceiling
                    $newPrice = ceil($rawPrice / $ceiling) * $ceiling;
                    
                    $product->update(['price' => $newPrice]);
                }
            });

            return redirect()->route('settings.index')->with('success', 'Berhasil menyinkronkan harga ' . count($selectedIds) . ' produk pilihan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyinkronkan harga: ' . $e->getMessage());
        }
    }
}
