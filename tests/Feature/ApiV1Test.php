<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Lead;
use App\Models\Payment;
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

    public function test_login_validation_and_successful_authentication(): void
    {
        // 1. Invalid credentials
        $response = $this->postJson('/api/v1/login', [
            'email'    => 'admin@safarmusafir.com',
            'password' => 'WrongPassword',
        ]);
        $response->assertStatus(422)->assertJsonStructure(['message', 'errors']);

        // 2. Successful login
        $response = $this->postJson('/api/v1/login', [
            'email'    => 'admin@safarmusafir.com',
            'password' => 'Admin@123',
        ]);
        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJsonStructure(['data' => ['user', 'token']]);
    }

    public function test_unauthenticated_requests_are_rejected(): void
    {
        $response = $this->getJson('/api/v1/dashboard');
        $response->assertStatus(401);
    }

    public function test_lead_crud_operations_and_event_activity_logging(): void
    {
        $user = User::where('email', 'admin@safarmusafir.com')->first();
        $this->actingAs($user, 'sanctum');

        // Create Lead
        $createResponse = $this->postJson('/api/v1/leads', [
            'name'        => 'Test Lead John',
            'email'       => 'john.test@example.com',
            'phone'       => '9988776655',
            'destination' => 'Goa',
            'budget'      => 30000.00,
        ]);
        $createResponse->assertStatus(201)
                       ->assertJson(['success' => true])
                       ->assertJsonPath('data.name', 'Test Lead John');

        $leadId = $createResponse->json('data.id');

        // Verify Lead Activity Logged via LeadCreatedEvent -> LogLeadActivityListener
        $this->assertDatabaseHas('lead_activities', [
            'lead_id'       => $leadId,
            'activity_type' => 'enquiry',
        ]);

        // Get Lead Details
        $showResponse = $this->getJson("/api/v1/leads/{$leadId}");
        $showResponse->assertStatus(200)->assertJsonPath('data.destination', 'Goa');

        // Update Lead
        $updateResponse = $this->putJson("/api/v1/leads/{$leadId}", [
            'status' => 'interested',
            'budget' => 35000.00,
        ]);
        $updateResponse->assertStatus(200)->assertJsonPath('data.status', 'interested');

        // Delete Lead
        $deleteResponse = $this->deleteJson("/api/v1/leads/{$leadId}");
        $deleteResponse->assertStatus(200);

        $this->assertDatabaseMissing('leads', ['id' => $leadId]);
    }

    public function test_booking_and_payment_synchronization(): void
    {
        $user = User::where('email', 'admin@safarmusafir.com')->first();
        $this->actingAs($user, 'sanctum');

        // Create Booking
        $bookingResponse = $this->postJson('/api/v1/bookings', [
            'total_amount' => 50000.00,
            'paid_amount'  => 0.00,
            'status'       => 'pending',
        ]);
        $bookingResponse->assertStatus(201)->assertJsonPath('data.due_amount', 50000);

        $bookingId = $bookingResponse->json('data.id');

        // Record Payment
        $paymentResponse = $this->postJson('/api/v1/payments', [
            'booking_id'   => $bookingId,
            'amount'       => 20000.00,
            'payment_type' => 'advance',
            'payment_mode' => 'upi',
        ]);
        $paymentResponse->assertStatus(201);

        // Verify Booking due amount recalculated automatically
        $this->assertDatabaseHas('bookings', [
            'id'          => $bookingId,
            'paid_amount' => 20000.00,
            'due_amount'  => 30000.00,
        ]);
    }

    public function test_dashboard_and_reports_endpoints(): void
    {
        $user = User::where('email', 'admin@safarmusafir.com')->first();
        $this->actingAs($user, 'sanctum');

        $this->getJson('/api/v1/dashboard')
             ->assertStatus(200)
             ->assertJson(['success' => true])
             ->assertJsonStructure(['data' => ['kpis', 'funnel', 'upcoming_departures']]);

        $this->getJson('/api/v1/reports/leads-by-source')->assertStatus(200);
        $this->getJson('/api/v1/reports/sales-by-staff')->assertStatus(200);
        $this->getJson('/api/v1/reports/monthly-revenue')->assertStatus(200);
    }
}
