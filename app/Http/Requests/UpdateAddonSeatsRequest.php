<?php

declare(strict_types=1);

namespace App\Http\Requests;

class UpdateAddonSeatsRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'addon_user_seats' => 'required|integer|min:0',
        ];
    }
}
