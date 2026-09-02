<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\CabBookingStoreRequest;
use App\Services\CabBookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CabBookingController extends BaseApiController
{
    protected CabBookingService $service;

    public function __construct(CabBookingService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request): JsonResponse
    {
        return $this->paginatedResponse(
            $this->service->getPaginated((int) $request->input('per_page', 15), ['lead', 'vehicle', 'driver'], $request->only(['search', 'status'])),
            'Cab bookings retrieved'
        );
    }

    public function store(CabBookingStoreRequest $request): JsonResponse
    {
        $data = $request->validated();
        return $this->createdResponse($this->service->create($data), 'Cab booking created');
    }

    public function show(int|string $id): JsonResponse
    {
        return $this->successResponse($this->service->getById($id, ['lead', 'vehicle', 'driver']), 'Cab booking details');
    }

    public function update(CabBookingStoreRequest $request, int|string $id): JsonResponse
    {
        $data = $request->validated();
        return $this->successResponse($this->service->update($id, $data), 'Cab booking updated');
    }

    public function destroy(int|string $id): JsonResponse
    {
        $this->service->delete($id);
        return $this->successResponse(null, 'Cab booking deleted');
    }
}
