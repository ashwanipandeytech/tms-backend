<?php

declare(strict_types=1);

namespace App\Enums;

enum QuotationItemType: string
{
    CASE PACKAGE = 'package';
    CASE HOTEL = 'hotel';
    CASE RESORT = 'resort';
    CASE VILLA = 'villa';
    CASE CAB = 'cab';
    CASE CUSTOM = 'custom';
}
