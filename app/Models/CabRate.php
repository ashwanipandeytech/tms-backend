<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CabRate extends BaseModel
{
    protected $fillable = [
        'company_id',
        'vehicle_type_id',
        'rate_per_km',
        'rate_per_day',
        'base_fare',
    ];

    protected $casts = [
        'rate_per_km'  => 'decimal:2',
        'rate_per_day' => 'decimal:2',
        'base_fare'    => 'decimal:2',
    ];

    public function vehicleType(): BelongsTo
    {
        return $this->belongsTo(VehicleType::class);
    }
}
