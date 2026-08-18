<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\VehicleRepositoryInterface;

class VehicleService extends BaseService
{
    public function __construct(VehicleRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
