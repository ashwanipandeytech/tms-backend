<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\PackageStoreRequest;
use App\Http\Requests\PackageUpdateRequest;
use App\Http\Resources\PackageResource;
use App\Services\PackageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PackageController extends BaseApiController
{
    protected PackageService $service;

    public function __construct(PackageService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request): JsonResponse
    {
        $paginator = $this->service->getPaginated(
            perPage: (int) $request->input('per_page', 15),
            relations: ['destination', 'category'],
            filters: $request->only(['search', 'status'])
        );

        return $this->paginatedResponse($paginator, 'Packages retrieved successfully', PackageResource::class);
    }

    public function store(PackageStoreRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $package = $this->service->create($validated);
        return $this->createdResponse(new PackageResource($package), 'Package created successfully');
    }

    public function show(int|string $id): JsonResponse
    {
        $package = $this->service->getById($id, ['destination', 'category', 'images']);
        return $this->successResponse(new PackageResource($package), 'Package details retrieved');
    }

    public function update(PackageUpdateRequest $request, int|string $id): JsonResponse
    {
        $validated = $request->validated();
        $package = $this->service->update($id, $validated);
        return $this->successResponse(new PackageResource($package), 'Package updated successfully');
    }

    public function destroy(int|string $id): JsonResponse
    {
        $this->service->delete($id);
        return $this->successResponse(null, 'Package deleted successfully');
    }
}
