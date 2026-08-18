<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseApiController;
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

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:150',
            'destination_id' => 'nullable|exists:destinations,id',
            'category_id'    => 'nullable|exists:package_categories,id',
            'nights'         => 'nullable|integer|min:0',
            'days'           => 'nullable|integer|min:0',
            'price'          => 'required|numeric|min:0',
            'inclusions'     => 'nullable|string',
            'exclusions'     => 'nullable|string',
            'terms'          => 'nullable|string',
            'status'         => 'nullable|string',
        ]);

        $package = $this->service->create($validated);
        return $this->createdResponse(new PackageResource($package), 'Package created successfully');
    }

    public function show(int|string $id): JsonResponse
    {
        $package = $this->service->getById($id, ['destination', 'category', 'images']);
        return $this->successResponse(new PackageResource($package), 'Package details retrieved');
    }

    public function update(Request $request, int|string $id): JsonResponse
    {
        $validated = $request->validate([
            'name'           => 'sometimes|required|string|max:150',
            'destination_id' => 'nullable|exists:destinations,id',
            'category_id'    => 'nullable|exists:package_categories,id',
            'nights'         => 'nullable|integer|min:0',
            'days'           => 'nullable|integer|min:0',
            'price'          => 'sometimes|required|numeric|min:0',
            'inclusions'     => 'nullable|string',
            'exclusions'     => 'nullable|string',
            'terms'          => 'nullable|string',
            'status'         => 'nullable|string',
        ]);

        $package = $this->service->update($id, $validated);
        return $this->successResponse(new PackageResource($package), 'Package updated successfully');
    }

    public function destroy(int|string $id): JsonResponse
    {
        $this->service->delete($id);
        return $this->successResponse(null, 'Package deleted successfully');
    }
}
