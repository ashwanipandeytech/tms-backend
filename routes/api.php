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
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\VehicleController;
use App\Http\Controllers\Api\V1\VendorController;
use App\Http\Controllers\Api\V1\VillaController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Public routes & Webhooks
    Route::post('login', [AuthController::class, 'login']);

    Route::prefix('webhooks')->group(function () {
        Route::post('leads/meta', [LeadWebhookController::class, 'handleMetaWebhook']);
        Route::post('leads/website', [LeadWebhookController::class, 'handleWebsiteWebhook']);
    });

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
        Route::get('dashboard', [DashboardController::class, 'index']);

        // Reports
        Route::prefix('reports')->group(function () {
            Route::get('leads-by-source', [ReportController::class, 'leadsBySource']);
            Route::get('sales-by-staff', [ReportController::class, 'salesByStaff']);
            Route::get('monthly-revenue', [ReportController::class, 'monthlyRevenue']);
        });

        // Specialized Lead & Booking Assignment Endpoints
        Route::put('leads/{lead}/assign', [LeadController::class, 'assign']);
        Route::post('leads/import', [LeadController::class, 'importCsv']);
        Route::put('bookings/{booking}/assign-operations', [BookingController::class, 'assignOperations']);

        // Core CRM Resource REST API Endpoints
        Route::apiResource('leads', LeadController::class);
        Route::apiResource('follow-ups', FollowUpController::class);
        Route::apiResource('bookings', BookingController::class);
        Route::apiResource('quotations', QuotationController::class);
        Route::apiResource('payments', PaymentController::class);
        Route::apiResource('packages', PackageController::class);
        Route::apiResource('hotels', HotelController::class);
        Route::apiResource('resorts', ResortController::class);
        Route::apiResource('villas', VillaController::class);
        Route::apiResource('vendors', VendorController::class);
        Route::apiResource('vehicles', VehicleController::class);
        Route::apiResource('cab-bookings', CabBookingController::class);
        Route::apiResource('customers', CustomerController::class);
        Route::apiResource('invoices', InvoiceController::class);
        Route::apiResource('expenses', ExpenseController::class);
        Route::apiResource('users', UserController::class);
        Route::apiResource('roles', RoleController::class);
    });
});
