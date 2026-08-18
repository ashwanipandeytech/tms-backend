<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseApiController;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;

class ReportController extends BaseApiController
{
    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function leadsBySource(): JsonResponse
    {
        return $this->successResponse($this->reportService->getLeadsBySource(), 'Leads by source report');
    }

    public function salesByStaff(): JsonResponse
    {
        return $this->successResponse($this->reportService->getSalesByStaff(), 'Sales by staff report');
    }

    public function monthlyRevenue(): JsonResponse
    {
        return $this->successResponse($this->reportService->getMonthlyRevenue(), 'Monthly revenue report');
    }
}
