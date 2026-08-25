<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscriptionActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success'    => false,
                'error_code' => 'UNAUTHENTICATED',
                'message'    => 'Unauthenticated request.',
            ], 401);
        }

        // Super Admin bypasses subscription expiry check
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        $company = $user->company;

        if ($company && !$company->isSubscriptionActive()) {
            return response()->json([
                'success'    => false,
                'error_code' => 'SUBSCRIPTION_EXPIRED',
                'message'    => 'Your company subscription has expired or been suspended. Please renew your subscription to access API endpoints.',
            ], 402);
        }

        return $next($request);
    }
}
