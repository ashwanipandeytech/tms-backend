<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\CustomerRepositoryInterface;

class CustomerService extends BaseService
{
    public function __construct(CustomerRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
