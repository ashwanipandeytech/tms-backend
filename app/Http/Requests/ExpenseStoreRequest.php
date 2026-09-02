<?php

declare(strict_types=1);

namespace App\Http\Requests;

class ExpenseStoreRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'category'     => 'required|string|max:60',
            'amount'       => 'required|numeric|min:0',
            'description'  => 'nullable|string|max:255',
            'expense_date' => 'nullable|date',
        ];
    }
}
