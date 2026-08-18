<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ActiveStatus;
use App\Enums\MessageStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsappTemplate extends BaseModel
{
    protected $fillable = [
        'company_id',
        'name',
        'event_trigger',
        'message_body',
        'status',
    ];

    protected $casts = [
        'status' => ActiveStatus::class,
    ];
}
