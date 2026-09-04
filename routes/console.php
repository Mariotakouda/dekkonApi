<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

use Illuminate\Support\Facades\Schedule;

Schedule::command('dekkon:release-expired-carts')->everyFifteenMinutes();
Schedule::command('dekkon:cancel-unpaid-orders')->hourly();
Schedule::command('dekkon:check-low-stock')->daily();
Schedule::command('dekkon:clean-notifications')->weekly();
