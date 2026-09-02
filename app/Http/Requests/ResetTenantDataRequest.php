<?php

declare(strict_types=1);

namespace App\Http\Requests;

class ResetTenantDataRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'clear_all' => 'nullable|boolean',
            'id'        => 'nullable|integer|exists:companies,id',
            'tenant_id' => 'nullable|integer|exists:companies,id',
        ];
    }
}
