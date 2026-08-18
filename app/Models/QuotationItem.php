<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\QuotationItemType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuotationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'quotation_id',
        'item_type',
        'description',
        'qty',
        'amount',
    ];

    protected $casts = [
        'amount'    => 'decimal:2',
        'item_type' => QuotationItemType::class,
    ];

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }
}
