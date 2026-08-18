<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ActiveStatus;
use App\Enums\Season;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Resort extends BaseModel
{
    protected $fillable = [
        'company_id',
        'name',
        'location',
        'facilities',
        'status',
    ];

    protected $casts = [
        'status' => ActiveStatus::class,
    ];

    public function rooms(): HasMany
    {
        return $this->hasMany(ResortRoom::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ResortImage::class);
    }
}
