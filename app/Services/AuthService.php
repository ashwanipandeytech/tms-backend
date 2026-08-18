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
     * Authenticate user and issue Sanctum token.
     *
     * @return array{user: User, token: string}
     */
    public function login(string $email, string $password): array
    {
        $user = User::where('email', $email)->where('status', 'active')->first();

        if (!$user || !Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid email credentials or inactive account.'],
            ]);
        }

        $user->update(['last_login' => now()]);

        $token = $user->createToken('auth-token')->plainTextToken;

        return [
            'user'  => $user->load('role', 'company'),
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
