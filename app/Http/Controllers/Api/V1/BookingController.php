<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\BookingRequest;
use App\Http\Resources\BookingResource;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingController extends BaseApiController
{
    protected BookingService $service;

    public function __construct(BookingService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request): JsonResponse
    {
        $paginator = $this->service->getPaginated(
            perPage: (int) $request->input('per_page', 15),
            relations: ['customer', 'package'],
            filters: $request->only(['search', 'status'])
        );

        return $this->paginatedResponse($paginator, 'Bookings retrieved successfully', BookingResource::class);
    }

    public function store(BookingRequest $request): JsonResponse
    {
        $booking = $this->service->create($request->validated());
        return $this->createdResponse(new BookingResource($booking), 'Booking created successfully');
    }

    public function show(int|string $id): JsonResponse
    {
        $booking = $this->service->getById($id, ['customer', 'package', 'payments', 'invoice']);
        return $this->successResponse(new BookingResource($booking), 'Booking details retrieved');
    }

    public function update(BookingRequest $request, int|string $id): JsonResponse
    {
        $booking = $this->service->update($id, $request->validated());
        return $this->successResponse(new BookingResource($booking), 'Booking updated successfully');
    }

    public function destroy(int|string $id): JsonResponse
    {
        $this->service->delete($id);
        return $this->successResponse(null, 'Booking deleted successfully');
    }
}
