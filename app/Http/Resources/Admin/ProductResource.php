<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\Client\ProductVariantResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Côté Admin, cost_price est visible (à l'inverse du Resource Client).
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category' => $this->when($this->relationLoaded('category'), fn() => [
                'id' => $this->category->id,
                'name' => $this->category->name,
            ]),
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'sku' => $this->sku,
            'price' => $this->price,
            'compare_at_price' => $this->compare_at_price,
            'cost_price' => $this->cost_price,
            'brand' => $this->brand,
            'weight' => $this->weight,
            'status' => $this->status,
            'is_featured' => $this->is_featured,
            'attributes' => $this->when(
                $this->relationLoaded('attributeValues'),
                // (object) force l'encodage JSON en {} même vide, au lieu de []
                // qu'un tableau PHP vide produirait — sinon le client (Dart) qui
                // attend une Map plante sur un cast Map<String,dynamic>.
                fn() => (object) $this->attributesFormatted()
            ),

            'images' => $this->whenLoaded('images', fn() => $this->images->map(fn($img) => [
                'id' => $img->id,
                'url' => $img->url,
                'alt_text' => $img->alt_text,
                'sort_order' => $img->sort_order,
                'is_primary' => $img->is_primary,
            ])),
            'variants' => ProductVariantResource::collection($this->whenLoaded('variants')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
