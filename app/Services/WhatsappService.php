<?php
 
namespace App\Services;
 
use App\Models\AiSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
 
class WhatsappService
{
    protected $provider;
    protected $fonnteToken;
    protected $twilioSid;
    protected $twilioToken;
    protected $twilioWaNumber;
 
    public function __construct()
    {
        $this->provider = AiSetting::get('wa_provider', 'fonnte');
        $this->fonnteToken = AiSetting::get('fonnte_token');
        $this->twilioSid = AiSetting::get('twilio_sid');
        $this->twilioToken = AiSetting::get('twilio_token');
        $this->twilioWaNumber = AiSetting::get('twilio_wa_number');
    }
 
    /**
     * Send a WhatsApp message
     */
    public function sendMessage(string $target, string $message): bool
    {
        if ($this->provider === 'twilio') {
            return $this->sendViaTwilio($target, $message);
        }
        
        return $this->sendViaFonnte($target, $message);
    }

    /**
     * Send via Fonnte (Unofficial)
     */
    protected function sendViaFonnte(string $target, string $message): bool
    {
        if (empty($this->fonnteToken)) {
            Log::error('Fonnte Token not set in settings.');
            return false;
        }
 
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->fonnteToken,
            ])->post('https://api.fonnte.com/send', [
                'target' => $target,
                'message' => $message,
                'countryCode' => '62',
            ]);
 
            if ($response->successful()) {
                Log::info("WA sent via Fonnte to {$target}");
                return true;
            }
 
            Log::error('Fonnte API Error:', $response->json() ?? []);
            return false;
 
        } catch (\Exception $e) {
            Log::error('Fonnte Service Exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send via Twilio (Official WhatsApp Business API)
     */
    protected function sendViaTwilio(string $target, string $message): bool
    {
        if (empty($this->twilioSid) || empty($this->twilioToken) || empty($this->twilioWaNumber)) {
            Log::error('Twilio credentials not complete in settings.');
            return false;
        }

        try {
            // Format target number for Twilio (must be whatsapp:+62xxx)
            $formattedTarget = $this->formatForTwilio($target);
            
            $response = Http::withBasicAuth($this->twilioSid, $this->twilioToken)
                ->asForm()
                ->post("https://api.twilio.com/2010-04-01/Accounts/{$this->twilioSid}/Messages.json", [
                    'From' => "whatsapp:{$this->twilioWaNumber}",
                    'To' => "whatsapp:{$formattedTarget}",
                    'Body' => $message,
                ]);

            if ($response->successful()) {
                Log::info("WA sent via Twilio to {$formattedTarget}");
                return true;
            }

            Log::error('Twilio API Error:', $response->json() ?? []);
            return false;

        } catch (\Exception $e) {
            Log::error('Twilio Service Exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Format phone number for Twilio (must be +62xxx format)
     */
    protected function formatForTwilio(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        if (str_starts_with($phone, '0')) {
            return '+62' . substr($phone, 1);
        }
        
        if (str_starts_with($phone, '62')) {
            return '+' . $phone;
        }
        
        return '+' . $phone;
    }

    /**
     * Get current provider
     */
    public function getProvider(): string
    {
        return $this->provider;
    }
}
