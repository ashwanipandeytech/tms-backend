<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\InvoiceStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends BaseModel
{
    protected $fillable = [
        'company_id',
        'invoice_no',
        'booking_id',
        'amount',
        'gst_amount',
        'total',
        'status',
        'issued_at',
        'pdf_path',
    ];

    protected $casts = [
        'amount'     => 'decimal:2',
        'gst_amount' => 'decimal:2',
        'total'      => 'decimal:2',
        'issued_at'  => 'date',
        'status'     => InvoiceStatus::class,
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
