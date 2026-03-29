<?php

namespace App\Console\Commands;

use App\Models\MedicationReminder;
use App\Models\PushSubscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class SendMedicationReminders extends Command
{
    protected $signature = 'reminders:send';
    protected $description = 'Send push notifications for medication reminders due in the current minute';

    public function handle(): int
    {
        $currentTime = now()->format('H:i');

        $reminders = MedicationReminder::where('is_active', true)
            ->whereRaw("TIME_FORMAT(reminder_time, '%H:%i') = ?", [$currentTime])
            ->with('patient.user')
            ->get();

        if ($reminders->isEmpty()) {
            $this->info("No reminders due at {$currentTime}.");
            return self::SUCCESS;
        }

        $vapidPublicKey = config('webpush.vapid.public_key');
        $vapidPrivateKey = config('webpush.vapid.private_key');

        if (!$vapidPublicKey || !$vapidPrivateKey) {
            $this->warn('VAPID keys not configured. Skipping push notifications.');
            return self::SUCCESS;
        }

        $auth = [
            'VAPID' => [
                'subject' => config('app.url'),
                'publicKey' => $vapidPublicKey,
                'privateKey' => $vapidPrivateKey,
            ],
        ];

        try {
            $webPush = new WebPush($auth);
        } catch (\Exception $e) {
            $this->error('Failed to initialize WebPush: ' . $e->getMessage());
            return self::FAILURE;
        }

        $sent = 0;

        foreach ($reminders as $reminder) {
            $userId = $reminder->patient->user->id;
            $subscriptions = PushSubscription::where('user_id', $userId)->get();

            $payload = json_encode([
                'title' => 'CardioSecure',
                'body' => "E' ora di assumere il tuo farmaco: {$reminder->medication_name} ({$reminder->dosage})",
                'icon' => '/icons/icon-192x192.png',
            ]);

            foreach ($subscriptions as $sub) {
                $webPush->queueNotification(
                    Subscription::create([
                        'endpoint' => $sub->endpoint,
                        'publicKey' => $sub->public_key,
                        'authToken' => $sub->auth_token,
                    ]),
                    $payload
                );
                $sent++;
            }
        }

        foreach ($webPush->flush() as $report) {
            if (!$report->isSuccess()) {
                Log::warning('Push notification failed', [
                    'endpoint' => $report->getEndpoint(),
                    'reason' => $report->getReason(),
                ]);
            }
        }

        $this->info("Processed {$reminders->count()} reminders, queued {$sent} notifications.");

        return self::SUCCESS;
    }
}
