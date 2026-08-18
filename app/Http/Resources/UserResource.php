<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;

class UserResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'company_id' => $this->company_id,
            'name'       => $this->name,
            'email'      => $this->email,
            'phone'      => $this->phone,
            'avatar'     => $this->avatar,
            'status'     => $this->status?->value ?? $this->status,
            'role'       => $this->whenLoaded('role', fn() => [
                'id'          => $this->role->id,
                'name'        => $this->role->name,
                'permissions' => $this->role->permissions->map(fn($p) => $p->module . '.' . $p->action),
            ]),
            'last_login' => $this->last_login?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
