<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\HotelStoreRequest;
use App\Http\Requests\HotelUpdateRequest;
use App\Services\HotelService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HotelController extends BaseApiController
{
    protected HotelService $service;

    public function __construct(HotelService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request): JsonResponse
    {
        $paginator = $this->service->getPaginated(
            perPage: (int) $request->input('per_page', 15),
            relations: ['rooms', 'images'],
            filters: $request->only(['search', 'status'])
        );
        return $this->paginatedResponse($paginator, 'Hotels retrieved successfully');
    }

    public function store(HotelStoreRequest $request): JsonResponse
    {
        $data = $request->validated();
        $hotel = $this->service->create($data);
        return $this->createdResponse($hotel, 'Hotel created successfully');
    }

    public function show(int|string $id): JsonResponse
    {
        $hotel = $this->service->getById($id, ['rooms', 'images']);
        return $this->successResponse($hotel, 'Hotel details retrieved');
    }

    public function update(HotelUpdateRequest $request, int|string $id): JsonResponse
    {
        $data = $request->validated();
        $hotel = $this->service->update($id, $data);
        return $this->successResponse($hotel, 'Hotel updated successfully');
    }

    public function destroy(int|string $id): JsonResponse
    {
        $this->service->delete($id);
        return $this->successResponse(null, 'Hotel deleted successfully');
    }
}
