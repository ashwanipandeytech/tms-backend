<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\VehicleStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VehicleType extends BaseModel
{
    protected $fillable = [
        'company_id',
        'name',
    ];

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    public function rates(): HasMany
    {
        return $this->hasMany(CabRate::class);
    }
}
