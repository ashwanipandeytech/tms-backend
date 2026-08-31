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
            'last_login'   => $this->last_login?->toIso8601String(),
            'created_at'   => $this->created_at?->toIso8601String(),
        ];
    }
}
