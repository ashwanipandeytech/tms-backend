<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\RoleStoreRequest;
use App\Http\Requests\RoleUpdateRequest;
use App\Models\Role;
use App\Services\RoleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleController extends BaseApiController
{
    protected RoleService $service;

    public function __construct(RoleService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request): JsonResponse
    {
        $query = Role::with(['permissions'])->where('name', '!=', 'Super Admin');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->input('search') . '%');
        }

        $roles = $query->paginate((int) $request->input('per_page', 15));

        return $this->paginatedResponse($roles, 'Roles retrieved successfully');
    }

    public function store(RoleStoreRequest $request): JsonResponse
    {
        $data = $request->validated();

        if ($matchedExisting = $this->checkDuplicateRoleName($data['name'])) {
            return $this->errorResponse(
                "Role '{$data['name']}' cannot be created because a conflicting role or plural variant ('{$matchedExisting}') already exists.",
                422,
                ['name' => ["Role name or plural variant ('{$matchedExisting}') already exists."]],
                'DUPLICATE_ROLE_NAME'
            );
        }

        $role = $this->service->create([
            'company_id'  => null,
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

    public function update(RoleUpdateRequest $request, int|string $id): JsonResponse
    {
        $role = Role::findOrFail($id);
        $data = $request->validated();

        if (!empty($data['name']) && ($matchedExisting = $this->checkDuplicateRoleName($data['name'], (int) $id))) {
            return $this->errorResponse(
                "Role '{$data['name']}' cannot be saved because a conflicting role or plural variant ('{$matchedExisting}') already exists.",
                422,
                ['name' => ["Role name or plural variant ('{$matchedExisting}') already exists."]],
                'DUPLICATE_ROLE_NAME'
            );
        }

        $role->update($data);

        if (array_key_exists('permissions', $data)) {
            $role->permissions()->sync($data['permissions'] ?? []);
        }

        return $this->successResponse($role->load('permissions'), 'Role updated successfully');
    }

    public function destroy(int|string $id): JsonResponse
    {
        if (!auth()->check() || !auth()->user()->isSuperAdmin()) {
            return $this->errorResponse('Only Super Admin can delete universal roles.', 403, [], 'FORBIDDEN');
        }

        $this->service->delete($id);
        return $this->successResponse(null, 'Role deleted successfully');
    }

    /**
     * Check if a role with the same name or its singular/plural variant already exists.
     */
    protected function checkDuplicateRoleName(string $name, ?int $ignoreId = null): ?string
    {
        $inputTrimmed = strtolower(trim($name));
        $inputSingular = Str::singular($inputTrimmed);
        $inputPlural = Str::plural($inputTrimmed);

        $existingRoles = Role::query()
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->get(['id', 'name']);

        foreach ($existingRoles as $role) {
            $existingTrimmed = strtolower(trim($role->name));
            $existingSingular = Str::singular($existingTrimmed);
            $existingPlural = Str::plural($existingTrimmed);

            if (
                $existingTrimmed === $inputTrimmed ||
                $existingSingular === $inputSingular ||
                $existingPlural === $inputPlural ||
                $existingSingular === $inputTrimmed ||
                $existingPlural === $inputTrimmed
            ) {
                return $role->name;
            }
        }

        return null;
    }
}
