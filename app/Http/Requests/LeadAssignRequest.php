<?php

declare(strict_types=1);

namespace App\Http\Requests;

class LeadAssignRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'assigned_to' => 'required|exists:users,id',
        ];
    }
}
