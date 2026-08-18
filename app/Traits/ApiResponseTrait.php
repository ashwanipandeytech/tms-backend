<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\Response;

trait ApiResponseTrait
{
    /**
     * Return a success JSON response.
     */
    protected function successResponse(
        mixed $data = null,
        string $message = 'Success',
        int $code = Response::HTTP_OK,
        array $meta = []
    ): JsonResponse {
        $response = [
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ];

        if (!empty($meta)) {
            $response['meta'] = $meta;
        }

        return response()->json($response, $code);
    }

    /**
     * Return a created (201) JSON response.
     */
    protected function createdResponse(
        mixed $data = null,
        string $message = 'Resource created successfully'
    ): JsonResponse {
        return $this->successResponse($data, $message, Response::HTTP_CREATED);
    }

    /**
     * Return a no content (204) response.
     */
    protected function noContentResponse(): JsonResponse
    {
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Return an error JSON response.
     */
    protected function errorResponse(
        string $message = 'An error occurred',
        int $code = Response::HTTP_BAD_REQUEST,
        array $errors = [],
        ?string $errorCode = null
    ): JsonResponse {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errorCode !== null) {
            $response['error_code'] = $errorCode;
        }

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    /**
     * Return a paginated JSON response.
     */
    protected function paginatedResponse(
        LengthAwarePaginator $paginator,
        string $message = 'Data retrieved successfully',
        mixed $resourceClass = null
    ): JsonResponse {
        $items = $resourceClass !== null
            ? $resourceClass::collection($paginator->items())
            : $paginator->items();

        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $items,
            'meta'    => [
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
                'from'         => $paginator->firstItem(),
                'to'           => $paginator->lastItem(),
            ],
        ], Response::HTTP_OK);
    }
}
