<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\SubscriptionPlanStoreRequest;
use App\Http\Requests\SubscriptionPlanUpdateRequest;
use App\Models\SubscriptionPlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubscriptionPlanController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $plans = SubscriptionPlan::where('status', 'active')->get();

        // Optional authenticated user check (if Sanctum bearer token is provided)
        $user = auth('sanctum')->user();
        if ($user && $user->company) {
            $currentPlanId = $user->company->plan_id;
            $plans->transform(function ($plan) use ($currentPlanId) {
                $plan->is_current_plan = ((int) $plan->id === (int) $currentPlanId);
                return $plan;
            });
        }

        return $this->successResponse($plans, 'Subscription plans retrieved successfully');
    }

    public function store(SubscriptionPlanStoreRequest $request): JsonResponse
    {
        $data = $request->validated();

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        $data['database_type'] = $data['database_type'] ?? 'shared';
        $data['status'] = $data['status'] ?? 'active';

        $plan = SubscriptionPlan::create($data);
        return $this->createdResponse($plan, 'Subscription plan created successfully');
    }

    public function show(int|string $id): JsonResponse
    {
        $plan = SubscriptionPlan::findOrFail($id);
        return $this->successResponse($plan, 'Subscription plan details retrieved');
    }

    public function update(SubscriptionPlanUpdateRequest $request, int|string $id): JsonResponse
    {
        $plan = SubscriptionPlan::findOrFail($id);
        $data = $request->validated();

        if (isset($data['name']) && empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $plan->update($data);
        return $this->successResponse($plan, 'Subscription plan updated successfully');
    }

    public function destroy(int|string $id): JsonResponse
    {
        $plan = SubscriptionPlan::findOrFail($id);
        $plan->delete();
        return $this->successResponse(null, 'Subscription plan deleted successfully');
    }
}
