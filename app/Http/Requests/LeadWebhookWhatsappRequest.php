<?php

declare(strict_types=1);

namespace App\Http\Requests;

class LeadWebhookWhatsappRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'            => 'nullable|string|max:100',
            'phone'           => 'required|string|max:20',
            'message'         => 'nullable|string',
            'destination'     => 'nullable|string|max:100',
            'campaign_source' => 'nullable|string|max:100',
            'notes'           => 'nullable|string',
        ];
    }
}
