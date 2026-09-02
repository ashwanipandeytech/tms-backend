<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\LeadWebhookMetaRequest;
use App\Http\Requests\LeadWebhookWebsiteRequest;
use App\Http\Resources\LeadResource;
use App\Services\LeadService;
use Illuminate\Http\JsonResponse;

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
    public function handleMetaWebhook(LeadWebhookMetaRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['campaign_source'] = $data['campaign_source'] ?? 'Meta Ads';
        $lead = $this->service->create($data);

        return $this->createdResponse(new LeadResource($lead), 'Meta lead ingested successfully');
    }

    /**
     * Ingest Lead from Website contact / enquiry form webhook.
     */
    public function handleWebsiteWebhook(LeadWebhookWebsiteRequest $request): JsonResponse
    {
        $data = $request->validated();
        if (isset($data['full_name']) && !isset($data['name'])) {
            $data['name'] = $data['full_name'];
            unset($data['full_name']);
        }
        $data['campaign_source'] = $data['campaign_source'] ?? 'Website Form';
        $lead = $this->service->create($data);

        return $this->createdResponse(new LeadResource($lead), 'Website lead ingested successfully');
    }
}
