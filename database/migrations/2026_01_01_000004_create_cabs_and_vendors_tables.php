<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade');
            $table->string('name', 120);
            $table->string('type', 50)->default('cab');
            $table->string('contact', 20)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('address', 255)->nullable();
            $table->timestamps();
        });

        Schema::create('vehicle_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade');
            $table->string('name', 50);
            $table->timestamps();
        });

        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade');
            $table->foreignId('vehicle_type_id')->constrained('vehicle_types')->onDelete('cascade');
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->onDelete('set null');
            $table->string('model', 80)->nullable();
            $table->string('number_plate', 20)->nullable();
            $table->string('status', 30)->default('available');
            $table->timestamps();
        });

        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade');
            $table->string('name', 100);
            $table->string('phone', 20)->nullable();
            $table->string('license_no', 40)->nullable();
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('cab_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade');
            $table->foreignId('vehicle_type_id')->constrained('vehicle_types')->onDelete('cascade');
            $table->decimal('rate_per_km', 8, 2)->default(0);
            $table->decimal('rate_per_day', 10, 2)->default(0);
            $table->decimal('base_fare', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('cab_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade');
            $table->foreignId('lead_id')->nullable()->constrained('leads')->onDelete('set null');
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->onDelete('set null');
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->onDelete('set null');
            $table->string('pickup_location', 150)->nullable();
            $table->string('drop_location', 150)->nullable();
            $table->dateTime('pickup_datetime')->nullable();
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('status', 30)->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cab_bookings');
        Schema::dropIfExists('cab_rates');
        Schema::dropIfExists('drivers');
        Schema::dropIfExists('vehicles');
        Schema::dropIfExists('vehicle_types');
        Schema::dropIfExists('vendors');
    }
};
