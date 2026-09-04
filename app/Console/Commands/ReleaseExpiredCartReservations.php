<?php

namespace App\Console\Commands;

use App\Jobs\ReleaseExpiredCartReservationsJob;
use Illuminate\Console\Command;

class ReleaseExpiredCartReservations extends Command
{
    protected $signature = 'dekkon:release-expired-carts';
    protected $description = 'Marque les paniers actifs inactifs depuis trop longtemps comme abandonnés';

    public function handle(): void
    {
        ReleaseExpiredCartReservationsJob::dispatchSync();
        $this->info('Paniers expirés traités.');
    }
}
