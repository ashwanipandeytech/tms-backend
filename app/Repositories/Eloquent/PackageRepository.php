<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Package;
use App\Repositories\Contracts\PackageRepositoryInterface;

class PackageRepository extends BaseRepository implements PackageRepositoryInterface
{
    public function __construct(Package $model)
    {
        parent::__construct($model);
    }
}
