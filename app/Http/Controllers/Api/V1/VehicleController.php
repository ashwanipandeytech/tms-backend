<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseApiController;
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

    public function store(Request $request): JsonResponse
    {
        return $this->createdResponse($this->service->create($request->validate(['vehicle_type_id' => 'required|exists:vehicle_types,id', 'vendor_id' => 'nullable|exists:vendors,id', 'model' => 'nullable|string', 'number_plate' => 'nullable|string', 'status' => 'nullable|string'])), 'Vehicle created');
    }

    public function show(int|string $id): JsonResponse
    {
        return $this->successResponse($this->service->getById($id, ['vehicleType', 'vendor', 'drivers']), 'Vehicle details');
    }

    public function update(Request $request, int|string $id): JsonResponse
    {
        return $this->successResponse($this->service->update($id, $request->all()), 'Vehicle updated');
    }

    public function destroy(int|string $id): JsonResponse
    {
        $this->service->delete($id);
        return $this->successResponse(null, 'Vehicle deleted');
    }
}
