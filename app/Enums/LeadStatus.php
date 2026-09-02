<?php

declare(strict_types=1);

namespace App\Enums;

enum LeadStatus: string
{
    case NEW = 'new';
    case NEW_LEAD = 'NEW_LEAD';
    case CONTACTED = 'contacted';
    case ATTEMPTED_CONTACT = 'ATTEMPTED_CONTACT';
    case CONNECTED = 'CONNECTED';
    case FOLLOWUP = 'followup';
    case FOLLOW_UP = 'FOLLOW_UP';
    case INTERESTED = 'interested';
    case QUOTATION_SENT = 'quotation_sent';
    case NEGOTIATION = 'negotiation';
    case CONFIRMED = 'confirmed';
    case BOOKING_CONFIRMED = 'BOOKING_CONFIRMED';
    case TOUR_COMPLETED = 'TOUR_COMPLETED';
    case NOT_INTERESTED = 'NOT_INTERESTED';
    case LOST = 'lost';
    case LOST_LEAD = 'LOST_LEAD';
    case CANCELLED = 'CANCELLED';
}
