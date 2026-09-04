<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendPushNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public readonly User $user,
        public readonly string $title,
        public readonly string $message,
        public readonly ?array $data = null,
    ) {}

    public function handle(): void
    {
        // Intégration future : Firebase Cloud Messaging (FCM) pour push Flutter.
        // Placeholder pour l'instant — la notification est déjà en base via NotificationService,
        // ce Job servira uniquement à déclencher l'envoi push mobile quand FCM sera branché.
    }
}
