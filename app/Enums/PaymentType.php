<?php

declare(strict_types=1);

namespace App\Enums;

enum PaymentType: string
{
    CASE ADVANCE = 'advance';
    CASE PARTIAL = 'partial';
    CASE FULL = 'full';
}
