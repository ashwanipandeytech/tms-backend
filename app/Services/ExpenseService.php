<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\ExpenseRepositoryInterface;

class ExpenseService extends BaseService
{
    public function __construct(ExpenseRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
