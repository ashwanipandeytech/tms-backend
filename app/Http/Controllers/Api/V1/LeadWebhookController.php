<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\LeadWebhookGoogleRequest;
use App\Http\Requests\LeadWebhookMetaRequest;
use App\Http\Requests\LeadWebhookWebsiteRequest;
use App\Http\Requests\LeadWebhookWhatsappRequest;
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
     * Handle Meta / Facebook Webhook Verification Challenge (GET Request).
     */
    public function verifyMetaWebhook(\Illuminate\Http\Request $request): \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
    {
        $verifyToken = env('META_VERIFY_TOKEN', 'safarsystem_meta_token');
        $mode = $request->query('hub_mode') ?? $request->query('hub.mode');
        $token = $request->query('hub_verify_token') ?? $request->query('hub.verify_token');
        $challenge = $request->query('hub_challenge') ?? $request->query('hub.challenge');

        if ($mode === 'subscribe' && $token === $verifyToken) {
            return response((string) $challenge, 200)->header('Content-Type', 'text/plain');
        }

        return response()->json(['error' => 'Verification token mismatch'], 403);
    }

    /**
     * Ingest Lead from Meta / Facebook & Instagram Ads campaign webhook.
     */
    public function handleMetaWebhook(LeadWebhookMetaRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['campaign_source'] = $data['campaign_source'] ?? 'Meta Facebook/Instagram Ads';
        $lead = $this->service->create($data);

        return $this->createdResponse(new LeadResource($lead), 'Meta lead ingested successfully');
    }

    /**
     * Ingest Lead from Google Ads Lead Form extension webhook.
     */
    public function handleGoogleWebhook(LeadWebhookGoogleRequest $request): JsonResponse
    {
        $data = $request->validated();
        if (isset($data['full_name']) && !isset($data['name'])) {
            $data['name'] = $data['full_name'];
            unset($data['full_name']);
        }
        $data['campaign_source'] = $data['campaign_source'] ?? 'Google Search Ads';
        $lead = $this->service->create($data);

        return $this->createdResponse(new LeadResource($lead), 'Google lead ingested successfully');
    }

    /**
     * Ingest Lead from WhatsApp Business Cloud API webhook / incoming message.
     */
    public function handleWhatsappWebhook(LeadWebhookWhatsappRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['name'] = $data['name'] ?? 'WhatsApp Lead (' . substr($data['phone'], -4) . ')';
        $data['campaign_source'] = $data['campaign_source'] ?? 'WhatsApp Direct';
        $data['notes'] = $data['message'] ?? $data['notes'] ?? null;
        unset($data['message']);

        $lead = $this->service->create($data);

        return $this->createdResponse(new LeadResource($lead), 'WhatsApp lead ingested successfully');
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
