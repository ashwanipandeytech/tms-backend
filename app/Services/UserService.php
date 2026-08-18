<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\UserRepositoryInterface;

class UserService extends BaseService
{
    public function __construct(UserRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
