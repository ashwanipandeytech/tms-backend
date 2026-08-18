<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;

class BookingResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'booking_no'   => $this->booking_no,
            'lead_id'      => $this->lead_id,
            'customer'     => $this->whenLoaded('customer', fn() => [
                'id'    => $this->customer->id,
                'name'  => $this->customer->name,
                'phone' => $this->customer->phone,
            ]),
            'package'      => $this->whenLoaded('package', fn() => [
                'id'   => $this->package->id,
                'name' => $this->package->name,
            ]),
            'travel_date'  => $this->travel_date?->format('Y-m-d'),
            'total_amount' => (float) $this->total_amount,
            'paid_amount'  => (float) $this->paid_amount,
            'due_amount'   => (float) $this->due_amount,
            'status'       => $this->status?->value ?? $this->status,
            'created_at'   => $this->created_at?->toIso8601String(),
        ];
    }
}
