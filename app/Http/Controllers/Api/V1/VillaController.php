<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\VillaStoreRequest;
use App\Services\VillaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VillaController extends BaseApiController
{
    protected VillaService $service;

    public function __construct(VillaService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request): JsonResponse
    {
        $paginator = $this->service->getPaginated((int) $request->input('per_page', 15), ['images'], $request->only(['search', 'status']));
        return $this->paginatedResponse($paginator, 'Villas retrieved');
    }

    public function store(VillaStoreRequest $request): JsonResponse
    {
        $villa = $this->service->create($request->validated());
        return $this->createdResponse($villa, 'Villa created');
    }

    public function show(int|string $id): JsonResponse
    {
        return $this->successResponse($this->service->getById($id, ['images']), 'Villa details');
    }

    public function update(VillaStoreRequest $request, int|string $id): JsonResponse
    {
        return $this->successResponse($this->service->update($id, $request->validated()), 'Villa updated');
    }

    public function destroy(int|string $id): JsonResponse
    {
        $this->service->delete($id);
        return $this->successResponse(null, 'Villa deleted');
    }
}
