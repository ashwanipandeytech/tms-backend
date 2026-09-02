<?php

declare(strict_types=1);

namespace App\Http\Requests;

class BookingAssignOperationsRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'operations_id' => 'required|exists:users,id',
        ];
    }
}
