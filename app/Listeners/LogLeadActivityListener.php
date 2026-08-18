<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\LeadCreatedEvent;
use App\Models\LeadActivity;

class LogLeadActivityListener
{
    public function handle(LeadCreatedEvent $event): void
    {
        LeadActivity::create([
            'company_id'    => $event->lead->company_id,
            'lead_id'       => $event->lead->id,
            'user_id'       => auth()->id(),
            'activity_type' => 'enquiry',
            'description'   => 'New lead enquiry created for ' . $event->lead->name,
        ]);
    }
}
