<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventory extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'product_variant_id',
        'quantity',
        'reserved_quantity',
        'low_stock_threshold',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'reserved_quantity' => 'integer',
        'low_stock_threshold' => 'integer',
    ];

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    /**
     * Stock réellement disponible à la vente (section 12).
     * Ex: quantity=20, reserved=3 → disponible=17.
     */
    public function getAvailableQuantityAttribute(): int
    {
        return max(0, $this->quantity - $this->reserved_quantity);
    }

    public function isLowStock(): bool
    {
        return $this->available_quantity <= $this->low_stock_threshold;
    }

    public function canReserve(int $quantity): bool
    {
        return $this->available_quantity >= $quantity;
    }

    public function scopeLowStock($query)
    {
        return $query->whereRaw('(quantity - reserved_quantity) <= low_stock_threshold');
    }
}
