<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\ResortRepositoryInterface;

class ResortService extends BaseService
{
    public function __construct(ResortRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
