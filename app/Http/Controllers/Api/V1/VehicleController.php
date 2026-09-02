<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\VehicleStoreRequest;
use App\Services\VehicleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VehicleController extends BaseApiController
{
    protected VehicleService $service;

    public function __construct(VehicleService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request): JsonResponse
    {
        return $this->paginatedResponse($this->service->getPaginated((int) $request->input('per_page', 15), ['vehicleType', 'vendor'], $request->only(['search', 'status'])), 'Vehicles retrieved');
    }

    public function store(VehicleStoreRequest $request): JsonResponse
    {
        $data = $request->validated();
        return $this->createdResponse($this->service->create($data), 'Vehicle created');
    }

    public function show(int|string $id): JsonResponse
    {
        return $this->successResponse($this->service->getById($id, ['vehicleType', 'vendor', 'drivers']), 'Vehicle details');
    }

    public function update(VehicleStoreRequest $request, int|string $id): JsonResponse
    {
        $data = $request->validated();
        return $this->successResponse($this->service->update($id, $data), 'Vehicle updated');
    }

    public function destroy(int|string $id): JsonResponse
    {
        $this->service->delete($id);
        return $this->successResponse(null, 'Vehicle deleted');
    }
}
