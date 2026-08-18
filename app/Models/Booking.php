<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\BookingStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends BaseModel
{
    protected $fillable = [
        'company_id',
        'booking_no',
        'lead_id',
        'quotation_id',
        'customer_id',
        'package_id',
        'travel_date',
        'total_amount',
        'paid_amount',
        'due_amount',
        'status',
        'created_by',
    ];

    protected $casts = [
        'travel_date'  => 'date',
        'total_amount' => 'decimal:2',
        'paid_amount'  => 'decimal:2',
        'due_amount'   => 'decimal:2',
        'status'       => BookingStatus::class,
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }
}
