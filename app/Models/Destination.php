<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Destination extends BaseModel
{
    protected $fillable = [
        'company_id',
        'category_id',
        'name',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(PackageCategory::class, 'category_id');
    }

    public function packages(): HasMany
    {
        return $this->hasMany(Package::class);
    }
}
