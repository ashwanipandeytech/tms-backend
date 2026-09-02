<?php

declare(strict_types=1);

namespace App\Http\Requests;

class HotelStoreRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name'          => 'required|string|max:150',
            'location'      => 'nullable|string|max:150',
            'star_category' => 'nullable|integer|min:1|max:5',
            'contact_name'  => 'nullable|string|max:100',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:150',
            'rating'        => 'nullable|numeric|min:0|max:5',
            'status'        => 'nullable|string|in:active,inactive',
        ];
    }
}
