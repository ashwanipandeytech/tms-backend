<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\LeadRequest;
use App\Http\Resources\LeadResource;
use App\Services\LeadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeadController extends BaseApiController
{
    protected LeadService $service;

    public function __construct(LeadService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request): JsonResponse
    {
        $paginator = $this->service->getPaginated(
            perPage: (int) $request->input('per_page', 15),
            relations: ['source', 'assignedUser'],
            filters: $request->only(['search', 'status'])
        );

        return $this->paginatedResponse($paginator, 'Leads retrieved successfully', LeadResource::class);
    }

    public function store(LeadRequest $request): JsonResponse
    {
        $lead = $this->service->create($request->validated());
        return $this->createdResponse(new LeadResource($lead), 'Lead created successfully');
    }

    public function show(int|string $id): JsonResponse
    {
        $lead = $this->service->getById($id, ['source', 'assignedUser', 'activities', 'followUps']);
        return $this->successResponse(new LeadResource($lead), 'Lead details retrieved');
    }

    public function update(LeadRequest $request, int|string $id): JsonResponse
    {
        $lead = $this->service->update($id, $request->validated());
        return $this->successResponse(new LeadResource($lead), 'Lead updated successfully');
    }

    public function destroy(int|string $id): JsonResponse
    {
        $this->service->delete($id);
        return $this->successResponse(null, 'Lead deleted successfully');
    }
}
