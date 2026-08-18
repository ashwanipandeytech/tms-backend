<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Season;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResortRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'resort_id',
        'room_type',
        'season',
        'price',
        'inventory',
    ];

    protected $casts = [
        'price'  => 'decimal:2',
        'season' => Season::class,
    ];

    public function resort(): BelongsTo
    {
        return $this->belongsTo(Resort::class);
    }
}
