<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Company;
use App\Models\Lead;
use App\Models\Payment;
use App\Models\Role;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiV1Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_login_validation_and_role_verification(): void
    {
        // Invalid password
        $response = $this->postJson('/api/v1/login', [
            'email'    => 'travel@demohandler.in',
            'password' => 'WrongPassword',
        ]);
        $response->assertStatus(422)->assertJsonStructure(['message', 'errors']);

        // Login with Role Type
        $response = $this->postJson('/api/v1/login', [
            'email'     => 'travel@demohandler.in',
            'password'  => 'Admin@123',
            'role_type' => 'Super Admin',
        ]);
        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJsonStructure(['data' => ['user', 'token']]);
    }

    public function test_system_status_config_endpoint(): void
    {
        $response = $this->getJson('/api/v1/config/statuses');
        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJsonStructure([
                     'data' => ['leads', 'bookings', 'invoices', 'followups', 'vehicles', 'quotations', 'customers']
                 ])
                 ->assertJsonFragment(['key' => 'NEW_LEAD', 'label' => 'New Lead']);
    }

    public function test_super_admin_subscription_plans_and_tenant_onboarding(): void
    {
        $admin = User::where('email', 'travel@demohandler.in')->first();
        $this->actingAs($admin, 'sanctum');

        // 1. Get Subscription Plans
        $plansResponse = $this->getJson('/api/v1/plans');
        $plansResponse->assertStatus(200)->assertJson(['success' => true]);

        // 2. Onboard New Company Subscriber with Add-on Seats
        $starterPlan = SubscriptionPlan::where('slug', 'starter-plan')->first();

        $onboardResponse = $this->postJson('/api/v1/admin/tenants', [
            'company_name'     => 'Sunrise Travel Agency',
            'subdomain'        => 'sunrisetravel',
            'plan_id'          => $starterPlan->id,
            'billing_cycle'    => 'monthly',
            'addon_user_seats' => 3,
            'admin_name'       => 'Rajesh Kumar',
            'admin_email'      => 'rajesh@sunrisetravel.com',
            'initial_password' => 'Password@123',
        ]);

        $onboardResponse->assertStatus(201)
                        ->assertJsonPath('data.total_allowed_seats', 8) // 5 Base + 3 Add-on
                        ->assertJsonPath('data.tenant_admin.email', 'rajesh@sunrisetravel.com');

        $companyId = $onboardResponse->json('data.company.id');

        // 3. Purchase Additional Add-on Seats
        $addonResponse = $this->putJson("/api/v1/admin/tenants/{$companyId}/addon-seats", [
            'addon_user_seats' => 5,
        ]);

        $addonResponse->assertStatus(200)
                      ->assertJsonPath('data.addon_user_seats', 5)
                      ->assertJsonPath('data.total_allowed_seats', 10); // 5 Base + 5 Add-on
    }

    public function test_user_seat_limit_enforcement(): void
    {
        $admin = User::where('email', 'travel@demohandler.in')->first();

        // Create Tenant Company with 1 Base Seat + 0 Add-on Seats
        $customPlan = SubscriptionPlan::create([
            'name'             => 'Single User Plan',
            'slug'             => 'single-user-plan',
            'monthly_price'    => 10.00,
            'yearly_price'     => 100.00,
            'base_user_seats'  => 1,
            'addon_seat_price' => 5.00,
            'modules'          => ['leads', 'bookings'],
            'database_type'    => 'shared',
        ]);

        $company = Company::create([
            'name'                => 'Limited Seat Agency',
            'subdomain'           => 'limitedagency',
            'plan_id'             => $customPlan->id,
            'addon_user_seats'    => 0,
            'subscription_status' => 'active',
        ]);

        $salesRole = Role::where('name', 'Sales Executive')->first();

        $tenantUser = User::create([
            'company_id' => $company->id,
            'name'       => 'Tenant Admin User',
            'email'      => 'tenant.admin@limited.com',
            'phone'      => '9111122222',
            'role_id'    => $salesRole->id,
            'password'   => bcrypt('Password@123'),
            'status'     => 'active',
        ]);

        // Tenant Admin attempts to create a 2nd user (Exceeds capacity of 1)
        $this->actingAs($tenantUser, 'sanctum');

        $exceedResponse = $this->postJson('/api/v1/users', [
            'name'                  => 'Extra Staff User',
            'email'                 => 'extra.staff@limited.com',
            'phone'                 => '9222233333',
            'role_id'               => $salesRole->id,
            'password'              => 'Password@123',
            'password_confirmation' => 'Password@123',
            'status'                => 'active',
        ]);

        $exceedResponse->assertStatus(422)
                       ->assertJsonPath('error_code', 'USER_SEAT_LIMIT_REACHED');
    }

    public function test_plan_feature_gating_middleware(): void
    {
        $starterPlan = SubscriptionPlan::where('slug', 'starter-plan')->first();

        $company = Company::create([
            'name'                => 'Starter Agency',
            'subdomain'           => 'starteragency',
            'plan_id'             => $starterPlan->id,
            'subscription_status' => 'active',
        ]);

        $role = Role::where('name', 'Manager')->first();

        $starterUser = User::create([
            'company_id' => $company->id,
            'name'       => 'Starter User',
            'email'      => 'starter.user@agency.com',
            'phone'      => '9333344444',
            'role_id'    => $role->id,
            'password'   => bcrypt('Password@123'),
            'status'     => 'active',
        ]);

        $this->actingAs($starterUser, 'sanctum');

        // Starter Plan excludes Finance module -> Expect 403 PLAN_FEATURE_RESTRICTED
        $financeResponse = $this->getJson('/api/v1/invoices');
        $financeResponse->assertStatus(403)
                        ->assertJsonPath('error_code', 'PLAN_FEATURE_RESTRICTED');
    }

    public function test_lead_assignment_and_csv_import(): void
    {
        $admin = User::where('email', 'travel@demohandler.in')->first();
        $salesRole = Role::where('name', 'Sales Executive')->first();

        $salesUser = User::create([
            'name'     => 'Sales Executive User',
            'email'    => 'sales.new@travel.com',
            'phone'    => '9888877777',
            'role_id'  => $salesRole->id,
            'password' => bcrypt('SalesPassword123!'),
            'status'   => 'active',
        ]);

        $this->actingAs($admin, 'sanctum');

        // Create Lead
        $leadResponse = $this->postJson('/api/v1/leads', [
            'name'        => 'Lead for Assignment',
            'email'       => 'assign.lead@example.com',
            'phone'       => '9988776655',
            'destination' => 'Goa',
            'budget'      => 40000.00,
        ]);
        $leadId = $leadResponse->json('data.id');

        // Admin assigns lead to Sales User
        $assignResponse = $this->putJson("/api/v1/leads/{$leadId}/assign", [
            'assigned_to' => $salesUser->id,
        ]);
        $assignResponse->assertStatus(200)->assertJsonPath('data.assigned_to', $salesUser->id);

        // Verify Sales Executive can access assigned lead
        $this->actingAs($salesUser, 'sanctum');
        $salesIndex = $this->getJson('/api/v1/leads');
        $salesIndex->assertStatus(200)->assertJsonFragment(['id' => $leadId]);
    }

    public function test_operations_handoff(): void
    {
        $admin = User::where('email', 'travel@demohandler.in')->first();
        $opsRole = Role::where('name', 'Operation Team')->first();

        $opsUser = User::create([
            'name'     => 'Ops User',
            'email'    => 'ops.new@travel.com',
            'phone'    => '9777766666',
            'role_id'  => $opsRole->id,
            'password' => bcrypt('OpsPassword123!'),
            'status'   => 'active',
        ]);

        $this->actingAs($admin, 'sanctum');

        $bookingResponse = $this->postJson('/api/v1/bookings', [
            'total_amount' => 60000.00,
            'status'       => 'confirmed',
        ]);
        $bookingId = $bookingResponse->json('data.id');

        // Assign Operations
        $assignOpsResponse = $this->putJson("/api/v1/bookings/{$bookingId}/assign-operations", [
            'operations_id' => $opsUser->id,
        ]);
        $assignOpsResponse->assertStatus(200)->assertJsonPath('data.operations_id', $opsUser->id);

        // Verify Ops User sees assigned booking
        $this->actingAs($opsUser, 'sanctum');
        $opsIndex = $this->getJson('/api/v1/bookings');
        $opsIndex->assertStatus(200)->assertJsonFragment(['id' => $bookingId]);
    }

    public function test_lead_webhook_ingestion(): void
    {
        // Meta Webhook
        $metaResponse = $this->postJson('/api/v1/webhooks/leads/meta', [
            'name'            => 'Meta Lead User',
            'phone'           => '9123456789',
            'email'           => 'meta.lead@example.com',
            'destination'     => 'Kerala',
            'campaign_source' => 'Meta Facebook Ads',
        ]);
        $metaResponse->assertStatus(201)->assertJsonPath('data.name', 'Meta Lead User');

        // Website Webhook
        $webResponse = $this->postJson('/api/v1/webhooks/leads/website', [
            'name'            => 'Website Form Lead',
            'phone'           => '9876543211',
            'email'           => 'web.lead@example.com',
            'destination'     => 'Manali',
            'campaign_source' => 'Website Popup',
        ]);
        $webResponse->assertStatus(201)->assertJsonPath('data.name', 'Website Form Lead');
    }

    public function test_get_customers_and_users_list(): void
    {
        $admin = User::where('email', 'travel@demohandler.in')->first();
        $this->actingAs($admin, 'sanctum');

        // Test GET /api/v1/customers
        $custResponse = $this->getJson('/api/v1/customers');
        $custResponse->assertStatus(200)
                    ->assertJson(['success' => true])
                    ->assertJsonStructure(['data', 'meta']);

        // Test GET /api/v1/users
        $userResponse = $this->getJson('/api/v1/users');
        $userResponse->assertStatus(200)
                    ->assertJson(['success' => true])
                    ->assertJsonStructure(['data', 'meta']);
    }

    public function test_free_trial_demo_plan_limit_restriction(): void
    {
        $demoPlan = SubscriptionPlan::where('slug', 'free-trial-plan')->first();

        $demoCompany = Company::create([
            'name'                => 'Demo Trial Travel Agency',
            'subdomain'           => 'demotrialagency',
            'plan_id'             => $demoPlan->id,
            'subscription_status' => 'active',
        ]);

        $role = Role::where('name', 'Manager')->first();

        $demoUser = User::create([
            'company_id' => $demoCompany->id,
            'name'       => 'Demo User',
            'email'      => 'demo.user@agency.com',
            'phone'      => '9444455555',
            'role_id'    => $role->id,
            'password'   => bcrypt('Password@123'),
            'status'     => 'active',
        ]);

        $this->actingAs($demoUser, 'sanctum');

        // 1st entry creation -> Allowed
        $firstLeadResponse = $this->postJson('/api/v1/leads', [
            'name'        => 'First Demo Lead',
            'phone'       => '9911122233',
            'email'       => 'firstdemo@example.com',
            'destination' => 'Kashmir',
        ]);
        $firstLeadResponse->assertStatus(201);

        // 2nd entry creation -> Blocked with 422 DEMO_PLAN_LIMIT_REACHED
        $secondLeadResponse = $this->postJson('/api/v1/leads', [
            'name'        => 'Second Demo Lead',
            'phone'       => '9911122244',
            'email'       => 'seconddemo@example.com',
            'destination' => 'Himachal',
        ]);
        $secondLeadResponse->assertStatus(422)
                          ->assertJsonPath('error_code', 'DEMO_PLAN_LIMIT_REACHED');
    }

    public function test_role_and_permission_management_for_tenants(): void
    {
        $admin = User::where('email', 'travel@demohandler.in')->first();
        $this->actingAs($admin, 'sanctum');

        // 1. Get List of Permissions
        $permResponse = $this->getJson('/api/v1/permissions');
        $permResponse->assertStatus(200)->assertJson(['success' => true]);

        $permissionIds = \App\Models\Permission::pluck('id')->take(3)->toArray();

        // 2. Create Custom Role with Permissions
        $createRoleResponse = $this->postJson('/api/v1/roles', [
            'name'        => 'Senior Sales Specialist',
            'description' => 'Custom role for senior sales reps',
            'permissions' => $permissionIds,
        ]);
        $createRoleResponse->assertStatus(201)
                           ->assertJsonPath('data.name', 'Senior Sales Specialist');

        $roleId = $createRoleResponse->json('data.id');

        // 3. Assign Role to User
        $createUserResponse = $this->postJson('/api/v1/users', [
            'name'                  => 'Senior Rep User',
            'email'                 => 'senior.sales@agency.com',
            'phone'                 => '9555566666',
            'role_id'               => $roleId,
            'password'              => 'Password@123',
            'password_confirmation' => 'Password@123',
            'status'                => 'active',
        ]);
        $createUserResponse->assertStatus(201)
                           ->assertJsonPath('data.role.id', $roleId);

        // 4. Duplicate Role Name & Plural Variant Rejection Test (e.g. "Managers" when "Manager" exists)
        $duplicateResponse = $this->postJson('/api/v1/roles', [
            'name' => 'Managers',
        ]);
        $duplicateResponse->assertStatus(422)
                         ->assertJsonPath('error_code', 'DUPLICATE_ROLE_NAME');
    }

    public function test_super_admin_companies_list_and_filters(): void
    {
        $admin = User::where('email', 'travel@demohandler.in')->first();
        $this->actingAs($admin, 'sanctum');

        $response = $this->getJson('/api/v1/admin/companies');
        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJsonStructure([
                     'data' => [
                         '*' => [
                             'id',
                             'company_name',
                             'status',
                             'subscription' => ['plan_name', 'status', 'days_remaining'],
                             'total_employees',
                             'employees',
                         ]
                     ]
                 ]);
    }

    public function test_super_admin_role_and_user_hiding_and_tenant_reset(): void
    {
        $admin = User::where('email', 'travel@demohandler.in')->first();
        $this->actingAs($admin, 'sanctum');

        // Test Roles list hides Super Admin role
        $rolesResponse = $this->getJson('/api/v1/roles');
        $rolesResponse->assertStatus(200);
        $roleNames = collect($rolesResponse->json('data'))->pluck('name')->toArray();
        $this->assertNotContains('Super Admin', $roleNames);

        // Test Tenant Reset API clears test data
        $resetResponse = $this->deleteJson('/api/v1/admin/reset');
        $resetResponse->assertStatus(200)
                      ->assertJsonPath('data.status', 'reset_completed');

        // Verify Super Admin account still exists
        $this->assertDatabaseHas('users', ['email' => 'travel@demohandler.in']);
    }
}
