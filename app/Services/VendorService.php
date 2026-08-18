<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\VendorRepositoryInterface;

class VendorService extends BaseService
{
    public function __construct(VendorRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
