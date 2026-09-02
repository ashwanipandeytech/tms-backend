<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\LeadAssignRequest;
use App\Http\Requests\LeadImportCsvRequest;
use App\Http\Requests\LeadRequest;
use App\Http\Resources\LeadResource;
use App\Models\Lead;
use App\Services\LeadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeadController extends BaseApiController
{
    protected LeadService $service;

    public function __construct(LeadService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'status']);
        $user = $request->user();

        // RBAC Scoping for Sales Executive: limit leads to assigned leads
        if ($user && $user->role && str_contains(strtolower($user->role->name), 'sales')) {
            $filters['assigned_to'] = $user->id;
        }

        $paginator = $this->service->getPaginated(
            perPage: (int) $request->input('per_page', 15),
            relations: ['source', 'assignedUser'],
            filters: $filters
        );

        return $this->paginatedResponse($paginator, 'Leads retrieved successfully', LeadResource::class);
    }

    public function store(LeadRequest $request): JsonResponse
    {
        $lead = $this->service->create($request->validated());
        return $this->createdResponse(new LeadResource($lead), 'Lead created successfully');
    }

    public function show(Request $request, int|string $id): JsonResponse
    {
        $lead = $this->service->getById($id, ['source', 'assignedUser', 'activities', 'followUps']);
        $user = $request->user();

        // RBAC Verification
        if ($user && $user->role && str_contains(strtolower($user->role->name), 'sales') && (int) $lead->assigned_to !== (int) $user->id) {
            return $this->errorResponse('Access denied to unassigned lead', 403, 'FORBIDDEN');
        }

        return $this->successResponse(new LeadResource($lead), 'Lead details retrieved');
    }

    public function update(LeadRequest $request, int|string $id): JsonResponse
    {
        $user = $request->user();
        $leadModel = $this->service->getById($id);

        if ($user && $user->role && str_contains(strtolower($user->role->name), 'sales') && (int) $leadModel->assigned_to !== (int) $user->id) {
            return $this->errorResponse('Access denied to modify unassigned lead', 403, 'FORBIDDEN');
        }

        $lead = $this->service->update($id, $request->validated());
        return $this->successResponse(new LeadResource($lead), 'Lead updated successfully');
    }

    public function assign(LeadAssignRequest $request, int|string $id): JsonResponse
    {
        $data = $request->validated();
        $lead = $this->service->update($id, ['assigned_to' => $data['assigned_to']]);
        return $this->successResponse(new LeadResource($lead), 'Lead assigned successfully');
    }

    public function importCsv(LeadImportCsvRequest $request): JsonResponse
    {
        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle);

        $importedCount = 0;
        $skippedCount = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (empty($row[0]) || empty($row[1])) {
                continue;
            }

            $name = trim($row[0]);
            $phone = trim($row[1]);
            $email = isset($row[2]) ? trim($row[2]) : null;
            $destination = isset($row[3]) ? trim($row[3]) : null;

            // Avoid Duplicate Phones
            if (Lead::where('phone', $phone)->exists()) {
                $skippedCount++;
                continue;
            }

            $this->service->create([
                'name'            => $name,
                'phone'           => $phone,
                'email'           => $email,
                'destination'     => $destination,
                'campaign_source' => 'CSV Import',
            ]);

            $importedCount++;
        }

        fclose($handle);

        return $this->successResponse([
            'imported_count' => $importedCount,
            'skipped_count'  => $skippedCount,
        ], "CSV Import completed: {$importedCount} leads imported, {$skippedCount} duplicate leads skipped");
    }

    public function destroy(int|string $id): JsonResponse
    {
        $this->service->delete($id);
        return $this->successResponse(null, 'Lead deleted successfully');
    }
}
