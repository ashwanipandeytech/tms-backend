<?php

declare(strict_types=1);

namespace App\Http\Requests;

class UserStoreRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name'                  => 'required|string|max:100',
            'email'                 => 'required|email|max:150|unique:users,email',
            'phone'                 => 'nullable|string|max:20',
            'role_id'               => 'required|exists:roles,id',
            'password'              => 'required|string|min:6|confirmed',
            'status'                => 'nullable|string|in:active,inactive',
        ];
    }
}
