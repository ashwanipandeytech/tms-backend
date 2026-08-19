<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseApiController;
use App\Http\Resources\LeadResource;
use App\Services\LeadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeadWebhookController extends BaseApiController
{
    protected LeadService $service;

    public function __construct(LeadService $service)
    {
        $this->service = $service;
    }

    /**
     * Ingest Lead from Meta / Facebook Ads campaign webhook.
     */
    public function handleMetaWebhook(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'            => 'required|string|max:100',
            'phone'           => 'required|string|max:20',
            'email'           => 'nullable|email|max:150',
            'destination'     => 'nullable|string|max:100',
            'campaign_source' => 'nullable|string|max:100',
            'notes'           => 'nullable|string',
        ]);

        $data['campaign_source'] = $data['campaign_source'] ?? 'Meta Ads';
        $lead = $this->service->create($data);

        return $this->createdResponse(new LeadResource($lead), 'Meta lead ingested successfully');
    }

    /**
     * Ingest Lead from Website contact / enquiry form webhook.
     */
    public function handleWebsiteWebhook(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'            => 'required|string|max:100',
            'phone'           => 'required|string|max:20',
            'email'           => 'nullable|email|max:150',
            'destination'     => 'nullable|string|max:100',
            'travel_date'     => 'nullable|date',
            'pax_adults'      => 'nullable|integer|min:0',
            'pax_children'    => 'nullable|integer|min:0',
            'budget'          => 'nullable|numeric|min:0',
            'campaign_source' => 'nullable|string|max:100',
            'notes'           => 'nullable|string',
        ]);

        $data['campaign_source'] = $data['campaign_source'] ?? 'Website Form';
        $lead = $this->service->create($data);

        return $this->createdResponse(new LeadResource($lead), 'Website lead ingested successfully');
    }
}
