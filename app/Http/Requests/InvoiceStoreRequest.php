<?php

declare(strict_types=1);

namespace App\Http\Requests;

class InvoiceStoreRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'booking_id' => 'nullable|exists:bookings,id',
            'amount'     => 'nullable|numeric|min:0',
            'gst_amount' => 'nullable|numeric|min:0',
            'status'     => 'nullable|string|in:unpaid,partial,paid',
        ];
    }
}
