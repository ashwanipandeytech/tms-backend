<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ActiveStatus;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hotel extends BaseModel
{
    protected $fillable = [
        'company_id',
        'name',
        'location',
        'star_category',
        'contact_name',
        'contact_phone',
        'contact_email',
        'rating',
        'status',
    ];

    protected $casts = [
        'rating' => 'decimal:1',
        'status' => ActiveStatus::class,
    ];

    public function rooms(): HasMany
    {
        return $this->hasMany(HotelRoom::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(HotelImage::class);
    }
}
