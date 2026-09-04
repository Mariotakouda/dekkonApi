<?php

namespace App\Models;

use App\Enums\EmployeeStatus;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Employee extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'user_id',
        'employee_number',
        'first_name',
        'last_name',
        'position',
        'date_hired_at',
        'gender',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'date_hired_at' => 'date',
            'status' => EmployeeStatus::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function driver(): HasOne
    {
        return $this->hasOne(Driver::class);
    }

    public function fullName(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function isDriver(): bool
    {
        return $this->driver()->exists();
    }
}
