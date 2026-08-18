<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\FollowUpStatus;
use App\Enums\FollowUpType;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FollowUp extends BaseModel
{
    protected $fillable = [
        'company_id',
        'lead_id',
        'assigned_to',
        'follow_up_date',
        'follow_up_time',
        'type',
        'remarks',
        'remind_whatsapp',
        'remind_email',
        'status',
    ];

    protected $casts = [
        'follow_up_date'  => 'date',
        'type'            => FollowUpType::class,
        'status'          => FollowUpStatus::class,
        'remind_whatsapp' => 'boolean',
        'remind_email'    => 'boolean',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
