<?php

declare(strict_types=1);

namespace App\Http\Requests;

class LeadStoreRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name'            => 'required|string|max:100',
            'email'           => 'nullable|email|max:150',
            'phone'           => 'required|string|max:20',
            'source_id'       => 'nullable|exists:lead_sources,id',
            'campaign_source' => 'nullable|string|max:100',
            'destination'     => 'nullable|string|max:100',
            'travel_date'     => 'nullable|date',
            'pax_adults'      => 'nullable|integer|min:0',
            'pax_children'    => 'nullable|integer|min:0',
            'budget'          => 'nullable|numeric|min:0',
            'status'          => 'nullable|string|in:new,contacted,followup,interested,quotation_sent,negotiation,confirmed,lost',
            'assigned_to'     => 'nullable|exists:users,id',
            'notes'           => 'nullable|string',
        ];
    }
}
