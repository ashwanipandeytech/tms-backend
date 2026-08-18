<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\VehicleStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends BaseModel
{
    protected $fillable = [
        'company_id',
        'vehicle_type_id',
        'vendor_id',
        'model',
        'number_plate',
        'status',
    ];

    protected $casts = [
        'status' => VehicleStatus::class,
    ];

    public function vehicleType(): BelongsTo
    {
        return $this->belongsTo(VehicleType::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function drivers(): HasMany
    {
        return $this->hasMany(Driver::class);
    }
}
