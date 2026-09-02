<?php

declare(strict_types=1);

namespace App\Http\Requests;

class ResortStoreRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name'       => 'required|string|max:150',
            'location'   => 'nullable|string|max:150',
            'facilities' => 'nullable|string',
            'status'     => 'nullable|string|in:active,inactive',
        ];
    }
}
