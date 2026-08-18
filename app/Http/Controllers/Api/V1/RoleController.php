<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseApiController;
use App\Services\RoleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoleController extends BaseApiController
{
    protected RoleService $service;

    public function __construct(RoleService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request): JsonResponse
    {
        return $this->paginatedResponse($this->service->getPaginated((int) $request->input('per_page', 15), ['permissions'], $request->only(['search'])), 'Roles retrieved');
    }

    public function store(Request $request): JsonResponse
    {
        return $this->createdResponse($this->service->create($request->validate(['name' => 'required|string|max:50', 'description' => 'nullable|string'])), 'Role created');
    }

    public function show(int|string $id): JsonResponse
    {
        return $this->successResponse($this->service->getById($id, ['permissions', 'users']), 'Role details');
    }

    public function update(Request $request, int|string $id): JsonResponse
    {
        return $this->successResponse($this->service->update($id, $request->all()), 'Role updated');
    }

    public function destroy(int|string $id): JsonResponse
    {
        $this->service->delete($id);
        return $this->successResponse(null, 'Role deleted');
    }
}
