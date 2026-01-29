<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Crypt;

class PaymentGatewayController extends Controller
{
    /**
     * Show payment gateway configuration page
     */
    public function index()
    {
        $settings = [
            'provider' => Setting::get('payment_gateway_provider', 'midtrans'),
            // Midtrans
            'midtrans_merchant_id' => Setting::get('midtrans_merchant_id', ''),
            'midtrans_client_key' => Setting::get('midtrans_client_key', ''),
            'midtrans_server_key' => $this->maskKey(Setting::get('midtrans_server_key', '')),
            'midtrans_is_production' => Setting::get('midtrans_is_production', false),
            // Tripay
            'tripay_merchant_code' => Setting::get('tripay_merchant_code', ''),
            'tripay_api_key' => $this->maskKey(Setting::get('tripay_api_key', '')),
            'tripay_private_key' => $this->maskKey(Setting::get('tripay_private_key', '')),
            'tripay_is_production' => Setting::get('tripay_is_production', false),
            // Payment methods
            'payment_methods' => json_decode(Setting::get('payment_methods_enabled', '["qris","bank_transfer","gopay"]'), true) ?? [],
        ];

        // Generate webhook URL
        $webhookUrl = route('payment.webhook');

        return view('settings.payment-gateway', compact('settings', 'webhookUrl'));
    }

    /**
     * Update payment gateway settings
     */
    public function update(Request $request)
    {
        $request->validate([
            'provider' => 'required|in:midtrans,xendit,doku,tripay',
            'payment_methods' => 'array',
        ]);

        // Save provider
        Setting::set('payment_gateway_provider', $request->provider);

        // Save Midtrans settings
        if ($request->provider === 'midtrans') {
            Setting::set('midtrans_merchant_id', $request->midtrans_merchant_id ?? '');
            Setting::set('midtrans_client_key', $request->midtrans_client_key ?? '');
            
            // Only update server key if a new one is provided (not masked)
            if ($request->midtrans_server_key && !str_contains($request->midtrans_server_key, '•')) {
                Setting::set('midtrans_server_key', $request->midtrans_server_key);
            }
            
            Setting::set('midtrans_is_production', $request->boolean('midtrans_is_production'));
        }

        // Save Tripay settings
        if ($request->provider === 'tripay') {
            Setting::set('tripay_merchant_code', $request->tripay_merchant_code ?? '');
            
            // Only update keys if new ones are provided (not masked)
            if ($request->tripay_api_key && !str_contains($request->tripay_api_key, '•')) {
                Setting::set('tripay_api_key', $request->tripay_api_key);
            }
            if ($request->tripay_private_key && !str_contains($request->tripay_private_key, '•')) {
                Setting::set('tripay_private_key', $request->tripay_private_key);
            }
            
            Setting::set('tripay_is_production', $request->boolean('tripay_is_production'));
        }

        // Save enabled payment methods
        Setting::set('payment_methods_enabled', json_encode($request->payment_methods ?? []));

        return redirect()->back()->with('success', 'Pengaturan Payment Gateway berhasil disimpan!');
    }

    /**
     * Test connection to payment gateway
     */
    public function testConnection(Request $request)
    {
        $provider = Setting::get('payment_gateway_provider', 'midtrans');
        
        if ($provider === 'midtrans') {
            return $this->testMidtrans();
        }

        if ($provider === 'tripay') {
            return $this->testTripay();
        }

        return response()->json([
            'success' => false,
            'message' => 'Provider belum didukung untuk test connection.'
        ]);
    }

    /**
     * Test Midtrans connection
     */
    private function testMidtrans()
    {
        $serverKey = Setting::get('midtrans_server_key', '');
        $isProduction = Setting::get('midtrans_is_production', false);

        if (empty($serverKey)) {
            return response()->json([
                'success' => false,
                'message' => 'Server Key belum dikonfigurasi.'
            ]);
        }

        try {
            $baseUrl = $isProduction 
                ? 'https://api.midtrans.com' 
                : 'https://api.sandbox.midtrans.com';

            // Test by calling a simple endpoint
            $response = Http::withBasicAuth($serverKey, '')
                ->timeout(10)
                ->get($baseUrl . '/v2/point_inquiry/test-order-123');

            // Midtrans returns 404 for non-existent order, but that means auth worked
            if ($response->status() === 404 || $response->status() === 200) {
                return response()->json([
                    'success' => true,
                    'message' => 'Koneksi berhasil! API Key valid.',
                    'environment' => $isProduction ? 'Production' : 'Sandbox'
                ]);
            }

            // Check if unauthorized
            if ($response->status() === 401) {
                return response()->json([
                    'success' => false,
                    'message' => 'Server Key tidak valid atau tidak memiliki akses.'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Response tidak dikenali: ' . $response->status()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal terhubung: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Test Tripay connection
     */
    private function testTripay()
    {
        $apiKey = Setting::get('tripay_api_key', '');
        $isProduction = Setting::get('tripay_is_production', false);

        if (empty($apiKey)) {
            return response()->json([
                'success' => false,
                'message' => 'API Key belum dikonfigurasi.'
            ]);
        }

        try {
            $baseUrl = $isProduction 
                ? 'https://tripay.co.id/api' 
                : 'https://tripay.co.id/api-sandbox';

            // Test by getting payment channels
            $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey
                ])
                ->timeout(10)
                ->get($baseUrl . '/merchant/payment-channel');

            if ($response->successful()) {
                $data = $response->json();
                if ($data['success'] ?? false) {
                    $channelCount = count($data['data'] ?? []);
                    return response()->json([
                        'success' => true,
                        'message' => "Koneksi berhasil! {$channelCount} channel pembayaran tersedia.",
                        'environment' => $isProduction ? 'Production' : 'Sandbox'
                    ]);
                }
            }

            // Check if unauthorized
            if ($response->status() === 401) {
                return response()->json([
                    'success' => false,
                    'message' => 'API Key tidak valid.'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Response: ' . ($response->json()['message'] ?? 'Unknown error')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal terhubung: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Mask sensitive key for display
     */
    private function maskKey($key)
    {
        if (empty($key) || strlen($key) < 10) {
            return $key;
        }

        return substr($key, 0, 8) . str_repeat('•', strlen($key) - 12) . substr($key, -4);
    }

    /**
     * Get active payment methods for checkout
     */
    public static function getEnabledPaymentMethods()
    {
        $methods = json_decode(Setting::get('payment_methods_enabled', '[]'), true);
        return $methods ?? [];
    }

    /**
     * Check if payment gateway is configured
     */
    public static function isConfigured()
    {
        $provider = Setting::get('payment_gateway_provider', 'midtrans');
        
        if ($provider === 'midtrans') {
            return !empty(Setting::get('midtrans_server_key', ''));
        }
        
        if ($provider === 'tripay') {
            return !empty(Setting::get('tripay_api_key', ''));
        }
        
        return false;
    }

    /**
     * Get current provider
     */
    public static function getProvider()
    {
        return Setting::get('payment_gateway_provider', 'midtrans');
    }
}

