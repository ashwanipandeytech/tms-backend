<?php

declare(strict_types=1);

namespace App\Http\Requests;

class BookingRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'lead_id'      => ['nullable', 'integer', 'exists:leads,id'],
            'quotation_id' => ['nullable', 'integer', 'exists:quotations,id'],
            'customer_id'  => ['nullable', 'integer', 'exists:customers,id'],
            'package_id'   => ['nullable', 'integer', 'exists:packages,id'],
            'travel_date'  => ['nullable', 'date'],
            'total_amount' => ['nullable', 'numeric', 'min:0'],
            'paid_amount'  => ['nullable', 'numeric', 'min:0'],
            'status'       => ['nullable', 'string'],
        ];
    }
}
