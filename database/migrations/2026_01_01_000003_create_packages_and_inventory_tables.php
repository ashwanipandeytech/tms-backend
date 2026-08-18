<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('package_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade');
            $table->string('name', 50);
            $table->string('type', 30);
            $table->timestamps();
        });

        Schema::create('destinations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained('package_categories')->onDelete('set null');
            $table->string('name', 100);
            $table->timestamps();
        });

        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade');
            $table->string('name', 150);
            $table->foreignId('destination_id')->nullable()->constrained('destinations')->onDelete('set null');
            $table->foreignId('category_id')->nullable()->constrained('package_categories')->onDelete('set null');
            $table->smallInteger('nights')->default(0);
            $table->smallInteger('days')->default(0);
            $table->decimal('price', 12, 2)->default(0);
            $table->boolean('gst_applicable')->default(false);
            $table->decimal('gst_percent', 5, 2)->default(5.00);
            $table->text('inclusions')->nullable();
            $table->text('exclusions')->nullable();
            $table->text('terms')->nullable();
            $table->string('brochure_pdf')->nullable();
            $table->string('status', 30)->default('active');
            $table->timestamps();
        });

        Schema::create('package_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained('packages')->onDelete('cascade');
            $table->string('image_path');
            $table->timestamps();
        });

        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade');
            $table->string('name', 150);
            $table->string('location', 150)->nullable();
            $table->tinyInteger('star_category')->nullable();
            $table->string('contact_name', 100)->nullable();
            $table->string('contact_phone', 20)->nullable();
            $table->string('contact_email', 150)->nullable();
            $table->decimal('rating', 2, 1)->nullable();
            $table->string('status', 30)->default('active');
            $table->timestamps();
        });

        Schema::create('hotel_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->onDelete('cascade');
            $table->string('room_type', 80);
            $table->string('meal_plan', 20)->default('EP');
            $table->decimal('price', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('hotel_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->onDelete('cascade');
            $table->string('image_path');
            $table->timestamps();
        });

        Schema::create('resorts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade');
            $table->string('name', 150);
            $table->string('location', 150)->nullable();
            $table->text('facilities')->nullable();
            $table->string('status', 30)->default('active');
            $table->timestamps();
        });

        Schema::create('resort_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resort_id')->constrained('resorts')->onDelete('cascade');
            $table->string('room_type', 80);
            $table->string('season', 30)->default('regular');
            $table->decimal('price', 10, 2)->default(0);
            $table->smallInteger('inventory')->default(0);
            $table->timestamps();
        });

        Schema::create('resort_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resort_id')->constrained('resorts')->onDelete('cascade');
            $table->string('image_path');
            $table->timestamps();
        });

        Schema::create('villas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade');
            $table->string('name', 150);
            $table->string('location', 150)->nullable();
            $table->smallInteger('capacity')->default(0);
            $table->smallInteger('bedrooms')->default(0);
            $table->decimal('price', 10, 2)->default(0);
            $table->text('amenities')->nullable();
            $table->string('status', 30)->default('active');
            $table->timestamps();
        });

        Schema::create('villa_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('villa_id')->constrained('villas')->onDelete('cascade');
            $table->string('image_path');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('villa_images');
        Schema::dropIfExists('villas');
        Schema::dropIfExists('resort_images');
        Schema::dropIfExists('resort_rooms');
        Schema::dropIfExists('resorts');
        Schema::dropIfExists('hotel_images');
        Schema::dropIfExists('hotel_rooms');
        Schema::dropIfExists('hotels');
        Schema::dropIfExists('package_images');
        Schema::dropIfExists('packages');
        Schema::dropIfExists('destinations');
        Schema::dropIfExists('package_categories');
    }
};
