<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackageImage extends Model
{
    use HasFactory;

    protected $fillable = ['package_id', 'image_path'];

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }
}
