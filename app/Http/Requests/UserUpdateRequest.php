<?php

declare(strict_types=1);

namespace App\Http\Requests;

class UserUpdateRequest extends BaseRequest
{
    public function rules(): array
    {
        $userId = $this->route('user') ?? $this->route('id');

        return [
            'name'                  => 'sometimes|required|string|max:100',
            'email'                 => 'sometimes|required|email|max:150|unique:users,email,' . $userId,
            'phone'                 => 'nullable|string|max:20',
            'role_id'               => 'sometimes|required|exists:roles,id',
            'password'              => 'nullable|string|min:6|confirmed',
            'status'                => 'nullable|string|in:active,inactive',
        ];
    }
}
