<?php

namespace App\Http\Resources\Client;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'recipient_name' => $this->recipient_name,
            'phone' => $this->phone,
            'city' => $this->city,
            'district' => $this->district,
            'address_line' => $this->address_line,
            'landmark_note' => $this->landmark_note,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'is_default' => $this->is_default,
            'created_at' => $this->created_at,
        ];
    }
}
