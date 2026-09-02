<?php

declare(strict_types=1);

namespace App\Http\Requests;

class PackageStoreRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name'           => 'required|string|max:150',
            'destination_id' => 'nullable|exists:destinations,id',
            'category_id'    => 'nullable|exists:package_categories,id',
            'nights'         => 'nullable|integer|min:0',
            'days'           => 'nullable|integer|min:0',
            'price'          => 'required|numeric|min:0',
            'gst_applicable' => 'nullable|boolean',
            'gst_percent'    => 'nullable|numeric|min:0|max:100',
            'inclusions'     => 'nullable|string',
            'exclusions'     => 'nullable|string',
            'terms'          => 'nullable|string',
            'status'         => 'nullable|string|in:active,inactive',
        ];
    }
}
