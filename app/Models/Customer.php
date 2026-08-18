<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ActiveStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends BaseModel
{
    protected $fillable = [
        'company_id',
        'name',
        'email',
        'phone',
        'password',
        'lead_id',
        'status',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'password' => 'hashed',
        'status'   => ActiveStatus::class,
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
