<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\BookingAssignOperationsRequest;
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
        $filters = $request->only(['search', 'status']);
        $user = $request->user();

        // RBAC Scoping for Operations Team
        if ($user && $user->role && str_contains(strtolower($user->role->name), 'operation')) {
            $filters['operations_id'] = $user->id;
        }

        $paginator = $this->service->getPaginated(
            perPage: (int) $request->input('per_page', 15),
            relations: ['customer', 'package', 'assignedOperations'],
            filters: $filters
        );

        return $this->paginatedResponse($paginator, 'Bookings retrieved successfully', BookingResource::class);
    }

    public function store(BookingRequest $request): JsonResponse
    {
        $booking = $this->service->create($request->validated());
        return $this->createdResponse(new BookingResource($booking), 'Booking created successfully');
    }

    public function show(Request $request, int|string $id): JsonResponse
    {
        $booking = $this->service->getById($id, ['customer', 'package', 'payments', 'invoice', 'assignedOperations']);
        $user = $request->user();

        if ($user && $user->role && str_contains(strtolower($user->role->name), 'operation') && (int) $booking->operations_id !== (int) $user->id) {
            return $this->errorResponse('Access denied to unassigned booking', 403, 'FORBIDDEN');
        }

        return $this->successResponse(new BookingResource($booking), 'Booking details retrieved');
    }

    public function update(BookingRequest $request, int|string $id): JsonResponse
    {
        $user = $request->user();
        $bookingModel = $this->service->getById($id);

        if ($user && $user->role && str_contains(strtolower($user->role->name), 'operation') && (int) $bookingModel->operations_id !== (int) $user->id) {
            return $this->errorResponse('Access denied to modify unassigned booking', 403, 'FORBIDDEN');
        }

        $booking = $this->service->update($id, $request->validated());
        return $this->successResponse(new BookingResource($booking), 'Booking updated successfully');
    }

    public function assignOperations(BookingAssignOperationsRequest $request, int|string $id): JsonResponse
    {
        $data = $request->validated();
        $booking = $this->service->update($id, ['operations_id' => $data['operations_id']]);
        return $this->successResponse(new BookingResource($booking), 'Operations fulfillment assigned successfully');
    }

    public function destroy(int|string $id): JsonResponse
    {
        $this->service->delete($id);
        return $this->successResponse(null, 'Booking deleted successfully');
    }
}
