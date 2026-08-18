<?php

declare(strict_types=1);

namespace App\Http\Requests;

class FollowUpRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'lead_id'        => ['required', 'integer', 'exists:leads,id'],
            'assigned_to'    => ['nullable', 'integer', 'exists:users,id'],
            'follow_up_date' => ['required', 'date'],
            'follow_up_time' => ['nullable', 'date_format:H:i,H:i:s'],
            'type'           => ['nullable', 'string'],
            'remarks'        => ['nullable', 'string', 'max:500'],
            'status'         => ['nullable', 'string'],
        ];
    }
}
