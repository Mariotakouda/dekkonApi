<?php

namespace App\Actions\Delivery;

use App\Enums\DriverStatus;
use App\Exceptions\Delivery\DriverNotAvailableException;
use App\Models\Delivery;
use App\Models\Driver;
use Illuminate\Support\Facades\DB;

class AssignDriverAction
{
    public function execute(Delivery $delivery, Driver $driver): Delivery
    {
        return DB::transaction(function () use ($delivery, $driver) {
            if (! $driver->isAvailable()) {
                throw new DriverNotAvailableException();
            }

            $delivery->assignDriver($driver);
            $driver->update(['status' => DriverStatus::BUSY]);

            return $delivery->fresh('driver');
        });
    }
}
