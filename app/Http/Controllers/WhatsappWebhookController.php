<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use App\Services\WhatsappAssistantService;
use App\Models\AiSetting;
use Illuminate\Support\Facades\Log;
 
class WhatsappWebhookController extends Controller
{
    protected $assistantService;
 
    public function __construct(WhatsappAssistantService $assistantService)
    {
        $this->assistantService = $assistantService;
    }
 
    /**
     * Handle webhook from Fonnte
     */
    public function handle(Request $request)
    {
        // Check if Bot is enabled
        if (AiSetting::get('wa_bot_enabled', 'false') !== 'true') {
            return response()->json(['status' => 'disabled']);
        }

        // Check if Fonnte is the selected provider
        if (AiSetting::get('wa_provider', 'fonnte') !== 'fonnte') {
            return response()->json(['status' => 'wrong_provider']);
        }
 
        // Data from Fonnte
        $sender = $request->input('sender');      // Target/Recipient number
        $receiver = $request->input('receiver');  // Your Fonnte number
        $message = $request->input('message');    // The message text
 
        // Log incoming for debugging
        Log::info("WA Webhook (Fonnte) from {$sender}: {$message}");
 
        if (!$sender || !$message) {
            return response()->json(['status' => 'invalid_data']);
        }
 
        // Process the message
        $this->assistantService->handleIncomingMessage($sender, $message);
 
        return response()->json(['status' => 'success']);
    }

    /**
     * Handle webhook from Twilio
     */
    public function handleTwilio(Request $request)
    {
        // Check if Bot is enabled
        if (AiSetting::get('wa_bot_enabled', 'false') !== 'true') {
            return response('disabled', 200);
        }

        // Check if Twilio is the selected provider
        if (AiSetting::get('wa_provider', 'fonnte') !== 'twilio') {
            return response('wrong_provider', 200);
        }

        // Data from Twilio webhook
        // Twilio sends: From, To, Body, NumMedia, etc.
        $from = $request->input('From');      // whatsapp:+6281234567890
        $to = $request->input('To');          // whatsapp:+14155238886
        $body = $request->input('Body');      // Message text

        // Extract phone number from whatsapp:+62xxx format
        $sender = $this->extractPhoneFromTwilio($from);
        $message = $body;

        // Log incoming for debugging
        Log::info("WA Webhook (Twilio) from {$sender}: {$message}");

        if (!$sender || !$message) {
            return response('invalid_data', 200);
        }

        // Process the message
        $this->assistantService->handleIncomingMessage($sender, $message);

        // Twilio expects TwiML response or empty 200
        return response('', 200)
            ->header('Content-Type', 'text/xml');
    }

    /**
     * Extract phone number from Twilio format
     * whatsapp:+6281234567890 -> 6281234567890
     */
    protected function extractPhoneFromTwilio(string $from): string
    {
        // Remove "whatsapp:" prefix and "+" sign
        $phone = str_replace(['whatsapp:', '+'], '', $from);
        return $phone;
    }
}
