<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\OnboardTenantDTO;
use App\Models\Company;
use App\Models\Role;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TenantAdminService
{
    /**
     * Get paginated list of subscriber companies with aggregated statistics.
     */
    public function getPaginatedCompanies(int $perPage = 15): LengthAwarePaginator
    {
        $paginated = Company::with(['subscriptionPlan', 'users.role'])
            ->latest()
            ->paginate($perPage);

        $paginated->getCollection()->transform(function ($company) {
            $tenantUsers = $company->users->filter(fn($user) => (int) $user->role_id !== 1);
            $users = $tenantUsers->map(function ($user) {
                $defaultPassword = match ($user->role?->name) {
                    'Manager'         => 'Manager@123',
                    'Sales Executive' => 'Sales@123',
                    'Operation Team'  => 'Ops@123',
                    'Accounts'        => 'Accounts@123',
                    default           => 'Password@123',
                };

                return [
                    'id'            => $user->id,
                    'name'          => $user->name,
                    'email'         => $user->email,
                    'status'        => $user->status?->value ?? $user->status,
                    'role_name'     => $user->role?->name ?? 'Staff',
                    'demo_password' => $defaultPassword,
                ];
            })->values();

            $createdAt = $company->created_at ?? now();
            $startsAt = $company->subscription_starts_at ?? $createdAt;
            $endsAt = $company->subscription_ends_at ?? $startsAt->copy()->addMonth();

            $daysRemaining = max(0, (int) now()->diffInDays($endsAt, false));

            return [
                'id'                  => $company->id,
                'company_name'        => $company->name,
                'subdomain'           => $company->subdomain,
                'status'              => $company->subscription_status?->value ?? $company->subscription_status ?? 'active',
                'created_at'          => $createdAt->toIso8601String(),
                'subscription'        => [
                    'plan_name'        => $company->subscriptionPlan?->name ?? 'N/A',
                    'status'           => $company->subscription_status?->value ?? $company->subscription_status ?? 'active',
                    'starts_at'        => $startsAt->toIso8601String(),
                    'ends_at'          => $endsAt->toIso8601String(),
                    'days_remaining'   => $daysRemaining,
                    'is_expiring_soon' => $daysRemaining <= 7,
                ],
                'total_employees'     => $tenantUsers->count(),
                'total_allowed_seats' => $company->total_allowed_seats,
                'employees'           => $users,
            ];
        });

        return $paginated;
    }

    /**
     * Setup & Onboard a New Company Subscriber Account within DB transaction.
     */
    public function onboardTenant(OnboardTenantDTO $dto): array
    {
        $plan = SubscriptionPlan::findOrFail($dto->planId);

        return DB::transaction(function () use ($dto, $plan) {
            $subdomain = !empty($dto->subdomain)
                ? Str::slug($dto->subdomain)
                : Str::slug($dto->companyName);

            $startsAt = now();
            $endsAt = $dto->billingCycle === 'yearly' ? now()->addYear() : now()->addMonth();

            // 1. Create Company Subscriber
            $company = Company::create([
                'name'                   => $dto->companyName,
                'subdomain'              => $subdomain,
                'plan_id'                => $plan->id,
                'addon_user_seats'       => $dto->addonUserSeats,
                'subscription_status'    => 'active',
                'billing_cycle'          => $dto->billingCycle,
                'subscription_starts_at' => $startsAt,
                'subscription_ends_at'   => $endsAt,
                'database_type'          => $dto->databaseType ?? $plan->database_type?->value ?? 'shared',
            ]);

            // 2. Assign Primary Tenant Admin Account to Universal Global Manager Role
            $managerRole = Role::where('name', 'Manager')->first()
                ?? Role::where('name', 'Super Admin')->first();

            if (!$managerRole) {
                $managerRole = Role::create([
                    'company_id'  => null,
                    'name'        => 'Manager',
                    'description' => 'Manage company leads, packages, bookings',
                ]);
            }

            // 3. Create Primary Tenant Admin Account
            $adminUser = User::create([
                'company_id'      => $company->id,
                'role_id'         => $managerRole->id,
                'name'            => $dto->adminName,
                'email'           => $dto->adminEmail,
                'phone'           => $dto->adminPhone,
                'password'        => Hash::make($dto->initialPassword),
                'status'          => 'active',
                'created_by'      => auth()->id() ?? null,
                'created_by_type' => auth()->user()?->isSuperAdmin() ? 'super_admin' : 'tenant_admin',
            ]);

            return [
                'company'             => $company->load('subscriptionPlan'),
                'total_allowed_seats' => $company->total_allowed_seats,
                'tenant_admin'        => [
                    'id'    => $adminUser->id,
                    'name'  => $adminUser->name,
                    'email' => $adminUser->email,
                ],
            ];
        });
    }

    /**
     * Update Company Add-on User Seats.
     */
    public function updateAddonSeats(int|string $companyId, int $addonSeats): array
    {
        $company = Company::findOrFail($companyId);
        $company->update([
            'addon_user_seats' => $addonSeats,
        ]);

        return [
            'company_id'          => $company->id,
            'company_name'        => $company->name,
            'base_user_seats'     => $company->subscriptionPlan?->base_user_seats ?? 5,
            'addon_user_seats'    => $company->addon_user_seats,
            'total_allowed_seats' => $company->total_allowed_seats,
        ];
    }

    /**
     * Reset Tenant Data (Single company or Bulk All companies).
     */
    public function resetTenantData(User $currentUser, bool $clearAll, ?int $targetCompanyId): array
    {
        return DB::transaction(function () use ($currentUser, $clearAll, $targetCompanyId) {
            // Delete staff users except current user and Super Admin (role_id 1)
            $usersQuery = User::query();
            if (!$clearAll && $targetCompanyId) {
                $usersQuery->where('company_id', $targetCompanyId);
            }
            $usersQuery->where('id', '!=', $currentUser->id)
                ->where('role_id', '!=', 1)
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

            return [
                'clear_all'       => $clearAll,
                'company_id'      => $targetCompanyId,
                'preserved_admin' => $currentUser->email,
                'status'          => 'reset_completed',
            ];
        });
    }
}
