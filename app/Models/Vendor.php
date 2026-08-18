<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CabBookingStatus;
use App\Enums\VehicleStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vendor extends BaseModel
{
    protected $fillable = [
        'company_id',
        'name',
        'type',
        'contact',
        'email',
        'address',
    ];

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    public function vendorPayments(): HasMany
    {
        return $this->hasMany(VendorPayment::class);
    }
}
