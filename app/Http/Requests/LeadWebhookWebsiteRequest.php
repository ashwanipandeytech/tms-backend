<?php

declare(strict_types=1);

namespace App\Http\Requests;

class LeadWebhookWebsiteRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'            => 'required_without:full_name|nullable|string|max:100',
            'full_name'       => 'required_without:name|nullable|string|max:100',
            'phone'           => 'required|string|max:20',
            'email'           => 'nullable|email|max:150',
            'destination'     => 'nullable|string|max:100',
            'travel_date'     => 'nullable|date',
            'pax_adults'      => 'nullable|integer|min:0',
            'pax_children'    => 'nullable|integer|min:0',
            'budget'          => 'nullable|numeric|min:0',
            'campaign_source' => 'nullable|string|max:100',
            'notes'           => 'nullable|string',
        ];
    }
}
