<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\MessageStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsappLog extends BaseModel
{
    protected $fillable = [
        'company_id',
        'lead_id',
        'phone',
        'message',
        'status',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'status'  => MessageStatus::class,
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }
}
