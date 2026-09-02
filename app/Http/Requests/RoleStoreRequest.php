<?php

declare(strict_types=1);

namespace App\Http\Requests;

class RoleStoreRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'name'          => 'required|string|max:50',
            'description'   => 'nullable|string|max:255',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ];
    }
}
