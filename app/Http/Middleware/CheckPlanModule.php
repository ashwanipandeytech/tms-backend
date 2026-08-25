<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPlanModule
{
    public function handle(Request $request, Closure $next, string $module): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success'    => false,
                'error_code' => 'UNAUTHENTICATED',
                'message'    => 'Unauthenticated request.',
            ], 401);
        }

        // Super Admin bypasses plan module checks
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        $company = $user->company;
        $plan = $company?->subscriptionPlan;

        if ($plan && !$plan->hasModule($module)) {
            return response()->json([
                'success'    => false,
                'error_code' => 'PLAN_FEATURE_RESTRICTED',
                'message'    => "The '{$module}' module is not enabled under your company's current '{$plan->name}'. Please upgrade your plan to access this feature.",
            ], 403);
        }

        return $next($request);
    }
}
