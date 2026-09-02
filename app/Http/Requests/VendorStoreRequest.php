<?php

declare(strict_types=1);

namespace App\Http\Requests;

class VendorStoreRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name'    => 'required|string|max:120',
            'type'    => 'nullable|string|max:50',
            'contact' => 'nullable|string|max:20',
            'email'   => 'nullable|email|max:150',
            'address' => 'nullable|string|max:255',
        ];
    }
}
