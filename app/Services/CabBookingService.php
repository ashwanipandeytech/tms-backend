<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\CabBookingRepositoryInterface;

class CabBookingService extends BaseService
{
    public function __construct(CabBookingRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
