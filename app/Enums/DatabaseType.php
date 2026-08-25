<?php

declare(strict_types=1);

namespace App\Enums;

enum DatabaseType: string
{
    case SHARED = 'shared';
    case DEDICATED = 'dedicated';
}
