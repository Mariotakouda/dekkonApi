<?php

namespace App\Models;

use App\Enums\CategoryAttributeType;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategoryAttribute extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'category_id',
        'key',
        'label',
        'type',
        'options',
        'is_required',
        'is_variant_attribute',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'type' => CategoryAttributeType::class,
            'options' => 'array',
            'is_required' => 'boolean',
            'is_variant_attribute' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function values(): HasMany
    {
        return $this->hasMany(ProductAttributeValue::class);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
