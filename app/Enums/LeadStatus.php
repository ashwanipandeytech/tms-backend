<?php

declare(strict_types=1);

namespace App\Enums;

enum LeadStatus: string
{
    CASE NEW = 'new';
    CASE CONTACTED = 'contacted';
    CASE FOLLOWUP = 'followup';
    CASE INTERESTED = 'interested';
    CASE QUOTATION_SENT = 'quotation_sent';
    CASE NEGOTIATION = 'negotiation';
    CASE CONFIRMED = 'confirmed';
    CASE LOST = 'lost';
}
