<?php

declare(strict_types=1);

namespace App\DTOs;

class OnboardTenantDTO
{
    public function __construct(
        public readonly string $companyName,
        public readonly ?string $subdomain,
        public readonly int $planId,
        public readonly string $billingCycle,
        public readonly int $addonUserSeats,
        public readonly string $databaseType,
        public readonly string $adminName,
        public readonly string $adminEmail,
        public readonly ?string $adminPhone,
        public readonly string $initialPassword
    ) {}

    public static function fromRequest(\Illuminate\Http\Request $request): self
    {
        return new self(
            companyName: (string) $request->input('company_name'),
            subdomain: $request->input('subdomain') ? (string) $request->input('subdomain') : null,
            planId: (int) $request->input('plan_id'),
            billingCycle: (string) $request->input('billing_cycle', 'monthly'),
            addonUserSeats: (int) $request->input('addon_user_seats', 0),
            databaseType: (string) $request->input('database_type', 'shared'),
            adminName: (string) $request->input('admin_name'),
            adminEmail: (string) $request->input('admin_email'),
            adminPhone: $request->input('admin_phone') ? (string) $request->input('admin_phone') : null,
            initialPassword: (string) $request->input('initial_password')
        );
    }
}
