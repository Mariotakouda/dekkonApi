<?php

namespace App\Http\Resources\Client;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'rating' => $this->rating,
            'comment' => $this->comment,
            'status' => $this->status,
            'product' => $this->when($this->relationLoaded('product'), fn () => [
                'id' => $this->product->id,
                'name' => $this->product->name,
            ]),
            'created_at' => $this->created_at,
        ];
    }
}
