<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseApiController;
use App\Models\Company;
use App\Models\Permission;
use App\Models\Role;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TenantAdminController extends BaseApiController
{
    /**
     * List all Subscriber Companies.
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
        $paginated = Company::with(['subscriptionPlan', 'users.role'])
            ->latest()
            ->paginate((int) $request->input('per_page', 15));

        $paginated->getCollection()->transform(function ($company) {
            $users = $company->users->map(fn($user) => [
                'id'        => $user->id,
                'name'      => $user->name,
                'email'     => $user->email,
                'status'    => $user->status?->value ?? $user->status,
                'role_name' => $user->role?->name ?? 'Staff',
            ]);

            $daysRemaining = $company->subscription_ends_at
                ? max(0, (int) now()->diffInDays($company->subscription_ends_at, false))
                : null;

            return [
                'id'                  => $company->id,
                'company_name'        => $company->name,
                'subdomain'           => $company->subdomain,
                'status'              => $company->subscription_status?->value ?? $company->subscription_status,
                'created_at'          => $company->created_at?->toIso8601String(),
                'subscription'        => [
                    'plan_name'        => $company->subscriptionPlan?->name ?? 'N/A',
                    'status'           => $company->subscription_status?->value ?? $company->subscription_status,
                    'starts_at'        => $company->subscription_starts_at?->toIso8601String(),
                    'ends_at'          => $company->subscription_ends_at?->toIso8601String(),
                    'days_remaining'   => $daysRemaining,
                    'is_expiring_soon' => $daysRemaining !== null && $daysRemaining <= 7,
                ],
                'total_employees'     => $company->users->count(),
                'total_allowed_seats' => $company->total_allowed_seats,
                'employees'           => $users,
            ];
        });

        return $this->paginatedResponse($paginated, 'Company subscribers and statistics retrieved successfully');
    }

    /**
     * Setup & Onboard a New Company Subscriber Account (Super Admin action).
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'company_name'     => 'required|string|max:150',
            'subdomain'        => 'nullable|string|max:100|unique:companies,subdomain',
            'plan_id'          => 'required|exists:subscription_plans,id',
            'billing_cycle'    => 'nullable|string|in:monthly,yearly',
            'addon_user_seats' => 'nullable|integer|min:0',
            'database_type'    => 'nullable|string|in:shared,dedicated',
            'admin_name'       => 'required|string|max:100',
            'admin_email'      => 'required|email|max:150',
            'admin_phone'      => 'nullable|string|max:20',
            'initial_password' => 'required|string|min:6',
        ]);

        $plan = SubscriptionPlan::findOrFail($data['plan_id']);

        return DB::transaction(function () use ($data, $plan) {
            $subdomain = !empty($data['subdomain'])
                ? Str::slug($data['subdomain'])
                : Str::slug($data['company_name']);

            $billingCycle = $data['billing_cycle'] ?? 'monthly';
            $startsAt = now();
            $endsAt = $billingCycle === 'yearly' ? now()->addYear() : now()->addMonth();

            // 1. Create Company Subscriber
            $company = Company::create([
                'name'                   => $data['company_name'],
                'subdomain'              => $subdomain,
                'plan_id'                => $plan->id,
                'addon_user_seats'       => $data['addon_user_seats'] ?? 0,
                'subscription_status'    => 'active',
                'billing_cycle'          => $billingCycle,
                'subscription_starts_at' => $startsAt,
                'subscription_ends_at'   => $endsAt,
                'database_type'          => $data['database_type'] ?? $plan->database_type?->value ?? 'shared',
            ]);

            // 2. Seed Default Company Tenant Roles
            $roles = [
                ['name' => 'Manager', 'description' => 'Manage company leads, packages, bookings'],
                ['name' => 'Sales Executive', 'description' => 'Handle assigned leads & quotations'],
                ['name' => 'Operation Team', 'description' => 'Manage tour fulfillment, cabs, hotels'],
                ['name' => 'Accounts', 'description' => 'Finance, invoices, payments, expenses'],
            ];

            $tenantRoles = [];
            foreach ($roles as $roleData) {
                $role = Role::create([
                    'company_id'  => $company->id,
                    'name'        => $roleData['name'],
                    'description' => $roleData['description'],
                ]);
                $tenantRoles[$roleData['name']] = $role;
            }

            // Grant Manager Role all permissions
            $allPermissions = Permission::pluck('id')->toArray();
            if (isset($tenantRoles['Manager'])) {
                $tenantRoles['Manager']->permissions()->sync($allPermissions);
            }

            // 3. Create Primary Tenant Admin Account
            $tenantAdminRole = Role::where('name', 'Super Admin')->first() ?? $tenantRoles['Manager'];

            $adminUser = User::create([
                'company_id'      => $company->id,
                'role_id'         => $tenantAdminRole->id,
                'name'            => $data['admin_name'],
                'email'           => $data['admin_email'],
                'phone'           => $data['admin_phone'] ?? null,
                'password'        => Hash::make($data['initial_password']),
                'status'          => 'active',
                'created_by'      => auth()->id() ?? null,
                'created_by_type' => auth()->user()?->isSuperAdmin() ? 'super_admin' : 'tenant_admin',
            ]);

            return $this->createdResponse([
                'company'             => $company->load('subscriptionPlan'),
                'total_allowed_seats' => $company->total_allowed_seats,
                'tenant_admin'        => [
                    'id'    => $adminUser->id,
                    'name'  => $adminUser->name,
                    'email' => $adminUser->email,
                ],
            ], 'Company subscription account set up successfully');
        });
    }

    /**
     * Update Company Add-on User Seats.
     */
    public function updateAddonSeats(Request $request, int|string $id): JsonResponse
    {
        $request->validate([
            'addon_user_seats' => 'required|integer|min:0',
        ]);

        $company = Company::findOrFail($id);
        $company->update([
            'addon_user_seats' => (int) $request->input('addon_user_seats'),
        ]);

        return $this->successResponse([
            'company_id'          => $company->id,
            'company_name'        => $company->name,
            'base_user_seats'     => $company->subscriptionPlan?->base_user_seats ?? 5,
            'addon_user_seats'    => $company->addon_user_seats,
            'total_allowed_seats' => $company->total_allowed_seats,
        ], 'Add-on user seats updated successfully');
    }

    /**
     * Bulk Reset/Clear Tenant Data (supports clear_all: true or specific id / tenant_id payload).
     */
    public function resetTenantData(Request $request): JsonResponse
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

        return DB::transaction(function () use ($currentUser, $clearAll, $targetCompanyId) {
            // Delete staff users except current user and Super Admin (role_id 1)
            $usersQuery = User::query();
            if (!$clearAll && $targetCompanyId) {
                $usersQuery->where('company_id', $targetCompanyId);
            }
            $usersQuery->where('id', '!=', $currentUser->id)
                ->where('role_id', '!=', 1)
                ->delete();

            // Delete custom tenant roles except system default roles
            $rolesQuery = Role::query();
            if (!$clearAll && $targetCompanyId) {
                $rolesQuery->where('company_id', $targetCompanyId);
            }
            $rolesQuery->whereNotIn('name', ['Super Admin', 'Manager', 'Sales Executive', 'Operation Team', 'Accounts'])
                ->delete();

            // Clear tenant resource tables
            $tables = [
                \App\Models\Lead::class,
                \App\Models\FollowUp::class,
                \App\Models\Booking::class,
                \App\Models\Quotation::class,
                \App\Models\Package::class,
                \App\Models\Hotel::class,
                \App\Models\Resort::class,
                \App\Models\Villa::class,
                \App\Models\Vendor::class,
                \App\Models\Vehicle::class,
                \App\Models\CabBooking::class,
                \App\Models\Invoice::class,
                \App\Models\Payment::class,
                \App\Models\Expense::class,
                \App\Models\Customer::class,
            ];

            foreach ($tables as $modelClass) {
                if (class_exists($modelClass)) {
                    $q = $modelClass::query();
                    if (!$clearAll && $targetCompanyId) {
                        $q->where('company_id', $targetCompanyId);
                    }
                    $q->delete();
                }
            }

            $msg = $clearAll
                ? 'All tenant data reset successfully across all companies. Primary Super Admin account preserved.'
                : ($targetCompanyId ? "Tenant data for company ID {$targetCompanyId} reset successfully." : 'Tenant data reset successfully.');

            return $this->successResponse([
                'clear_all'       => $clearAll,
                'company_id'      => $targetCompanyId,
                'preserved_admin' => $currentUser->email,
                'status'          => 'reset_completed',
            ], $msg);
        });
    }
}
