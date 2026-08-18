<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ActiveStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Villa extends BaseModel
{
    protected $fillable = [
        'company_id',
        'name',
        'location',
        'capacity',
        'bedrooms',
        'price',
        'amenities',
        'status',
    ];

    protected $casts = [
        'price'  => 'decimal:2',
        'status' => ActiveStatus::class,
    ];

    public function images(): HasMany
    {
        return $this->hasMany(VillaImage::class);
    }
}
