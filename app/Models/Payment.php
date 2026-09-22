<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Payment extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'order_id',
        'transaction_reference',
        'provider',
        'method',
        'phone_number',
        'status',
        'amount',
        'paid_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'method' => PaymentMethod::class,
            'status' => PaymentStatus::class,
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function scopeSuccessful($query)
    {
        return $query->where('status', PaymentStatus::SUCCESS);
    }

    public function markAsSuccessful(): void
    {
        $this->update([
            'status' => PaymentStatus::SUCCESS,
            'paid_at' => Carbon::now(),
        ]);
    }

    public function markAsFailed(): void
    {
        $this->update(['status' => PaymentStatus::FAILED]);
    }

    public function isSuccessful(): bool
    {
        return $this->status === PaymentStatus::SUCCESS;
    }

    public function requiresGateway(): bool
    {
        return $this->method->requiresGateway();
    }
}
