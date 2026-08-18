<?php

declare(strict_types=1);

namespace App\Http\Requests;

class LoginRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ];
    }
}
