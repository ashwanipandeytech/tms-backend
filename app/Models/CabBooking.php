<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CabBookingStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CabBooking extends BaseModel
{
    protected $fillable = [
        'company_id',
        'lead_id',
        'vehicle_id',
        'driver_id',
        'pickup_location',
        'drop_location',
        'pickup_datetime',
        'amount',
        'status',
    ];

    protected $casts = [
        'pickup_datetime' => 'datetime',
        'amount'          => 'decimal:2',
        'status'          => CabBookingStatus::class,
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }
}
