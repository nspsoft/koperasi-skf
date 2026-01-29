<?php

namespace App\Http\Controllers;

use App\Models\AiSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AiSettingController extends Controller
{
    /**
     * Display AI settings page
     */
    public function index()
    {
        $settings = AiSetting::getAll();
        return view('settings.ai', compact('settings'));
    }

    /**
     * Update AI settings
     */
    public function update(Request $request)
    {
        $request->validate([
            'ai_enabled' => 'required|in:true,false',
            'ai_provider' => 'required|in:ollama,openai,gemini,custom',
            'ai_url' => 'required|url',
            'ai_model' => 'required|string|max:100',
            'ai_api_key' => 'nullable|string|max:255',
            'ai_system_prompt' => 'required|string|max:10000',
            'wa_bot_enabled' => 'required|in:true,false',
            'wa_provider' => 'nullable|in:fonnte,twilio',
            'fonnte_token' => 'nullable|string|max:255',
            'twilio_sid' => 'nullable|string|max:100',
            'twilio_token' => 'nullable|string|max:100',
            'twilio_wa_number' => 'nullable|string|max:20',
        ]);

        AiSetting::set('ai_enabled', $request->ai_enabled);
        AiSetting::set('ai_provider', $request->ai_provider);
        AiSetting::set('ai_url', $request->ai_url);
        AiSetting::set('ai_model', $request->ai_model);
        AiSetting::set('ai_api_key', $request->ai_api_key ?? '');
        AiSetting::set('ai_system_prompt', $request->ai_system_prompt);
        AiSetting::set('wa_bot_enabled', $request->wa_bot_enabled);
        AiSetting::set('wa_provider', $request->wa_provider ?? 'fonnte');
        AiSetting::set('fonnte_token', $request->fonnte_token ?? '');
        AiSetting::set('twilio_sid', $request->twilio_sid ?? '');
        AiSetting::set('twilio_token', $request->twilio_token ?? '');
        AiSetting::set('twilio_wa_number', $request->twilio_wa_number ?? '');

        return back()->with('success', 'Pengaturan AI berhasil disimpan!');
    }

    /**
     * Test connection to AI provider
     */
    public function testConnection(Request $request)
    {
        $provider = $request->provider;
        $url = $request->url;
        $apiKey = $request->api_key;

        try {
            if ($provider === 'ollama') {
                $response = Http::timeout(5)->get("{$url}/api/tags");
                if ($response->successful()) {
                    $models = collect($response->json('models', []))->pluck('name')->toArray();
                    return response()->json([
                        'success' => true,
                        'message' => 'Terhubung ke Ollama',
                        'models' => $models
                    ]);
                }
            } elseif ($provider === 'openai') {
                $response = Http::timeout(5)
                    ->withHeaders(['Authorization' => "Bearer {$apiKey}"])
                    ->get('https://api.openai.com/v1/models');
                
                if ($response->successful()) {
                    $models = collect($response->json('data', []))
                        ->filter(fn($m) => str_starts_with($m['id'], 'gpt'))
                        ->pluck('id')
                        ->toArray();
                    return response()->json([
                        'success' => true,
                        'message' => 'Terhubung ke OpenAI',
                        'models' => $models
                    ]);
                }
            } elseif ($provider === 'gemini') {
                // Test Gemini API
                $response = Http::timeout(5)
                    ->get("https://generativelanguage.googleapis.com/v1beta/models?key={$apiKey}");
                
                if ($response->successful()) {
                    $models = collect($response->json('models', []))
                        ->pluck('name')
                        ->map(fn($m) => str_replace('models/', '', $m))
                        ->toArray();
                    return response()->json([
                        'success' => true,
                        'message' => 'Terhubung ke Google Gemini',
                        'models' => $models
                    ]);
                } else {
                    $errorMsg = $response->json('error.message', 'API Key tidak valid');
                    throw new \Exception($errorMsg);
                }
            } else {
                // Custom provider - just check if URL is reachable
                $response = Http::timeout(5)->get($url);
                return response()->json([
                    'success' => $response->successful(),
                    'message' => $response->successful() ? 'URL dapat diakses' : 'URL tidak dapat diakses',
                    'models' => []
                ]);
            }

            throw new \Exception('Koneksi gagal');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'models' => []
            ]);
        }
    }

    /**
     * Get AI settings for frontend (API)
     */
    public function getSettings()
    {
        return response()->json(AiSetting::getConfig());
    }

    /**
     * Test Fonnte connection
     */
    public function testFonnte(Request $request)
    {
        $token = $request->input('token');

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak boleh kosong'
            ]);
        }

        try {
            // Call Fonnte device status endpoint
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->post('https://api.fonnte.com/device');

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['status']) && $data['status'] === true) {
                    $deviceName = $data['device'] ?? 'Unknown';
                    $quota = $data['quota'] ?? 'N/A';
                    return response()->json([
                        'success' => true,
                        'message' => "✅ Koneksi berhasil! Device: {$deviceName}, Quota: {$quota}"
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => '❌ Token tidak valid atau device tidak aktif'
                    ]);
                }
            }

            return response()->json([
                'success' => false,
                'message' => '❌ Gagal terhubung ke Fonnte API'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '❌ Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Proxy chat request to AI provider (avoids CORS issues)
     */
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $config = AiSetting::getConfig();
        
        if (!$config['enabled']) {
            return response()->json(['error' => 'AI Assistant is disabled'], 403);
        }

        $message = $request->message;
        $provider = $config['provider'];
        $url = $config['url'];
        $model = $config['model'];
        $apiKey = $config['apiKey'];
        $systemPrompt = $config['systemPrompt'];

        // Inject Financial Context if Admin
        if (auth()->user()->hasAdminAccess()) {
            $stats = [
                'total_members' => \App\Models\Member::where('status', 'active')->count(),
                'total_savings' => \App\Models\Saving::where('transaction_type', 'deposit')->sum('amount') - 
                                  \App\Models\Saving::where('transaction_type', 'withdrawal')->sum('amount'),
                'total_loans_active' => \App\Models\Loan::where('status', 'active')->sum('amount'),
                'total_outstanding' => \App\Models\Loan::where('status', 'active')->sum('remaining_amount'),
                'pending_loans' => \App\Models\Loan::where('status', 'pending')->count(),
                'total_products' => \App\Models\Product::count(),
                'monthly_revenue_mart' => \App\Models\Transaction::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->sum('total_amount'),
            ];

            $financialContext = "Konteks Keuangan Koperasi Saat Ini (Data Real-time):\n";
            $financialContext .= "- Total Anggota Aktif: " . $stats['total_members'] . "\n";
            $financialContext .= "- Total Saldo Simpanan (Semua): Rp " . number_format($stats['total_savings'], 0, ',', '.') . "\n";
            $financialContext .= "- Total Pinjaman Aktif: Rp " . number_format($stats['total_loans_active'], 0, ',', '.') . "\n";
            $financialContext .= "- Sisa Tagihan (Outstanding): Rp " . number_format($stats['total_outstanding'], 0, ',', '.') . "\n";
            $financialContext .= "- Pinjaman Menunggu Persetujuan: " . $stats['pending_loans'] . "\n";
            $financialContext .= "- Total Produk di Mart: " . $stats['total_products'] . "\n";
            $financialContext .= "- Omzet Mart Bulan Ini: Rp " . number_format($stats['monthly_revenue_mart'], 0, ',', '.') . "\n";
            $financialContext .= "\nGunakan data di atas untuk memberikan analisa keuangan jika ditanya oleh Admin.\n";

            $systemPrompt = $financialContext . "\n" . $systemPrompt;
        }

        try {
            if ($provider === 'ollama') {
                $response = Http::timeout(120)->post("{$url}/api/generate", [
                    'model' => $model,
                    'prompt' => "{$systemPrompt}\n\nUser: {$message}\nAssistant:",
                    'stream' => false
                ]);
                
                if (!$response->successful()) {
                    throw new \Exception('Ollama tidak merespons: ' . $response->status());
                }
                
                return response()->json([
                    'success' => true,
                    'response' => $response->json('response', 'Tidak ada respons')
                ]);
                
            } elseif ($provider === 'openai') {
                $response = Http::timeout(60)
                    ->withHeaders(['Authorization' => "Bearer {$apiKey}"])
                    ->post('https://api.openai.com/v1/chat/completions', [
                        'model' => $model,
                        'messages' => [
                            ['role' => 'system', 'content' => $systemPrompt],
                            ['role' => 'user', 'content' => $message]
                        ]
                    ]);
                
                if (!$response->successful()) {
                    throw new \Exception('OpenAI error: ' . $response->json('error.message', 'Unknown error'));
                }
                
                return response()->json([
                    'success' => true,
                    'response' => $response->json('choices.0.message.content', 'Tidak ada respons')
                ]);
                
            } elseif ($provider === 'gemini') {
                // Google Gemini API
                $response = Http::timeout(60)
                    ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
                        'contents' => [
                            [
                                'parts' => [
                                    ['text' => "{$systemPrompt}\n\nUser: {$message}\n\nPlease respond in Indonesian."]
                                ]
                            ]
                        ],
                        'generationConfig' => [
                            'temperature' => 0.7,
                            'maxOutputTokens' => 1024,
                        ]
                    ]);
                
                if (!$response->successful()) {
                    $errorMsg = $response->json('error.message', 'Unknown error');
                    throw new \Exception('Gemini error: ' . $errorMsg);
                }
                
                $text = $response->json('candidates.0.content.parts.0.text', 'Tidak ada respons');
                
                return response()->json([
                    'success' => true,
                    'response' => $text
                ]);
                
            } else {
                // Custom provider
                $response = Http::timeout(60)->post("{$url}/generate", [
                    'prompt' => "{$systemPrompt}\n\nUser: {$message}\nAssistant:"
                ]);
                
                $data = $response->json();
                return response()->json([
                    'success' => true,
                    'response' => $data['response'] ?? $data['text'] ?? $data['output'] ?? 'Tidak ada respons'
                ]);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
