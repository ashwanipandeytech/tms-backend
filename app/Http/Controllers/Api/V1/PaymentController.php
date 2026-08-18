<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\PaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends BaseApiController
{
    protected PaymentService $service;

    public function __construct(PaymentService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request): JsonResponse
    {
        $paginator = $this->service->getPaginated(
            perPage: (int) $request->input('per_page', 15),
            relations: ['booking'],
            filters: $request->only(['search'])
        );

        return $this->paginatedResponse($paginator, 'Payments retrieved successfully', PaymentResource::class);
    }

    public function store(PaymentRequest $request): JsonResponse
    {
        $payment = $this->service->create($request->validated());
        return $this->createdResponse(new PaymentResource($payment), 'Payment recorded successfully');
    }

    public function show(int|string $id): JsonResponse
    {
        $payment = $this->service->getById($id, ['booking']);
        return $this->successResponse(new PaymentResource($payment), 'Payment details retrieved');
    }

    public function update(PaymentRequest $request, int|string $id): JsonResponse
    {
        $payment = $this->service->update($id, $request->validated());
        return $this->successResponse(new PaymentResource($payment), 'Payment updated successfully');
    }

    public function destroy(int|string $id): JsonResponse
    {
        $this->service->delete($id);
        return $this->successResponse(null, 'Payment deleted successfully');
    }
}
