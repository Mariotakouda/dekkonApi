<?php

namespace App\Http\Resources\Client;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * ⚠️ cost_price n'apparaît JAMAIS ici — section 9 du cahier des charges.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'sku' => $this->sku,
            'price' => $this->price,
            'compare_at_price' => $this->compare_at_price,
            'brand' => $this->brand,
            'is_featured' => $this->is_featured,
            'category' => $this->whenLoaded('category', fn () => [
                'id' => $this->category->id,
                'name' => $this->category->name,
                'slug' => $this->category->slug,
            ]),
            'images' => $this->whenLoaded('images', fn () => $this->images->map(fn ($img) => [
                'url' => $img->url,
                'alt_text' => $img->alt_text,
                'is_primary' => $img->is_primary,
            ])),
            'variants' => ProductVariantResource::collection($this->whenLoaded('variants')),
            'average_rating' => $this->when($request->routeIs('*.products.show'), fn () => $this->averageRating()),
        ];
    }
}
