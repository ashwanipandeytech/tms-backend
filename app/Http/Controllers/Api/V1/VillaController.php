<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseApiController;
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

    public function store(Request $request): JsonResponse
    {
        $villa = $this->service->create($request->validate(['name' => 'required|string|max:150', 'price' => 'required|numeric|min:0', 'location' => 'nullable|string', 'capacity' => 'nullable|integer', 'bedrooms' => 'nullable|integer', 'amenities' => 'nullable|string']));
        return $this->createdResponse($villa, 'Villa created');
    }

    public function show(int|string $id): JsonResponse
    {
        return $this->successResponse($this->service->getById($id, ['images']), 'Villa details');
    }

    public function update(Request $request, int|string $id): JsonResponse
    {
        return $this->successResponse($this->service->update($id, $request->all()), 'Villa updated');
    }

    public function destroy(int|string $id): JsonResponse
    {
        $this->service->delete($id);
        return $this->successResponse(null, 'Villa deleted');
    }
}
