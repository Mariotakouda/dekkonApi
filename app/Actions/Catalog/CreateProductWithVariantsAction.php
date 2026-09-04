<?php

namespace App\Actions\Catalog;

use App\DTOs\ProductCreationData;
use App\Models\CategoryAttribute;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use App\Models\ProductVariant;
use App\Support\MediaUrl;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateProductWithVariantsAction
{
    /**
     * Crée un produit, ses attributs de catégorie, ses variantes (+ stock initial) et ses images
     * en une seule transaction — l'admin n'a besoin que d'un seul appel API.
     */
    public function execute(ProductCreationData $data): Product
    {
        return DB::transaction(function () use ($data) {
            $productFields = $data->productFields;
            $productFields['slug'] = $this->generateUniqueSlug(
                $productFields['slug'] ?? Str::slug($productFields['name'])
            );

            // Le SKU est un concept d'inventaire que l'admin n'a pas toujours en tête
            // (ex: "chaussures homme", "ustensiles de cuisine") — on le génère si absent.
            if (empty($productFields['sku'])) {
                $productFields['sku'] = $this->generateUniqueSku($productFields['name'], Product::class, 'sku');
            }

            $productFields = array_filter($productFields, fn($value) => ! is_null($value));

            $product = Product::create($productFields);

            $this->storeAttributes($product, $data->attributes);
            $this->storeVariants($product, $data->variants);
            $this->storeImages($product, $data->images);

            return $product->fresh([
                'category',
                'images',
                'variants.inventory',
                'attributeValues.categoryAttribute',
            ]);
        });
    }

    protected function storeAttributes(Product $product, array $attributes): void
    {
        if (empty($attributes)) {
            return;
        }

        $categoryAttributes = CategoryAttribute::where('category_id', $product->category_id)
            ->get()
            ->keyBy('key');

        foreach ($attributes as $key => $value) {
            if (! isset($categoryAttributes[$key])) {
                continue;
            }

            $product->attributeValues()->create([
                'category_attribute_id' => $categoryAttributes[$key]->id,
                'value' => $value,
            ]);
        }
    }

    protected function storeVariants(Product $product, array $variants): void
    {
        // Aucune variante fournie -> on crée une variante "Standard" par défaut,
        // pour que le produit soit immédiatement vendable sans forcer l'admin à gérer des variantes.
        if (empty($variants)) {
            $variants = [[
                'name' => 'Standard',
                'is_default' => true,
                'initial_quantity' => 0,
            ]];
        }

        $hasDefault = false;

        foreach ($variants as $variantData) {
            $isDefault = (bool) ($variantData['is_default'] ?? false);
            $hasDefault = $hasDefault || $isDefault;

            $sku = $variantData['sku'] ?? null;
            if (empty($sku)) {
                $seed = $product->sku . '-' . ($variantData['name'] ?? 'VAR');
                $sku = $this->generateUniqueSku($seed, ProductVariant::class, 'sku');
            }

            $variant = $product->variants()->create([
                'sku' => $sku,
                'name' => $variantData['name'],
                'price' => $variantData['price'] ?? null,
                'attributes' => $variantData['attributes'] ?? null,
                'is_active' => $variantData['is_active'] ?? true,
                'is_default' => $isDefault,
            ]);

            Inventory::create([
                'product_variant_id' => $variant->id,
                'quantity' => $variantData['initial_quantity'] ?? 0,
                'reserved_quantity' => 0,
                'low_stock_threshold' => $variantData['low_stock_threshold'] ?? 5,
            ]);
        }

        // Garantit toujours exactement une variante par défaut, même si l'admin n'en a coché aucune.
        if (! $hasDefault) {
            $product->variants()->first()?->update(['is_default' => true]);
        }
    }

    protected function storeImages(Product $product, array $images): void
    {
        foreach ($images as $index => $imageData) {
            $file = $imageData['file'];
            $path = $file->store('products', 'public');
            $url = MediaUrl::to($path);

            $product->images()->create([
                'url' => $url,
                'alt_text' => $imageData['alt_text'] ?? $product->name,
                'sort_order' => $index,
                'is_primary' => (bool) ($imageData['is_primary'] ?? ($index === 0)),
            ]);
        }
    }

    /**
     * Garantit un slug unique : si "iphone" existe déjà, essaie "iphone-2", "iphone-3", etc.
     */
    protected function generateUniqueSlug(string $baseSlug): string
    {
        $baseSlug = Str::slug($baseSlug) ?: 'produit';
        $slug = $baseSlug;
        $suffix = 2;

        while (Product::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }

    /**
     * Génère un SKU lisible et unique à partir d'un texte de départ (nom produit/variante),
     * avec un court suffixe aléatoire pour éviter toute collision.
     */
    protected function generateUniqueSku(string $seed, string $modelClass, string $column): string
    {
        $base = Str::upper(Str::slug($seed, ''));
        $base = substr($base, 0, 20) ?: 'SKU';

        do {
            $candidate = $base . '-' . Str::upper(Str::random(5));
        } while ($modelClass::where($column, $candidate)->exists());

        return $candidate;
    }
}
