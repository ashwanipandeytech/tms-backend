<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\BookingConfirmedEvent;
use App\Models\Notification;

class SendNotificationListener
{
    public function handle(BookingConfirmedEvent $event): void
    {
        if ($event->booking->created_by) {
            Notification::create([
                'user_id' => $event->booking->created_by,
                'title'   => 'Booking Confirmed',
                'message' => 'Booking #' . $event->booking->booking_no . ' has been confirmed.',
                'is_read' => false,
            ]);
        }
    }
}
