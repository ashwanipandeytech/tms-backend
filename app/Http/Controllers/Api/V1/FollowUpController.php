<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\FollowUpRequest;
use App\Http\Resources\FollowUpResource;
use App\Services\FollowUpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FollowUpController extends BaseApiController
{
    protected FollowUpService $service;

    public function __construct(FollowUpService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request): JsonResponse
    {
        $paginator = $this->service->getPaginated(
            perPage: (int) $request->input('per_page', 15),
            relations: ['lead', 'assignedUser'],
            filters: $request->only(['search', 'status'])
        );

        return $this->paginatedResponse($paginator, 'Follow-ups retrieved successfully', FollowUpResource::class);
    }

    public function store(FollowUpRequest $request): JsonResponse
    {
        $followUp = $this->service->create($request->validated());
        return $this->createdResponse(new FollowUpResource($followUp), 'Follow-up scheduled successfully');
    }

    public function show(int|string $id): JsonResponse
    {
        $followUp = $this->service->getById($id, ['lead', 'assignedUser']);
        return $this->successResponse(new FollowUpResource($followUp), 'Follow-up details retrieved');
    }

    public function update(FollowUpRequest $request, int|string $id): JsonResponse
    {
        $followUp = $this->service->update($id, $request->validated());
        return $this->successResponse(new FollowUpResource($followUp), 'Follow-up updated successfully');
    }

    public function destroy(int|string $id): JsonResponse
    {
        $this->service->delete($id);
        return $this->successResponse(null, 'Follow-up deleted successfully');
    }
}
