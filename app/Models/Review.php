<?php

namespace App\Models;

use App\Enums\ReviewStatus;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'customer_id',
        'product_id',
        'order_item_id',
        'rating',
        'comment',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'status' => ReviewStatus::class,
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', ReviewStatus::APPROVED);
    }

    public function approve(): void
    {
        $this->update(['status' => ReviewStatus::APPROVED]);
    }

    public function reject(): void
    {
        $this->update(['status' => ReviewStatus::REJECTED]);
    }
}
