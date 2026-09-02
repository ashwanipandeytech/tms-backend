<?php

declare(strict_types=1);

namespace App\Http\Requests;

class VehicleStoreRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'vehicle_type_id' => 'required|exists:vehicle_types,id',
            'vendor_id'       => 'nullable|exists:vendors,id',
            'model'           => 'nullable|string|max:80',
            'number_plate'    => 'nullable|string|max:20',
            'status'          => 'nullable|string|in:available,booked,maintenance',
        ];
    }
}
