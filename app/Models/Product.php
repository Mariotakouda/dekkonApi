<?php

namespace App\Models;

use App\Enums\ProductStatus;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'sku',
        'price',
        'compare_at_price',
        'cost_price',
        'status',
        'brand',
        'weight',
        'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'compare_at_price' => 'decimal:2',
            'cost_price' => 'decimal:2', // ⚠️ jamais exposé côté client — filtré au niveau du ProductResource client
            'weight' => 'decimal:2',
            'status' => ProductStatus::class,
            'is_featured' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function defaultVariant(): HasOne
    {
        return $this->hasOne(ProductVariant::class)->where('is_default', true);
    }

    public function promotions(): BelongsToMany
    {
        return $this->belongsToMany(Promotion::class, 'product_promotion')
            ->withTimestamps();
    }

    public function favoritedBy(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function attributeValues(): HasMany
    {
        return $this->hasMany(ProductAttributeValue::class);
    }

    public function attributesFormatted(): array
    {
        return $this->attributeValues
            ->filter(fn($attributeValue) => $attributeValue->categoryAttribute !== null)
            ->mapWithKeys(fn($attributeValue) => [
                $attributeValue->categoryAttribute->key => [
                    'label' => $attributeValue->categoryAttribute->label,
                    'value' => $attributeValue->value,
                ],
            ])
            ->toArray();
    }

    public function scopeActive($query)
    {
        return $query->where('status', ProductStatus::ACTIVE);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Note moyenne des avis approuvés — utile pour l'affichage catalogue.
     */
    public function averageRating(): float
    {
        return round(
            $this->reviews()->where('status', 'APPROVED')->avg('rating') ?? 0,
            1
        );
    }
}
