<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\CustomerStoreRequest;
use App\Http\Requests\CustomerUpdateRequest;
use App\Http\Resources\CustomerResource;
use App\Services\CustomerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends BaseApiController
{
    protected CustomerService $service;

    public function __construct(CustomerService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request): JsonResponse
    {
        $paginator = $this->service->getPaginated(
            perPage: (int) $request->input('per_page', 15),
            columns: ['*'],
            relations: [],
            filters: $request->only(['search', 'status'])
        );

        return $this->paginatedResponse($paginator, 'Customers retrieved successfully', CustomerResource::class);
    }

    public function store(CustomerStoreRequest $request): JsonResponse
    {
        $data = $request->validated();
        $customer = $this->service->create($data);
        return $this->createdResponse(new CustomerResource($customer), 'Customer created successfully');
    }

    public function show(int|string $id): JsonResponse
    {
        $customer = $this->service->getById($id, ['bookings']);
        return $this->successResponse(new CustomerResource($customer), 'Customer details retrieved');
    }

    public function update(CustomerUpdateRequest $request, int|string $id): JsonResponse
    {
        $data = $request->validated();
        $customer = $this->service->update($id, $data);
        return $this->successResponse(new CustomerResource($customer), 'Customer updated successfully');
    }

    public function destroy(int|string $id): JsonResponse
    {
        $this->service->delete($id);
        return $this->successResponse(null, 'Customer deleted successfully');
    }
}
