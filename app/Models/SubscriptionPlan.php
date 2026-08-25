<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DatabaseType;

class SubscriptionPlan extends BaseModel
{
    protected $fillable = [
        'name',
        'slug',
        'monthly_price',
        'yearly_price',
        'base_user_seats',
        'addon_seat_price',
        'modules',
        'database_type',
        'status',
    ];

    protected $casts = [
        'monthly_price'    => 'decimal:2',
        'yearly_price'     => 'decimal:2',
        'addon_seat_price' => 'decimal:2',
        'base_user_seats'  => 'integer',
        'modules'          => 'array',
        'database_type'    => DatabaseType::class,
    ];

    public function hasModule(string $moduleName): bool
    {
        if (empty($this->modules)) {
            return false;
        }

        return in_array(strtolower($moduleName), array_map('strtolower', $this->modules), true);
    }
}
