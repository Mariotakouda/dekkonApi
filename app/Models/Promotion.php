<?php

namespace App\Models;

use App\Enums\PromotionType;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Promotion extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'name',
        'code',
        'type',
        'value',
        'minimum_amount',
        'maximum_discount_amount',
        'starts_at',
        'ends_at',
        'usage_limit',
        'usage_count',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'type' => PromotionType::class,
            'value' => 'decimal:2',
            'minimum_amount' => 'decimal:2',
            'maximum_discount_amount' => 'decimal:2',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'usage_limit' => 'integer',
            'usage_count' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_promotion')
            ->withTimestamps();
    }

    public function orderPromotions(): HasMany
    {
        return $this->hasMany(OrderPromotion::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Vérifie si la promotion est valide à l'instant présent :
     * active, dans sa période de validité, et pas encore épuisée.
     */
    public function isValid(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $now = Carbon::now();

        if ($this->starts_at && $now->lt($this->starts_at)) {
            return false;
        }

        if ($this->ends_at && $now->gt($this->ends_at)) {
            return false;
        }

        if ($this->usage_limit !== null && $this->usage_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    public function meetsMinimumAmount(float $subtotal): bool
    {
        if ($this->minimum_amount === null) {
            return true;
        }

        return $subtotal >= (float) $this->minimum_amount;
    }

    /**
     * Calcule le montant de remise appliqué pour un sous-total donné,
     * en respectant le plafond éventuel (section 21).
     */
    public function calculateDiscount(float $subtotal): float
    {
        if (! $this->meetsMinimumAmount($subtotal)) {
            return 0.0;
        }

        $discount = match ($this->type) {
            PromotionType::PERCENTAGE => $subtotal * ((float) $this->value / 100),
            PromotionType::FIXED_AMOUNT => (float) $this->value,
        };

        if ($this->maximum_discount_amount !== null) {
            $discount = min($discount, (float) $this->maximum_discount_amount);
        }

        return min($discount, $subtotal); // jamais plus que le sous-total lui-même
    }

    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }
}
