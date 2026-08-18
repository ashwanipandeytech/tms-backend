<?php

declare(strict_types=1);

namespace App\Http\Requests;

class QuotationRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'lead_id'       => ['nullable', 'integer', 'exists:leads,id'],
            'customer_name' => ['nullable', 'string', 'max:120'],
            'package_id'    => ['nullable', 'integer', 'exists:packages,id'],
            'coupon_id'     => ['nullable', 'integer', 'exists:coupons,id'],
            'sub_total'     => ['nullable', 'numeric', 'min:0'],
            'discount'      => ['nullable', 'numeric', 'min:0'],
            'gst_amount'    => ['nullable', 'numeric', 'min:0'],
            'final_amount'  => ['nullable', 'numeric', 'min:0'],
            'status'        => ['nullable', 'string'],
            'valid_till'    => ['nullable', 'date'],
        ];
    }
}
