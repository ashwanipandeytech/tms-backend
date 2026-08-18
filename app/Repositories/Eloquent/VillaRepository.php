<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Villa;
use App\Repositories\Contracts\VillaRepositoryInterface;

class VillaRepository extends BaseRepository implements VillaRepositoryInterface
{
    public function __construct(Villa $model)
    {
        parent::__construct($model);
    }
}
