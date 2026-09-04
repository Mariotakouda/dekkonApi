<?php

namespace App\Console\Commands;

use App\Models\Notification;
use Illuminate\Console\Command;

class CleanOldNotifications extends Command
{
    protected $signature = 'dekkon:clean-notifications {--days=90}';
    protected $description = 'Supprime les notifications lues plus vieilles que X jours';

    public function handle(): void
    {
        $days = (int) $this->option('days');

        $count = Notification::whereNotNull('read_at')
            ->where('read_at', '<', now()->subDays($days))
            ->delete();

        $this->info("{$count} notification(s) supprimée(s).");
    }
}
