<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\CabBooking;
use App\Repositories\Contracts\CabBookingRepositoryInterface;

class CabBookingRepository extends BaseRepository implements CabBookingRepositoryInterface
{
    public function __construct(CabBooking $model)
    {
        parent::__construct($model);
    }
}
