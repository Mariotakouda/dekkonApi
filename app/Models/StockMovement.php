<?php

namespace App\Models;

use App\Enums\StockMovementType;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StockMovement extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'product_variant_id',
        'user_id',
        'type',
        'quantity',
        'reason',
        'reference_type',
        'reference_id',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'type' => StockMovementType::class,
        ];
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation polymorphique vers l'objet à l'origine du mouvement
     * (ex: une Order pour une RESERVATION/OUT).
     */
    public function reference(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'reference_type', 'reference_id');
    }

    public function scopeOfType($query, StockMovementType $type)
    {
        return $query->where('type', $type);
    }
}
