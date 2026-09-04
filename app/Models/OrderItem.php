<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderItem extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'order_id',
        'product_variant_id',
        'product_name',
        'sku',
        'quantity',
        'unit_price',
        'discount_amount',
        'total_amount',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Nullable : la variante peut avoir été supprimée depuis,
     * le snapshot (product_name, sku, unit_price) reste la source de vérité historique.
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    public function hasBeenReviewed(): bool
    {
        return $this->review()->exists();
    }
}
