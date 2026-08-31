<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService extends BaseService
{
    public function __construct(UserRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    /**
     * Authenticate user, validate role, and issue Sanctum token.
     *
     * @return array{user: User, token: string}
     */
    public function login(string $email, string $password, ?string $roleType = null, int|string|null $roleId = null): array
    {
        $user = User::where('email', $email)->where('status', 'active')->first();

        if (!$user || !Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid email credentials or inactive account.'],
            ]);
        }

        // Validate Role Selection if passed by frontend
        if ($roleId && (int) $user->role_id !== (int) $roleId) {
            throw ValidationException::withMessages([
                'role_id' => ['Selected role does not match user assigned account role.'],
            ]);
        }

        if ($roleType && $user->role) {
            $normalizedUserRole = strtolower(str_replace(' ', '_', $user->role->name));
            $normalizedInputRole = strtolower(str_replace(' ', '_', $roleType));

            if (!str_contains($normalizedUserRole, $normalizedInputRole) && !str_contains($normalizedInputRole, $normalizedUserRole)) {
                throw ValidationException::withMessages([
                    'role_type' => ["Account is not assigned to the '{$roleType}' role."],
                ]);
            }
        }

        $user->update(['last_login' => now()]);

        $token = $user->createToken('auth-token')->plainTextToken;

        return [
            'user'  => $user->load(['role.permissions', 'company']),
            'token' => $token,
        ];
    }

    /**
     * Revoke current user token.
     */
    public function logout(User $user): void
    {
        $user->currentAccessToken()?->delete();
    }
}
