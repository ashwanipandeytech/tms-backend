<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;

class LeadResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'email'        => $this->email,
            'phone'        => $this->phone,
            'source'       => $this->whenLoaded('source', fn() => $this->source->name),
            'destination'  => $this->destination,
            'travel_date'  => $this->travel_date?->format('Y-m-d'),
            'pax_adults'   => $this->pax_adults,
            'pax_children' => $this->pax_children,
            'budget'       => (float) $this->budget,
            'status'       => $this->status?->value ?? $this->status,
            'assigned_to'  => $this->whenLoaded('assignedUser', fn() => [
                'id'   => $this->assignedUser->id,
                'name' => $this->assignedUser->name,
            ]),
            'notes'        => $this->notes,
            'created_at'   => $this->created_at?->toIso8601String(),
        ];
    }
}
