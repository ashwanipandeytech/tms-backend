<?php

declare(strict_types=1);

namespace App\Http\Requests;

class PaymentRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'booking_id'    => ['required', 'integer', 'exists:bookings,id'],
            'amount'        => ['required', 'numeric', 'min:0.01'],
            'payment_type'  => ['nullable', 'string'],
            'payment_mode'  => ['nullable', 'string', 'max:40'],
            'txn_reference' => ['nullable', 'string', 'max:80'],
            'paid_at'       => ['nullable', 'date'],
        ];
    }
}
