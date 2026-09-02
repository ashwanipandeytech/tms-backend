<?php

declare(strict_types=1);

namespace App\Http\Requests;

class LeadWebhookMetaRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true; // Webhook authentication handled separately or public boundary
    }

    public function rules(): array
    {
        return [
            'name'            => 'required|string|max:100',
            'phone'           => 'required|string|max:20',
            'email'           => 'nullable|email|max:150',
            'campaign_source' => 'nullable|string|max:100',
            'destination'     => 'nullable|string|max:100',
        ];
    }
}
