<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Booking;
use App\Models\CabBooking;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\FollowUp;
use App\Models\Hotel;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Package;
use App\Models\Quotation;
use App\Models\Resort;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Vendor;
use App\Models\Villa;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckDemoPlanLimit
{
    /**
     * Map route path segments to their respective Eloquent Model classes.
     */
    protected array $routeModelMap = [
        'leads'       => Lead::class,
        'follow-ups'  => FollowUp::class,
        'bookings'    => Booking::class,
        'quotations'  => Quotation::class,
        'packages'    => Package::class,
        'hotels'      => Hotel::class,
        'resorts'     => Resort::class,
        'villas'      => Villa::class,
        'vendors'     => Vendor::class,
        'vehicles'    => Vehicle::class,
        'cab-bookings'=> CabBooking::class,
        'customers'   => Customer::class,
        'invoices'    => Invoice::class,
        'expenses'    => Expense::class,
        'users'       => User::class,
    ];

    public function handle(Request $request, Closure $next): Response
    {
        // Only inspect creation requests (POST)
        if ($request->method() !== 'POST') {
            return $next($request);
        }

        $user = $request->user();
        if (!$user || $user->isSuperAdmin()) {
            return $next($request);
        }

        $company = $user->company;
        $plan = $company?->subscriptionPlan;

        if ($plan && $plan->isDemoPlan()) {
            $path = $request->segment(3); // e.g. /api/v1/leads -> segment(3) is 'leads'

            if (isset($this->routeModelMap[$path])) {
                $modelClass = $this->routeModelMap[$path];
                $existingCount = $modelClass::where('company_id', $company->id)->count();

                if ($existingCount >= 1) {
                    return response()->json([
                        'success'    => false,
                        'error_code' => 'DEMO_PLAN_LIMIT_REACHED',
                        'message'    => "The Free Trial plan is restricted to 1 entry per module. Please upgrade your subscription plan to create additional {$path}.",
                        'errors'     => [
                            'demo_limit' => ["Free Trial limit of 1 entry reached for module '{$path}'."],
                        ],
                    ], 422);
                }
            }
        }

        return $next($request);
    }
}
