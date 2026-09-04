<?php

namespace App\Models;

use App\Enums\UserStatus;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasUuid, Notifiable;

    protected $fillable = [
        'role_id',
        'name',
        'code',
        'email',
        'phone',
        'password',
        'status',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'status' => UserStatus::class,
            'is_active' => 'boolean',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function directPermissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'user_permission')
            ->withTimestamps();
    }

    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class);
    }

    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class);
    }

    public function notifications(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\Notification::class);
    }
    /**
     * Vérifie si l'utilisateur possède une permission,
     * via son rôle OU ses permissions directes (section 7 du cahier des charges).
     */
    public function hasPermission(string $code): bool
    {
        if ($this->role && $this->role->permissions()->where('code', $code)->exists()) {
            return true;
        }

        return $this->directPermissions()->where('code', $code)->exists();
    }

    public function isEmployee(): bool
    {
        return $this->employee()->exists();
    }

    public function isCustomer(): bool
    {
        return $this->customer()->exists();
    }
}
