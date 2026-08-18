<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\QuotationItemType;
use App\Enums\QuotationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Quotation extends BaseModel
{
    protected $fillable = [
        'company_id',
        'quotation_no',
        'lead_id',
        'customer_name',
        'package_id',
        'coupon_id',
        'sub_total',
        'discount',
        'gst_amount',
        'final_amount',
        'status',
        'valid_till',
        'pdf_path',
        'created_by',
    ];

    protected $casts = [
        'sub_total'    => 'decimal:2',
        'discount'     => 'decimal:2',
        'gst_amount'   => 'decimal:2',
        'final_amount' => 'decimal:2',
        'valid_till'   => 'date',
        'status'       => QuotationStatus::class,
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function itinerary(): HasOne
    {
        return $this->hasOne(Itinerary::class);
    }
}
