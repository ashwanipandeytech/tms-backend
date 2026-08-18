<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResortImage extends Model
{
    use HasFactory;

    protected $fillable = ['resort_id', 'image_path'];

    public function resort(): BelongsTo
    {
        return $this->belongsTo(Resort::class);
    }
}
