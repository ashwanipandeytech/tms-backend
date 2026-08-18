<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Driver extends BaseModel
{
    protected $fillable = [
        'company_id',
        'name',
        'phone',
        'license_no',
        'vehicle_id',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}
