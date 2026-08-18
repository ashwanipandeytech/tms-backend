<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\TenantScopedTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model
{
    use HasFactory;
    use TenantScopedTrait;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>
     */
    protected $guarded = ['id'];
}
