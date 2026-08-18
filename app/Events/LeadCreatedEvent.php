<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Lead;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LeadCreatedEvent
{
    use Dispatchable;
    use SerializesModels;

    public Lead $lead;

    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
    }
}
