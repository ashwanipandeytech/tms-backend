<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\ExpenseStoreRequest;
use App\Services\ExpenseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExpenseController extends BaseApiController
{
    protected ExpenseService $service;

    public function __construct(ExpenseService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request): JsonResponse
    {
        return $this->paginatedResponse($this->service->getPaginated((int) $request->input('per_page', 15), ['creator'], $request->only(['search'])), 'Expenses retrieved');
    }

    public function store(ExpenseStoreRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();
        return $this->createdResponse($this->service->create($data), 'Expense recorded');
    }

    public function show(int|string $id): JsonResponse
    {
        return $this->successResponse($this->service->getById($id, ['creator']), 'Expense details');
    }

    public function update(ExpenseStoreRequest $request, int|string $id): JsonResponse
    {
        $data = $request->validated();
        return $this->successResponse($this->service->update($id, $data), 'Expense updated');
    }

    public function destroy(int|string $id): JsonResponse
    {
        $this->service->delete($id);
        return $this->successResponse(null, 'Expense deleted');
    }
}
