<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ActiveStatus;
use App\Enums\CouponType;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends BaseModel
{
    protected $fillable = [
        'company_id',
        'code',
        'type',
        'value',
        'expiry_date',
        'usage_limit',
        'used_count',
        'status',
    ];

    protected $casts = [
        'type'        => CouponType::class,
        'value'       => 'decimal:2',
        'expiry_date' => 'date',
        'status'      => ActiveStatus::class,
    ];
}
