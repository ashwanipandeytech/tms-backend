<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ActiveStatus;
use App\Enums\PackageCategoryType;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PackageCategory extends BaseModel
{
    protected $fillable = [
        'company_id',
        'name',
        'type',
    ];

    protected $casts = [
        'type' => PackageCategoryType::class,
    ];

    public function destinations(): HasMany
    {
        return $this->hasMany(Destination::class, 'category_id');
    }

    public function packages(): HasMany
    {
        return $this->hasMany(Package::class, 'category_id');
    }
}
