<?php

declare(strict_types=1);

namespace App\Enums;

enum FollowUpStatus: string
{
    CASE PENDING = 'pending';
    CASE DONE = 'done';
    CASE MISSED = 'missed';
}
