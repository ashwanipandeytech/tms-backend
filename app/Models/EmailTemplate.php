<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ActiveStatus;
use App\Enums\MessageStatus;

class EmailTemplate extends BaseModel
{
    protected $fillable = [
        'company_id',
        'name',
        'event_trigger',
        'subject',
        'body',
        'status',
    ];

    protected $casts = [
        'status' => ActiveStatus::class,
    ];
}
