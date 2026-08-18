<?php

declare(strict_types=1);

namespace App\Enums;

enum VehicleStatus: string
{
    CASE AVAILABLE = 'available';
    CASE BOOKED = 'booked';
    CASE MAINTENANCE = 'maintenance';
}
