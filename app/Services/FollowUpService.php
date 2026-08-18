<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\FollowUpRepositoryInterface;

class FollowUpService extends BaseService
{
    public function __construct(FollowUpRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
