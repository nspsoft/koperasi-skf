<?php

namespace App\Jobs;

use App\Models\Member;
use App\Models\Poll;
use App\Services\WhatsappService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendPollNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $poll;

    /**
     * Create a new job instance.
     */
    public function __construct(Poll $poll)
    {
        $this->poll = $poll;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $waService = new WhatsappService();

        // Get all active members with phone numbers
        $members = Member::where('status', 'active')
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->get();

        if ($members->isEmpty()) {
            Log::info('SendPollNotification: No active members with phone numbers found.');
            return;
        }

        $startDate = $this->poll->start_date->format('d M Y, H:i');
        $endDate = $this->poll->end_date->format('d M Y, H:i');

        $message = "🗳️ *UNDANGAN PEMILIHAN*\n\n";
        $message .= "*{$this->poll->title}*\n\n";
        $message .= "📅 Periode: {$startDate} - {$endDate}\n\n";
        $message .= "Segera gunakan hak suara Anda! Buka aplikasi Koperasi untuk memilih.\n\n";
        $message .= "- Pengurus Koperasi";

        $successCount = 0;
        $failCount = 0;

        foreach ($members as $member) {
            try {
                $result = $waService->sendMessage($member->phone, $message);
                if ($result) {
                    $successCount++;
                } else {
                    $failCount++;
                }
                // Add small delay to avoid rate limiting
                usleep(500000); // 0.5 seconds
            } catch (\Exception $e) {
                Log::error("SendPollNotification failed for {$member->phone}: " . $e->getMessage());
                $failCount++;
            }
        }

        Log::info("SendPollNotification completed: {$successCount} success, {$failCount} failed for Poll ID {$this->poll->id}");
    }
}
