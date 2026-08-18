<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;

class PaymentResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'booking_id'    => $this->booking_id,
            'amount'        => (float) $this->amount,
            'payment_type'  => $this->payment_type?->value ?? $this->payment_type,
            'payment_mode'  => $this->payment_mode,
            'txn_reference' => $this->txn_reference,
            'paid_at'       => $this->paid_at?->toIso8601String(),
            'created_at'    => $this->created_at?->toIso8601String(),
        ];
    }
}
