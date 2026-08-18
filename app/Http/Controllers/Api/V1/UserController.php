<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseApiController;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends BaseApiController
{
    protected UserService $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request): JsonResponse
    {
        return $this->paginatedResponse($this->service->getPaginated((int) $request->input('per_page', 15), ['role', 'company'], $request->only(['search', 'status'])), 'Users retrieved');
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate(['name' => 'required|string|max:100', 'email' => 'required|email|unique:users,email', 'phone' => 'nullable|string', 'role_id' => 'required|exists:roles,id', 'password' => 'required|string|min:6', 'status' => 'nullable|string']);
        return $this->createdResponse($this->service->create($data), 'User created');
    }

    public function show(int|string $id): JsonResponse
    {
        return $this->successResponse($this->service->getById($id, ['role', 'company']), 'User details');
    }

    public function update(Request $request, int|string $id): JsonResponse
    {
        $data = $request->validate(['name' => 'sometimes|required|string', 'email' => 'sometimes|required|email|unique:users,email,'.$id, 'phone' => 'nullable|string', 'role_id' => 'sometimes|required|exists:roles,id', 'password' => 'nullable|string|min:6', 'status' => 'nullable|string']);
        if (empty($data['password'])) {
            unset($data['password']);
        }
        return $this->successResponse($this->service->update($id, $data), 'User updated');
    }

    public function destroy(int|string $id): JsonResponse
    {
        $this->service->delete($id);
        return $this->successResponse(null, 'User deleted');
    }
}
