<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'customer_id',
        'order_number',
        'status',
        'subtotal',
        'discount_amount',
        'delivery_fee',
        'total_amount',
        'notes',
        'placed_at',
        'confirmed_at',
        'delivered_at',
        'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'delivery_fee' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'placed_at' => 'datetime',
            'confirmed_at' => 'datetime',
            'delivered_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function address(): HasOne
    {
        return $this->hasOne(OrderAddress::class);
    }

    public function promotions(): HasMany
    {
        return $this->hasMany(OrderPromotion::class);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->latest('created_at');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function successfulPayment(): HasOne
    {
        return $this->hasOne(Payment::class)->where('status', 'SUCCESS')->latestOfMany();
    }

    public function delivery(): HasOne
    {
        return $this->hasOne(Delivery::class);
    }

    public function scopeStatus($query, OrderStatus $status)
    {
        return $query->where('status', $status);
    }

    public function canTransitionTo(OrderStatus $target): bool
    {
        return $this->status->canTransitionTo($target);
    }

    public function canBeCancelled(): bool
    {
        return $this->canTransitionTo(OrderStatus::CANCELLED);
    }

    public function isPaid(): bool
    {
        return $this->successfulPayment()->exists();
    }
}
