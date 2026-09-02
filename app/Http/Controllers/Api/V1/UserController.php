<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
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
        $currentUser = $request->user();

        $query = User::with(['role.permissions', 'company']);

        // Always exclude Super Admin accounts (role_id 1) from tenant user listings.
        // Super Admin is a platform-level account, not a tenant staff member.
        // Super Admin data is accessible only via GET /api/v1/me (profile endpoint).
        $query->where('role_id', '!=', 1);

        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $paginator = $query->latest()->paginate((int) $request->input('per_page', 15));

        return $this->paginatedResponse($paginator, 'Users retrieved successfully', UserResource::class);
    }

    public function store(UserStoreRequest $request): JsonResponse
    {
        $currentUser = $request->user();

        // Enforce User Seat Limit for Tenant Companies
        if ($currentUser && $currentUser->company) {
            $company = $currentUser->company;
            $activeUsers = User::where('company_id', $company->id)->where('status', 'active')->count();
            $allowedSeats = $company->total_allowed_seats;

            if ($activeUsers >= $allowedSeats) {
                return $this->errorResponse(
                    "User seat limit reached ({$allowedSeats} max seats). Please purchase add-on seats or upgrade your subscription plan.",
                    422,
                    ['seat_limit' => ["Maximum allowed user seats ({$allowedSeats}) reached."]],
                    'USER_SEAT_LIMIT_REACHED'
                );
            }
        }

        $data = $request->validated();

        $rawPassword = $data['password'];
        $data['password'] = Hash::make($data['password']);
        $data['status'] = $data['status'] ?? 'active';
        $data['created_by'] = $currentUser?->id;
        $data['created_by_type'] = $currentUser?->isSuperAdmin() ? 'super_admin' : 'tenant_admin';

        $user = $this->service->create($data);
        $user->demo_password = $rawPassword;

        return $this->createdResponse(new UserResource($user->load(['role.permissions', 'creator'])), 'User created successfully');
    }

    public function show(int|string $id): JsonResponse
    {
        $user = $this->service->getById($id, ['role.permissions', 'company']);
        return $this->successResponse(new UserResource($user), 'User details retrieved');
    }

    public function update(UserUpdateRequest $request, int|string $id): JsonResponse
    {
        $data = $request->validated();

        if (!empty($data['password'])) {
            $rawPassword = $data['password'];
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user = $this->service->update($id, $data);
        if (isset($rawPassword)) {
            $user->demo_password = $rawPassword;
        }

        return $this->successResponse(new UserResource($user->load('role.permissions')), 'User updated successfully');
    }

    public function destroy(int|string $id): JsonResponse
    {
        $this->service->delete($id);
        return $this->successResponse(null, 'User deleted successfully');
    }
}
