<?php
 
namespace App\Services;
 
use App\Models\User;
use App\Models\AiSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
 
class WhatsappAssistantService
{
    protected $waService;
 
    public function __construct(WhatsappService $waService)
    {
        $this->waService = $waService;
    }
 
    /**
     * Handle incoming message from WhatsApp
     */
    public function handleIncomingMessage(string $from, string $message): void
    {
        // 1. Find User by Phone
        $cleanPhone = $this->formatToLocalPhone($from);
        $user = User::with('member')->where('phone', 'like', "%{$cleanPhone}%")->first();
 
        if (!$user || !$user->member) {
            $this->waService->sendMessage($from, "Maaf, nomor Anda belum terdaftar sebagai anggota unit Koperasi. Silakan hubungi Admin untuk pendaftaran.");
            return;
        }
 
        // 2. Prepare Context
        $context = $this->prepareUserContext($user);
        $config = AiSetting::getConfig();
        $systemPrompt = $context . "\n\n" . $config['systemPrompt'];
 
        // 3. Call AI
        $aiResponse = $this->getAiResponse($systemPrompt, $message);
 
        // 4. Send Back to WA
        $this->waService->sendMessage($from, $aiResponse);
    }
 
    /**
     * Format international phone (628...) to local (08...) for DB search
     */
    protected function formatToLocalPhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($phone, '62')) {
            return '0' . substr($phone, 2);
        }
        return $phone;
    }
 
    /**
     * Prepare financial context for the member
     */
    protected function prepareUserContext(User $user): string
    {
        $member = $user->member;
        $context = "Konteks Anggota ({$user->name}):\n";
        $context .= "- ID Anggota: {$member->member_id}\n";
        $context .= "- Divisi: {$member->department}\n";
        $context .= "- Saldo Simpanan Pokok: Rp " . number_format($member->total_simpanan_pokok, 0, ',', '.') . "\n";
        $context .= "- Saldo Simpanan Wajib: Rp " . number_format($member->total_simpanan_wajib, 0, ',', '.') . "\n";
        $context .= "- Saldo Simpanan Sukarela: Rp " . number_format($member->total_simpanan_sukarela, 0, ',', '.') . "\n";
        $context .= "- Total Simpanan: Rp " . number_format($member->total_simpanan, 0, ',', '.') . "\n";
        
        $activeLoans = $member->loans()->where('status', 'active')->get();
        if ($activeLoans->count() > 0) {
            $context .= "- Pinjaman Aktif:\n";
            foreach ($activeLoans as $loan) {
                $context .= "  * Pinjaman {$loan->loan_id}: Sisa Rp " . number_format($loan->remaining_amount, 0, ',', '.') . "\n";
            }
        } else {
            $context .= "- Tidak ada pinjaman aktif.\n";
        }
        
        $context .= "- Limit Belanja Mart: Rp " . number_format($member->credit_limit, 0, ',', '.') . "\n";
        $context .= "- Sisa Limit Belanja: Rp " . number_format($member->credit_available, 0, ',', '.') . "\n";
        $context .= "- Poin Koperasi: {$member->points} Poin\n";
 
        $context .= "\nINSTRUKSI BOT WA:\n";
        $context .= "Jawablah pertanyaan anggota di atas dengan ramah dan akurat menggunakan data tersebut. Jangan memberikan data anggota lain. Jika data tidak tersedia, katakan dengan sopan.";
        
        return $context;
    }
 
    /**
     * Get response from configured AI provider
     */
    protected function getAiResponse(string $systemPrompt, string $userMessage): string
    {
        $config = AiSetting::getConfig();
        $provider = $config['provider'];
 
        try {
            if ($provider === 'ollama') {
                $response = Http::timeout(120)->post("{$config['url']}/api/generate", [
                    'model' => $config['model'],
                    'prompt' => "{$systemPrompt}\n\nAnggota: {$userMessage}\nAssistant:",
                    'stream' => false
                ]);
                return $response->json('response', 'Maaf, asisten sedang sibuk. Coba lagi nanti.');
            } 
            
            if ($provider === 'openai') {
                $response = Http::timeout(60)
                    ->withHeaders(['Authorization' => "Bearer {$config['apiKey']}"])
                    ->post('https://api.openai.com/v1/chat/completions', [
                        'model' => $config['model'],
                        'messages' => [
                            ['role' => 'system', 'content' => $systemPrompt],
                            ['role' => 'user', 'content' => $userMessage]
                        ]
                    ]);
                return $response->json('choices.0.message.content', 'Maaf, asisten sedang sibuk.');
            }
 
            return "Maaf, sistem AI belum dikonfigurasi dengan benar.";
 
        } catch (\Exception $e) {
            Log::error('WA AI Assistant Error: ' . $e->getMessage());
            return "Maaf, terjadi gangguan teknis saat menghubungi otak AI.";
        }
    }
}
