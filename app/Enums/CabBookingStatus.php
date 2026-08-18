<?php

declare(strict_types=1);

namespace App\Enums;

enum CabBookingStatus: string
{
    CASE PENDING = 'pending';
    CASE CONFIRMED = 'confirmed';
    CASE COMPLETED = 'completed';
    CASE CANCELLED = 'cancelled';
}
