<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderAddress extends Model
{
    use HasFactory, HasUuid;

    const UPDATED_AT = null; // snapshot immuable — pas de modification après création

    protected $fillable = [
        'order_id',
        'recipient_name',
        'phone',
        'city',
        'district',
        'address_line',
        'landmark_note',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
