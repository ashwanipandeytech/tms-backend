<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Vendor;
use App\Repositories\Contracts\VendorRepositoryInterface;

class VendorRepository extends BaseRepository implements VendorRepositoryInterface
{
    public function __construct(Vendor $model)
    {
        parent::__construct($model);
    }
}
