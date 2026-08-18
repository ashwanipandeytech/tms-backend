<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Traits\ApiResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseRequest extends FormRequest
{
    use ApiResponseTrait;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            $this->errorResponse(
                message: 'Validation failed',
                code: Response::HTTP_UNPROCESSABLE_ENTITY,
                errors: $validator->errors()->toArray(),
                errorCode: 'VALIDATION_ERROR'
            )
        );
    }
}
