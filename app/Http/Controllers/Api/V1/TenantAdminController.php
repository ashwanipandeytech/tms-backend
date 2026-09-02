<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\DTOs\OnboardTenantDTO;
use App\Http\Controllers\BaseApiController;
use App\Http\Requests\OnboardTenantRequest;
use App\Http\Requests\ResetTenantDataRequest;
use App\Http\Requests\UpdateAddonSeatsRequest;
use App\Services\TenantAdminService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TenantAdminController extends BaseApiController
{
    protected TenantAdminService $service;

    public function __construct(TenantAdminService $service)
    {
        $this->service = $service;
    }

    /**
     * List all Subscriber Companies (Alias for companies method).
     */
    public function index(Request $request): JsonResponse
    {
        return $this->companies($request);
    }

    /**
     * List all Subscriber Companies with aggregated statistics for Super Admin dashboard.
     */
    public function companies(Request $request): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 15);
        $paginated = $this->service->getPaginatedCompanies($perPage);

        return $this->paginatedResponse($paginated, 'Company subscribers and statistics retrieved successfully');
    }

    /**
     * Setup & Onboard a New Company Subscriber Account (Super Admin action).
     */
    public function store(OnboardTenantRequest $request): JsonResponse
    {
        $dto = OnboardTenantDTO::fromRequest($request);
        $result = $this->service->onboardTenant($dto);

        return $this->createdResponse($result, 'Company subscription account set up successfully');
    }

    /**
     * Update Company Add-on User Seats.
     */
    public function updateAddonSeats(UpdateAddonSeatsRequest $request, int|string $id): JsonResponse
    {
        $addonSeats = (int) $request->input('addon_user_seats');
        $result = $this->service->updateAddonSeats((int) $id, $addonSeats);

        return $this->successResponse($result, 'Add-on user seats updated successfully');
    }

    /**
     * Bulk Reset/Clear Tenant Data (supports clear_all: true or specific id / tenant_id payload).
     */
    public function resetTenantData(ResetTenantDataRequest $request): JsonResponse
    {
        $currentUser = $request->user();

        if (!$currentUser) {
            return $this->errorResponse('Unauthenticated', 401);
        }

        $clearAll = $request->boolean('clear_all') || $request->input('clear_all') === true;
        $targetCompanyId = null;

        if (!$clearAll) {
            if ($request->filled('id')) {
                $targetCompanyId = (int) $request->input('id');
            } elseif ($request->filled('tenant_id')) {
                $targetCompanyId = (int) $request->input('tenant_id');
            } elseif ($request->header('X-Tenant-ID')) {
                $targetCompanyId = (int) $request->header('X-Tenant-ID');
            } else {
                $targetCompanyId = $currentUser->company_id;
            }
        }

        $result = $this->service->resetTenantData($currentUser, $clearAll, $targetCompanyId);

        $msg = $clearAll
            ? 'All tenant data reset successfully across all companies. Primary Super Admin account preserved.'
            : ($targetCompanyId ? "Tenant data for company ID {$targetCompanyId} reset successfully." : 'Tenant data reset successfully.');

        return $this->successResponse($result, $msg);
    }
}
