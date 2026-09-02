<?php

declare(strict_types=1);

namespace App\Http\Requests;

class CabBookingStoreRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'lead_id'         => 'nullable|exists:leads,id',
            'vehicle_id'      => 'nullable|exists:vehicles,id',
            'driver_id'       => 'nullable|exists:drivers,id',
            'pickup_location' => 'nullable|string|max:150',
            'drop_location'   => 'nullable|string|max:150',
            'pickup_datetime' => 'nullable|date',
            'amount'          => 'nullable|numeric|min:0',
            'status'          => 'nullable|string|in:pending,confirmed,completed,cancelled',
        ];
    }
}
