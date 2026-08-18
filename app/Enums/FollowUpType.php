<?php

declare(strict_types=1);

namespace App\Enums;

enum FollowUpType: string
{
    CASE CALL = 'call';
    CASE WHATSAPP = 'whatsapp';
    CASE EMAIL = 'email';
    CASE MEETING = 'meeting';
}
