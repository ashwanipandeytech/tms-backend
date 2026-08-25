<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DatabaseType;
use App\Enums\SubscriptionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'addon_user_seats'       => 'integer',
        'subscription_starts_at' => 'datetime',
        'subscription_ends_at'   => 'datetime',
        'subscription_status'    => SubscriptionStatus::class,
        'database_type'          => DatabaseType::class,
    ];

    public function subscriptionPlan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function roles(): HasMany
    {
        return $this->hasMany(Role::class);
    }

    /**
     * Calculate Total Allowed Seats = Base Plan Seats + Purchased Addon Seats.
     */
    public function getTotalAllowedSeatsAttribute(): int
    {
        $baseSeats = $this->subscriptionPlan?->base_user_seats ?? 5;
        return $baseSeats + ($this->addon_user_seats ?? 0);
    }

    /**
     * Check if company subscription is currently active or in valid grace period.
     */
    public function isSubscriptionActive(): bool
    {
        if ($this->subscription_status === SubscriptionStatus::SUSPENDED) {
            return false;
        }

        if ($this->subscription_ends_at && $this->subscription_ends_at->isPast()) {
            return false;
        }

        return true;
    }
}
