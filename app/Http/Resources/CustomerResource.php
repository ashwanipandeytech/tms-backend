<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;

class CustomerResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'email'      => $this->email,
            'phone'      => $this->phone,
            'city'       => $this->city,
            'status'     => $this->status?->value ?? $this->status,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
