<?php

declare(strict_types=1);

namespace App\Http\Requests;

class CustomerStoreRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name'    => 'required|string|max:100',
            'phone'   => 'required|string|max:20',
            'email'   => 'nullable|email|max:150',
            'lead_id' => 'nullable|exists:leads,id',
            'status'  => 'nullable|string|in:active,inactive',
        ];
    }
}
