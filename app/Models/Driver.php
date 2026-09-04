<?php

namespace App\Models;

use App\Enums\DriverStatus;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Driver extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'employee_id',
        'vehicle_type',
        'vehicle_number',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => DriverStatus::class,
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class);
    }

    public function isAvailable(): bool
    {
        return $this->status === DriverStatus::AVAILABLE;
    }
}
