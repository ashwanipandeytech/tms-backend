<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\QuotationRequest;
use App\Http\Resources\QuotationResource;
use App\Services\QuotationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuotationController extends BaseApiController
{
    protected QuotationService $service;

    public function __construct(QuotationService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request): JsonResponse
    {
        $paginator = $this->service->getPaginated(
            perPage: (int) $request->input('per_page', 15),
            relations: ['lead', 'package'],
            filters: $request->only(['search', 'status'])
        );

        return $this->paginatedResponse($paginator, 'Quotations retrieved successfully', QuotationResource::class);
    }

    public function store(QuotationRequest $request): JsonResponse
    {
        $quotation = $this->service->create($request->validated());
        return $this->createdResponse(new QuotationResource($quotation), 'Quotation created successfully');
    }

    public function show(int|string $id): JsonResponse
    {
        $quotation = $this->service->getById($id, ['lead', 'package', 'items', 'itinerary']);
        return $this->successResponse(new QuotationResource($quotation), 'Quotation details retrieved');
    }

    public function update(QuotationRequest $request, int|string $id): JsonResponse
    {
        $quotation = $this->service->update($id, $request->validated());
        return $this->successResponse(new QuotationResource($quotation), 'Quotation updated successfully');
    }

    public function destroy(int|string $id): JsonResponse
    {
        $this->service->delete($id);
        return $this->successResponse(null, 'Quotation deleted successfully');
    }
}
