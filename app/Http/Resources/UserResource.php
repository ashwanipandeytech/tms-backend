<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;

class UserResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'company_id'   => $this->company_id,
            'company_name' => $this->whenLoaded('company', fn() => $this->company->name),
            'name'         => $this->name,
            'email'        => $this->email,
            'phone'        => $this->phone,
            'avatar'       => $this->avatar,
            'status'       => $this->status?->value ?? $this->status,
            'role'         => $this->whenLoaded('role', fn() => [
                'id'          => $this->role->id,
                'name'        => $this->role->name,
                'permissions' => $this->role->relationLoaded('permissions')
                    ? $this->role->permissions->map(fn($p) => $p->module . '.' . $p->action)->values()
                    : [],
            ]),
            'company'      => $this->whenLoaded('company', fn() => [
                'id'                  => $this->company->id,
                'name'                => $this->company->name,
                'company_name'        => $this->company->name,
                'subdomain'           => $this->company->subdomain,
                'subscription_status' => $this->company->subscription_status?->value ?? $this->company->subscription_status,
                'total_allowed_seats' => $this->company->total_allowed_seats,
            ]),
            'created_by'   => $this->relationLoaded('creator') && $this->creator ? [
                'id'              => $this->creator->id,
                'name'            => $this->creator->name,
                'email'           => $this->creator->email,
                'created_by_type' => $this->created_by_type ?? ($this->creator->isSuperAdmin() ? 'super_admin' : 'tenant_admin'),
            ] : ($this->created_by ? [
                'id'              => $this->created_by,
                'created_by_type' => $this->created_by_type ?? 'staff',
            ] : null),
            'created_by_type' => $this->created_by_type ?? ($this->relationLoaded('creator') && $this->creator ? ($this->creator->isSuperAdmin() ? 'super_admin' : 'tenant_admin') : null),
            'last_login'   => $this->last_login?->toIso8601String(),
            'created_at'   => $this->created_at?->toIso8601String(),
        ];
    }
}
