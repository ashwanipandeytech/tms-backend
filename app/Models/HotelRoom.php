<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HotelRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id',
        'room_type',
        'meal_plan',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }
}
