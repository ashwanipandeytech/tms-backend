<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\VillaRepositoryInterface;

class VillaService extends BaseService
{
    public function __construct(VillaRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
