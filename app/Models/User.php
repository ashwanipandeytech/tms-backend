<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ActiveStatus;
use App\Traits\TenantScopedTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use TenantScopedTrait;

    protected $fillable = [
        'company_id',
        'role_id',
        'name',
        'email',
        'phone',
        'password',
        'avatar',
        'status',
        'last_login',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login'        => 'datetime',
        'password'          => 'hashed',
        'status'            => ActiveStatus::class,
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class, 'assigned_to');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role_id === 1 || ($this->role && strtolower($this->role->name) === 'super admin');
    }

    public function hasPermission(string $module, string $action): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        if (!$this->role) {
            return false;
        }

        return $this->role->permissions()
            ->where('module', $module)
            ->where('action', $action)
            ->exists();
    }
}
