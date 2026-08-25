<?php

declare(strict_types=1);

namespace App\Enums;

enum SubscriptionStatus: string
{
    case ACTIVE = 'active';
    case EXPIRED = 'expired';
    case GRACE_PERIOD = 'grace_period';
    case SUSPENDED = 'suspended';
}
