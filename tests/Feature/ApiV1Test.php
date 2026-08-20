<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Lead;
use App\Models\Payment;
use App\Models\Role;
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

    public function test_user_crud_operations(): void
    {
        $admin = User::where('email', 'travel@demohandler.in')->first();
        $this->actingAs($admin, 'sanctum');

        // 1. Add User
        $createResponse = $this->postJson('/api/v1/users', [
            'name'                  => 'Jane Doe',
            'email'                 => 'jane@demohandler.in',
            'phone'                 => '9876543210',
            'password'              => 'Password@123',
            'password_confirmation' => 'Password@123',
            'role_id'               => 3,
            'status'                => 'active',
        ]);
        $createResponse->assertStatus(201)
                       ->assertJsonPath('data.email', 'jane@demohandler.in');

        $userId = $createResponse->json('data.id');

        // 2. Edit User (without password)
        $updateResponse = $this->putJson("/api/v1/users/{$userId}", [
            'name'   => 'Jane Doe Updated',
            'email'  => 'jane@demohandler.in',
            'phone'  => '9876543211',
            'role_id'=> 3,
            'status' => 'inactive',
        ]);
        $updateResponse->assertStatus(200)
                       ->assertJsonPath('data.name', 'Jane Doe Updated')
                       ->assertJsonPath('data.status', 'inactive');

        // 3. Delete User
        $deleteResponse = $this->deleteJson("/api/v1/users/{$userId}");
        $deleteResponse->assertStatus(200);
    }
}
