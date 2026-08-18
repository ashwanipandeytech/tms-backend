<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;

class QuotationResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'quotation_no'  => $this->quotation_no,
            'lead_id'       => $this->lead_id,
            'customer_name' => $this->customer_name,
            'package_id'    => $this->package_id,
            'sub_total'     => (float) $this->sub_total,
            'discount'      => (float) $this->discount,
            'gst_amount'    => (float) $this->gst_amount,
            'final_amount'  => (float) $this->final_amount,
            'status'        => $this->status?->value ?? $this->status,
            'valid_till'    => $this->valid_till?->format('Y-m-d'),
            'created_at'    => $this->created_at?->toIso8601String(),
        ];
    }
}
