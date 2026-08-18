<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseApiController;
use App\Services\VendorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VendorController extends BaseApiController
{
    protected VendorService $service;

    public function __construct(VendorService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request): JsonResponse
    {
        return $this->paginatedResponse($this->service->getPaginated((int) $request->input('per_page', 15), [], $request->only(['search'])), 'Vendors retrieved');
    }

    public function store(Request $request): JsonResponse
    {
        return $this->createdResponse($this->service->create($request->validate(['name' => 'required|string|max:120', 'type' => 'nullable|string', 'contact' => 'nullable|string', 'email' => 'nullable|email'])), 'Vendor created');
    }

    public function show(int|string $id): JsonResponse
    {
        return $this->successResponse($this->service->getById($id, ['vehicles']), 'Vendor details');
    }

    public function update(Request $request, int|string $id): JsonResponse
    {
        return $this->successResponse($this->service->update($id, $request->all()), 'Vendor updated');
    }

    public function destroy(int|string $id): JsonResponse
    {
        $this->service->delete($id);
        return $this->successResponse(null, 'Vendor deleted');
    }
}
