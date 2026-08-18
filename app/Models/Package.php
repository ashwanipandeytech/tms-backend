<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ActiveStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends BaseModel
{
    protected $fillable = [
        'company_id',
        'name',
        'destination_id',
        'category_id',
        'nights',
        'days',
        'price',
        'gst_applicable',
        'gst_percent',
        'inclusions',
        'exclusions',
        'terms',
        'brochure_pdf',
        'status',
    ];

    protected $casts = [
        'price'          => 'decimal:2',
        'gst_percent'    => 'decimal:2',
        'gst_applicable' => 'boolean',
        'status'         => ActiveStatus::class,
    ];

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(PackageCategory::class, 'category_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(PackageImage::class);
    }
}
