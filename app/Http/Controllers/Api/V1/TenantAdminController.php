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
        $companies = Company::with(['subscriptionPlan', 'users'])
            ->latest()
            ->paginate((int) $request->input('per_page', 15));

        return $this->paginatedResponse($companies, 'Company subscribers retrieved successfully');
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
                'company_id' => $company->id,
                'role_id'    => $tenantAdminRole->id,
                'name'       => $data['admin_name'],
                'email'      => $data['admin_email'],
                'phone'      => $data['admin_phone'] ?? null,
                'password'   => Hash::make($data['initial_password']),
                'status'     => 'active',
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
}
