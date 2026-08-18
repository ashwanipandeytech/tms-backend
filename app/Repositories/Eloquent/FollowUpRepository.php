<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\FollowUp;
use App\Repositories\Contracts\FollowUpRepositoryInterface;

class FollowUpRepository extends BaseRepository implements FollowUpRepositoryInterface
{
    public function __construct(FollowUp $model)
    {
        parent::__construct($model);
    }
}
