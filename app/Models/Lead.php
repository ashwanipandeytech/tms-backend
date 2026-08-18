<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\LeadStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lead extends BaseModel
{
    protected $fillable = [
        'company_id',
        'name',
        'email',
        'phone',
        'source_id',
        'destination',
        'travel_date',
        'pax_adults',
        'pax_children',
        'budget',
        'status',
        'assigned_to',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'travel_date' => 'date',
        'budget'      => 'decimal:2',
        'status'      => LeadStatus::class,
    ];

    public function source(): BelongsTo
    {
        return $this->belongsTo(LeadSource::class, 'source_id');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(LeadActivity::class);
    }

    public function followUps(): HasMany
    {
        return $this->hasMany(FollowUp::class);
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where(function (Builder $q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('phone', 'like', "%{$term}%")
              ->orWhere('email', 'like', "%{$term}%")
              ->orWhere('destination', 'like', "%{$term}%");
        });
    }
}
