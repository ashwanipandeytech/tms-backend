<?php

declare(strict_types=1);

namespace App\Http\Requests;

class CustomerUpdateRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name'    => 'sometimes|required|string|max:100',
            'phone'   => 'sometimes|required|string|max:20',
            'email'   => 'nullable|email|max:150',
            'lead_id' => 'nullable|exists:leads,id',
            'status'  => 'nullable|string|in:active,inactive',
        ];
    }
}
