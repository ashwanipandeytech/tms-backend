<?php

declare(strict_types=1);

namespace App\Http\Requests;

class LeadImportCsvRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'file' => 'required|file|mimes:csv,txt|max:5120',
        ];
    }
}
