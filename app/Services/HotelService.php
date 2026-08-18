<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\HotelRepositoryInterface;

class HotelService extends BaseService
{
    public function __construct(HotelRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
