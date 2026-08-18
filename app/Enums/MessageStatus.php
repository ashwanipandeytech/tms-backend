<?php

declare(strict_types=1);

namespace App\Enums;

enum MessageStatus: string
{
    CASE QUEUED = 'queued';
    CASE SENT = 'sent';
    CASE DELIVERED = 'delivered';
    CASE FAILED = 'failed';
}
