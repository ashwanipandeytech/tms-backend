<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Booking;
use App\Repositories\Contracts\BookingRepositoryInterface;

class BookingRepository extends BaseRepository implements BookingRepositoryInterface
{
    public function __construct(Booking $model)
    {
        parent::__construct($model);
    }
}
