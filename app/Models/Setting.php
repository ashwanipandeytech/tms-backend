<?php

declare(strict_types=1);

namespace App\Models;

class Setting extends BaseModel
{
    protected $fillable = [
        'company_id',
        'setting_key',
        'setting_value',
    ];
}
