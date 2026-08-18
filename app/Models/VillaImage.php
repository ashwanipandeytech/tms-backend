<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VillaImage extends Model
{
    use HasFactory;

    protected $fillable = ['villa_id', 'image_path'];

    public function villa(): BelongsTo
    {
        return $this->belongsTo(Villa::class);
    }
}
