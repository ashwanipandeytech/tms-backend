<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\RoleRepositoryInterface;

class RoleService extends BaseService
{
    public function __construct(RoleRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
