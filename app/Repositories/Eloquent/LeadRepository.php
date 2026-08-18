<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Lead;
use App\Repositories\Contracts\LeadRepositoryInterface;

class LeadRepository extends BaseRepository implements LeadRepositoryInterface
{
    public function __construct(Lead $model)
    {
        parent::__construct($model);
    }
}
