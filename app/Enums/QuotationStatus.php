<?php

declare(strict_types=1);

namespace App\Enums;

enum QuotationStatus: string
{
    CASE DRAFT = 'draft';
    CASE SENT = 'sent';
    CASE ACCEPTED = 'accepted';
    CASE REJECTED = 'rejected';
}
