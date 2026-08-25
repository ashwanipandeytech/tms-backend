<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BookingController;
use App\Http\Controllers\Api\V1\CabBookingController;
use App\Http\Controllers\Api\V1\CustomerController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\ExpenseController;
use App\Http\Controllers\Api\V1\FollowUpController;
use App\Http\Controllers\Api\V1\HotelController;
use App\Http\Controllers\Api\V1\InvoiceController;
use App\Http\Controllers\Api\V1\LeadController;
use App\Http\Controllers\Api\V1\LeadWebhookController;
use App\Http\Controllers\Api\V1\PackageController;
use App\Http\Controllers\Api\V1\PaymentController;
use App\Http\Controllers\Api\V1\QuotationController;
use App\Http\Controllers\Api\V1\ReportController;
use App\Http\Controllers\Api\V1\ResortController;
use App\Http\Controllers\Api\V1\RoleController;
use App\Http\Controllers\Api\V1\SubscriptionPlanController;
use App\Http\Controllers\Api\V1\TenantAdminController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\VehicleController;
use App\Http\Controllers\Api\V1\VendorController;
use App\Http\Controllers\Api\V1\VillaController;
use App\Http\Middleware\CheckDemoPlanLimit;
use App\Http\Middleware\CheckPlanModule;
use App\Http\Middleware\CheckSubscriptionActive;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Public routes, Plan List & Tenant Onboarding Registration
    Route::post('login', [AuthController::class, 'login']);
    Route::get('plans', [SubscriptionPlanController::class, 'index']);
    Route::post('admin/tenants', [TenantAdminController::class, 'store']); // Public & Super Admin Tenant Onboarding

    Route::prefix('webhooks')->group(function () {
        Route::post('leads/meta', [LeadWebhookController::class, 'handleMetaWebhook']);
        Route::post('leads/website', [LeadWebhookController::class, 'handleWebsiteWebhook']);
    });

    // Protected Routes (Sanctum Auth + Active Subscription Check + Demo Plan Limit Check)
    Route::middleware(['auth:sanctum', CheckSubscriptionActive::class, CheckDemoPlanLimit::class])->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
        Route::get('dashboard', [DashboardController::class, 'index']);

        // Super Admin Management Endpoints
        Route::apiResource('plans', SubscriptionPlanController::class)->except(['index']);
        Route::get('admin/tenants', [TenantAdminController::class, 'index']);
        Route::put('admin/tenants/{id}/addon-seats', [TenantAdminController::class, 'updateAddonSeats']);

        // Reports
        Route::prefix('reports')->group(function () {
            Route::get('leads-by-source', [ReportController::class, 'leadsBySource']);
            Route::get('sales-by-staff', [ReportController::class, 'salesByStaff']);
            Route::get('monthly-revenue', [ReportController::class, 'monthlyRevenue']);
        });

        // Core Lead & Booking Endpoints
        Route::put('leads/{lead}/assign', [LeadController::class, 'assign']);
        Route::post('leads/import', [LeadController::class, 'importCsv']);
        Route::put('bookings/{booking}/assign-operations', [BookingController::class, 'assignOperations']);

        Route::apiResource('leads', LeadController::class);
        Route::apiResource('follow-ups', FollowUpController::class);
        Route::apiResource('bookings', BookingController::class);
        Route::apiResource('quotations', QuotationController::class);
        Route::apiResource('payments', PaymentController::class);
        Route::apiResource('customers', CustomerController::class);
        Route::apiResource('users', UserController::class);
        Route::apiResource('roles', RoleController::class);

        // Feature Gated Modules: Tour Packages
        Route::middleware(CheckPlanModule::class . ':packages')->group(function () {
            Route::apiResource('packages', PackageController::class);
        });

        // Feature Gated Modules: Inventory & Cabs
        Route::middleware(CheckPlanModule::class . ':inventory')->group(function () {
            Route::apiResource('hotels', HotelController::class);
            Route::apiResource('resorts', ResortController::class);
            Route::apiResource('villas', VillaController::class);
            Route::apiResource('vendors', VendorController::class);
            Route::apiResource('vehicles', VehicleController::class);
            Route::apiResource('cab-bookings', CabBookingController::class);
        });

        // Feature Gated Modules: Finance
        Route::middleware(CheckPlanModule::class . ':finance')->group(function () {
            Route::apiResource('invoices', InvoiceController::class);
            Route::apiResource('expenses', ExpenseController::class);
        });
    });
});
