<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseApiController;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;

class DashboardController extends BaseApiController
{
    protected DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index(): JsonResponse
    {
        $data = $this->dashboardService->getDashboardMetrics();
        return $this->successResponse($data, 'Dashboard metrics retrieved successfully');
    }
}
