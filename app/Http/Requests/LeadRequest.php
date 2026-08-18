<?php

declare(strict_types=1);

namespace App\Http\Requests;

class LeadRequest extends BaseRequest
{
    public function rules(): array
    {
        $isPost = $this->isMethod('post');

        return [
            'name'         => [$isPost ? 'required' : 'sometimes', 'string', 'max:100'],
            'phone'        => [$isPost ? 'required' : 'sometimes', 'string', 'max:20'],
            'email'        => ['nullable', 'email', 'max:150'],
            'source_id'    => ['nullable', 'integer', 'exists:lead_sources,id'],
            'destination'  => ['nullable', 'string', 'max:100'],
            'travel_date'  => ['nullable', 'date'],
            'pax_adults'   => ['nullable', 'integer', 'min:0'],
            'pax_children' => ['nullable', 'integer', 'min:0'],
            'budget'       => ['nullable', 'numeric', 'min:0'],
            'status'       => ['nullable', 'string'],
            'assigned_to'  => ['nullable', 'integer', 'exists:users,id'],
            'notes'        => ['nullable', 'string'],
        ];
    }
}
