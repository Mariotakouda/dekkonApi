<?php

namespace App\Models;

use App\Enums\CartStatus;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'customer_id',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => CartStatus::class,
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', CartStatus::ACTIVE);
    }

    public function isActive(): bool
    {
        return $this->status === CartStatus::ACTIVE;
    }

    /**
     * Recalcul du sous-total à partir des variantes réelles (jamais des prix figés en cache_items).
     * Section 14 : "Le prix affiché dans le panier n'est pas considéré comme une source de vérité."
     */
    public function calculatedSubtotal(): float
    {
        return $this->items->sum(
            fn (CartItem $item) => $item->variant->effective_price * $item->quantity
        );
    }
}
