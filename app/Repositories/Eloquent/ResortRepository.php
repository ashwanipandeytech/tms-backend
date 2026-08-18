<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Resort;
use App\Repositories\Contracts\ResortRepositoryInterface;

class ResortRepository extends BaseRepository implements ResortRepositoryInterface
{
    public function __construct(Resort $model)
    {
        parent::__construct($model);
    }
}
