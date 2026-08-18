<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseApiController;
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

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'          => 'required|string|max:150',
            'location'      => 'nullable|string|max:150',
            'star_category' => 'nullable|integer|min:1|max:5',
            'contact_name'  => 'nullable|string',
            'contact_phone' => 'nullable|string',
            'contact_email' => 'nullable|email',
            'rating'        => 'nullable|numeric|min:0|max:5',
            'status'        => 'nullable|string',
        ]);
        $hotel = $this->service->create($data);
        return $this->createdResponse($hotel, 'Hotel created successfully');
    }

    public function show(int|string $id): JsonResponse
    {
        $hotel = $this->service->getById($id, ['rooms', 'images']);
        return $this->successResponse($hotel, 'Hotel details retrieved');
    }

    public function update(Request $request, int|string $id): JsonResponse
    {
        $data = $request->validate([
            'name'          => 'sometimes|required|string|max:150',
            'location'      => 'nullable|string|max:150',
            'star_category' => 'nullable|integer|min:1|max:5',
            'contact_name'  => 'nullable|string',
            'contact_phone' => 'nullable|string',
            'contact_email' => 'nullable|email',
            'rating'        => 'nullable|numeric|min:0|max:5',
            'status'        => 'nullable|string',
        ]);
        $hotel = $this->service->update($id, $data);
        return $this->successResponse($hotel, 'Hotel updated successfully');
    }

    public function destroy(int|string $id): JsonResponse
    {
        $this->service->delete($id);
        return $this->successResponse(null, 'Hotel deleted successfully');
    }
}
