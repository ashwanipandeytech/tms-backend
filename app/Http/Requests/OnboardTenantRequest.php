<?php

declare(strict_types=1);

namespace App\Http\Requests;

class OnboardTenantRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'company_name'     => 'required|string|max:150',
            'subdomain'        => 'nullable|string|max:100|unique:companies,subdomain',
            'plan_id'          => 'required|exists:subscription_plans,id',
            'billing_cycle'    => 'nullable|string|in:monthly,yearly',
            'addon_user_seats' => 'nullable|integer|min:0',
            'database_type'    => 'nullable|string|in:shared,dedicated',
            'admin_name'       => 'required|string|max:100',
            'admin_email'      => 'required|email|max:150',
            'admin_phone'      => 'nullable|string|max:20',
            'initial_password' => 'required|string|min:6',
        ];
    }
}
