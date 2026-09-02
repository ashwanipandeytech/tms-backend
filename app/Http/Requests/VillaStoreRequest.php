<?php

declare(strict_types=1);

namespace App\Http\Requests;

class VillaStoreRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name'      => 'required|string|max:150',
            'price'     => 'required|numeric|min:0',
            'location'  => 'nullable|string|max:150',
            'capacity'  => 'nullable|integer|min:0',
            'bedrooms'  => 'nullable|integer|min:0',
            'amenities' => 'nullable|string',
            'status'    => 'nullable|string|in:active,inactive',
        ];
    }
}
