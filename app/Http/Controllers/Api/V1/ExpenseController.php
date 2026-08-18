<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseApiController;
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

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate(['category' => 'required|string', 'amount' => 'required|numeric', 'description' => 'nullable|string', 'expense_date' => 'nullable|date']);
        $data['created_by'] = auth()->id();
        return $this->createdResponse($this->service->create($data), 'Expense recorded');
    }

    public function show(int|string $id): JsonResponse
    {
        return $this->successResponse($this->service->getById($id, ['creator']), 'Expense details');
    }

    public function update(Request $request, int|string $id): JsonResponse
    {
        return $this->successResponse($this->service->update($id, $request->all()), 'Expense updated');
    }

    public function destroy(int|string $id): JsonResponse
    {
        $this->service->delete($id);
        return $this->successResponse(null, 'Expense deleted');
    }
}
