<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PaymentType;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends BaseModel
{
    protected $fillable = [
        'company_id',
        'booking_id',
        'amount',
        'payment_type',
        'payment_mode',
        'txn_reference',
        'paid_at',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'paid_at'      => 'datetime',
        'payment_type' => PaymentType::class,
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
