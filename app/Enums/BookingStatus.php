<?php

declare(strict_types=1);

namespace App\Enums;

enum BookingStatus: string
{
    CASE PENDING = 'pending';
    CASE CONFIRMED = 'confirmed';
    CASE CANCELLED = 'cancelled';
    CASE COMPLETED = 'completed';
}
