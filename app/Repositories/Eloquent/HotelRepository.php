<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Hotel;
use App\Repositories\Contracts\HotelRepositoryInterface;

class HotelRepository extends BaseRepository implements HotelRepositoryInterface
{
    public function __construct(Hotel $model)
    {
        parent::__construct($model);
    }
}
