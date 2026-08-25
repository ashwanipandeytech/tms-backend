<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseApiController;
use App\Models\Role;
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
        return $this->paginatedResponse(
            $this->service->getPaginated((int) $request->input('per_page', 15), ['permissions'], $request->only(['search'])),
            'Roles retrieved successfully'
        );
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'          => 'required|string|max:50',
            'description'   => 'nullable|string|max:255',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = $this->service->create([
            'name'        => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        if (!empty($data['permissions'])) {
            $role->permissions()->sync($data['permissions']);
        }

        return $this->createdResponse($role->load('permissions'), 'Role created successfully');
    }

    public function show(int|string $id): JsonResponse
    {
        $role = Role::with(['permissions', 'users'])->findOrFail($id);
        return $this->successResponse($role, 'Role details retrieved');
    }

    public function update(Request $request, int|string $id): JsonResponse
    {
        $role = Role::findOrFail($id);

        $data = $request->validate([
            'name'          => 'sometimes|required|string|max:50',
            'description'   => 'nullable|string|max:255',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update($data);

        if (array_key_exists('permissions', $data)) {
            $role->permissions()->sync($data['permissions'] ?? []);
        }

        return $this->successResponse($role->load('permissions'), 'Role updated successfully');
    }

    public function destroy(int|string $id): JsonResponse
    {
        $this->service->delete($id);
        return $this->successResponse(null, 'Role deleted successfully');
    }
}
