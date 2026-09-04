<?php

namespace App\Models;

use App\Enums\DeliveryStatus;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Delivery extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'order_id',
        'driver_id',
        'status',
        'delivery_fee',
        'assigned_at',
        'picked_up_at',
        'out_for_delivery_at',
        'delivered_at',
        'failed_at',
        'failure_reason',
    ];

    protected function casts(): array
    {
        return [
            'status' => DeliveryStatus::class,
            'delivery_fee' => 'decimal:2',
            'assigned_at' => 'datetime',
            'picked_up_at' => 'datetime',
            'out_for_delivery_at' => 'datetime',
            'delivered_at' => 'datetime',
            'failed_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function assignDriver(Driver $driver): void
    {
        $this->update([
            'driver_id' => $driver->id,
            'status' => DeliveryStatus::ASSIGNED,
            'assigned_at' => Carbon::now(),
        ]);
    }

    public function markAsPickedUp(): void
    {
        $this->update([
            'status' => DeliveryStatus::PICKED_UP,
            'picked_up_at' => Carbon::now(),
        ]);
    }

    public function markAsOutForDelivery(): void
    {
        $this->update([
            'status' => DeliveryStatus::OUT_FOR_DELIVERY,
            'out_for_delivery_at' => Carbon::now(),
        ]);
    }

    public function markAsDelivered(): void
    {
        $this->update([
            'status' => DeliveryStatus::DELIVERED,
            'delivered_at' => Carbon::now(),
        ]);
    }

    public function markAsFailed(string $reason): void
    {
        $this->update([
            'status' => DeliveryStatus::FAILED,
            'failed_at' => Carbon::now(),
            'failure_reason' => $reason,
        ]);
    }

    public function hasDriver(): bool
    {
        return ! is_null($this->driver_id);
    }
}
