<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadActivity extends BaseModel
{
    protected $fillable = [
        'company_id',
        'lead_id',
        'user_id',
        'activity_type',
        'description',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
