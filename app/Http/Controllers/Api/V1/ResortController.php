<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\ResortStoreRequest;
use App\Services\ResortService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ResortController extends BaseApiController
{
    protected ResortService $service;

    public function __construct(ResortService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request): JsonResponse
    {
        $paginator = $this->service->getPaginated((int) $request->input('per_page', 15), ['rooms', 'images'], $request->only(['search', 'status']));
        return $this->paginatedResponse($paginator, 'Resorts retrieved');
    }

    public function store(ResortStoreRequest $request): JsonResponse
    {
        $resort = $this->service->create($request->validated());
        return $this->createdResponse($resort, 'Resort created');
    }

    public function show(int|string $id): JsonResponse
    {
        return $this->successResponse($this->service->getById($id, ['rooms', 'images']), 'Resort details');
    }

    public function update(ResortStoreRequest $request, int|string $id): JsonResponse
    {
        return $this->successResponse($this->service->update($id, $request->validated()), 'Resort updated');
    }

    public function destroy(int|string $id): JsonResponse
    {
        $this->service->delete($id);
        return $this->successResponse(null, 'Resort deleted');
    }
}
