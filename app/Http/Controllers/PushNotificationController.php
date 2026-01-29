<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PushSubscription;

class PushNotificationController extends Controller
{
    /**
     * Get VAPID public key
     */
    public function vapidPublicKey()
    {
        return response()->json([
            'publicKey' => config('webpush.vapid.public_key')
        ]);
    }

    /**
     * Subscribe user to push notifications
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|url',
            'keys.auth' => 'required|string',
            'keys.p256dh' => 'required|string',
        ]);

        $user = Auth::user();

        // Save or update subscription
        PushSubscription::updateOrCreate(
            [
                'user_id' => $user->id,
                'endpoint' => $request->endpoint,
            ],
            [
                'public_key' => $request->keys['p256dh'],
                'auth_token' => $request->keys['auth'],
                'content_encoding' => $request->contentEncoding ?? 'aesgcm',
            ]
        );

        return response()->json(['success' => true, 'message' => 'Subscribed to push notifications']);
    }

    /**
     * Unsubscribe user from push notifications
     */
    public function unsubscribe(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|url',
        ]);

        PushSubscription::where('user_id', Auth::id())
            ->where('endpoint', $request->endpoint)
            ->delete();

        return response()->json(['success' => true, 'message' => 'Unsubscribed from push notifications']);
    }

    /**
     * Send push notification to a user (for testing)
     */
    public function sendTest(Request $request)
    {
        $user = Auth::user();
        
        $subscriptions = PushSubscription::where('user_id', $user->id)->get();
        
        if ($subscriptions->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No subscriptions found']);
        }
        
        $payload = json_encode([
            'title' => 'Test Notification 🔔',
            'body' => 'Push notification berhasil! Ini adalah test.',
            'icon' => '/icons/icon-192x192.png',
            'url' => '/dashboard'
        ]);
        
        $sent = 0;
        foreach ($subscriptions as $subscription) {
            try {
                $this->sendPushNotification($subscription, $payload);
                $sent++;
            } catch (\Exception $e) {
                // Remove invalid subscription
                $subscription->delete();
            }
        }
        
        return response()->json([
            'success' => true, 
            'message' => "Push notification sent to {$sent} device(s)"
        ]);
    }

    /**
     * Send push notification using Web Push protocol
     */
    protected function sendPushNotification(PushSubscription $subscription, string $payload)
    {
        $auth = [
            'VAPID' => [
                'subject' => config('app.url'),
                'publicKey' => config('webpush.vapid.public_key'),
                'privateKey' => config('webpush.vapid.private_key'),
            ],
        ];
        
        $webPush = new \Minishlink\WebPush\WebPush($auth);
        
        $sub = \Minishlink\WebPush\Subscription::create([
            'endpoint' => $subscription->endpoint,
            'publicKey' => $subscription->public_key,
            'authToken' => $subscription->auth_token,
            'contentEncoding' => $subscription->content_encoding,
        ]);
        
        $report = $webPush->sendOneNotification($sub, $payload);
        
        if (!$report->isSuccess()) {
            throw new \Exception('Push notification failed: ' . $report->getReason());
        }
        
        return true;
    }

    /**
     * Send notification to all subscribers of a user (used by system)
     */
    public static function notifyUser($userId, $title, $body, $url = '/dashboard', $icon = null)
    {
        $subscriptions = PushSubscription::where('user_id', $userId)->get();
        
        if ($subscriptions->isEmpty()) {
            return false;
        }
        
        $payload = json_encode([
            'title' => $title,
            'body' => $body,
            'icon' => $icon ?? '/icons/icon-192x192.png',
            'url' => $url
        ]);
        
        $controller = new self();
        
        foreach ($subscriptions as $subscription) {
            try {
                $controller->sendPushNotification($subscription, $payload);
            } catch (\Exception $e) {
                $subscription->delete();
            }
        }
        
        return true;
    }
}
