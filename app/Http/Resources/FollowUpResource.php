<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;

class FollowUpResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'lead_id'         => $this->lead_id,
            'assigned_to'     => $this->assigned_to,
            'follow_up_date'  => $this->follow_up_date?->format('Y-m-d'),
            'follow_up_time'  => $this->follow_up_time,
            'type'            => $this->type?->value ?? $this->type,
            'remarks'         => $this->remarks,
            'remind_whatsapp' => $this->remind_whatsapp,
            'remind_email'    => $this->remind_email,
            'status'          => $this->status?->value ?? $this->status,
            'created_at'      => $this->created_at?->toIso8601String(),
        ];
    }
}
