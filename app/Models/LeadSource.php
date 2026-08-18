<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class LeadSource extends BaseModel
{
    protected $fillable = [
        'company_id',
        'name',
    ];

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class, 'source_id');
    }
}
