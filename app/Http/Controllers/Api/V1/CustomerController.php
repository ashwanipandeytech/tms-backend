<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseApiController;
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
        return $this->paginatedResponse($this->service->getPaginated((int) $request->input('per_page', 15), [], $request->only(['search', 'status'])), 'Customers retrieved');
    }

    public function store(Request $request): JsonResponse
    {
        return $this->createdResponse($this->service->create($request->validate(['name' => 'required|string|max:100', 'phone' => 'required|string|max:20', 'email' => 'nullable|email', 'status' => 'nullable|string'])), 'Customer created');
    }

    public function show(int|string $id): JsonResponse
    {
        return $this->successResponse($this->service->getById($id, ['bookings']), 'Customer details');
    }

    public function update(Request $request, int|string $id): JsonResponse
    {
        return $this->successResponse($this->service->update($id, $request->all()), 'Customer updated');
    }

    public function destroy(int|string $id): JsonResponse
    {
        $this->service->delete($id);
        return $this->successResponse(null, 'Customer deleted');
    }
}
