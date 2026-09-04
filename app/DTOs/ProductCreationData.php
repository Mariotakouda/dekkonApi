<?php

namespace App\DTOs;

class ProductCreationData
{
    public function __construct(
        public readonly array $productFields,
        public readonly array $attributes = [],
        public readonly array $variants = [],
        public readonly array $images = [],
    ) {}

    public static function fromRequest($request): self
    {
        $validated = $request->validated();

        return new self(
            productFields: [
                'category_id' => $validated['category_id'],
                'name' => $validated['name'],
                'slug' => $validated['slug'] ?? null,
                'description' => $validated['description'] ?? null,
                'sku' => $validated['sku'] ?? null,
                'price' => $validated['price'],
                'compare_at_price' => $validated['compare_at_price'] ?? null,
                'cost_price' => $validated['cost_price'] ?? null,
                'brand' => $validated['brand'] ?? null,
                'weight' => $validated['weight'] ?? null,
                'is_featured' => $validated['is_featured'] ?? false,
                'status' => $validated['status'] ?? null,
            ],
            attributes: $validated['attributes'] ?? [],
            variants: $validated['variants'] ?? [],
            images: array_map(
                fn(int $index, array $image) => [
                    'file' => $image['file'],
                    'alt_text' => $image['alt_text'] ?? null,
                    'is_primary' => $image['is_primary'] ?? ($index === 0),
                ],
                array_keys($validated['images'] ?? []),
                $validated['images'] ?? []
            ),
        );
    }
}
