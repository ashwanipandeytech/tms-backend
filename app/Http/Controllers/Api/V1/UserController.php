<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseApiController;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends BaseApiController
{
    protected UserService $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request): JsonResponse
    {
        $paginator = $this->service->getPaginated(
            perPage: (int) $request->input('per_page', 15),
            columns: ['*'],
            relations: ['role', 'company'],
            filters: $request->only(['search', 'status'])
        );

        return $this->paginatedResponse($paginator, 'Users retrieved successfully', UserResource::class);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'                  => 'required|string|max:100',
            'email'                 => 'required|email|max:150|unique:users,email',
            'phone'                 => 'nullable|string|max:20',
            'role_id'               => 'required|exists:roles,id',
            'password'              => 'required|string|min:6|confirmed',
            'status'                => 'nullable|string|in:active,inactive',
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['status'] = $data['status'] ?? 'active';

        $user = $this->service->create($data);
        return $this->createdResponse(new UserResource($user->load('role')), 'User created successfully');
    }

    public function show(int|string $id): JsonResponse
    {
        $user = $this->service->getById($id, ['role', 'company']);
        return $this->successResponse(new UserResource($user), 'User details retrieved');
    }

    public function update(Request $request, int|string $id): JsonResponse
    {
        $data = $request->validate([
            'name'                  => 'sometimes|required|string|max:100',
            'email'                 => 'sometimes|required|email|max:150|unique:users,email,' . $id,
            'phone'                 => 'nullable|string|max:20',
            'role_id'               => 'sometimes|required|exists:roles,id',
            'password'              => 'nullable|string|min:6|confirmed',
            'status'                => 'nullable|string|in:active,inactive',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user = $this->service->update($id, $data);
        return $this->successResponse(new UserResource($user->load('role')), 'User updated successfully');
    }

    public function destroy(int|string $id): JsonResponse
    {
        $this->service->delete($id);
        return $this->successResponse(null, 'User deleted successfully');
    }
}
