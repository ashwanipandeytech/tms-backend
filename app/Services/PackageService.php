<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\PackageRepositoryInterface;

class PackageService extends BaseService
{
    public function __construct(PackageRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
