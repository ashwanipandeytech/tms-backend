<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade');
            $table->string('code', 40)->unique();
            $table->string('type', 20);
            $table->decimal('value', 10, 2);
            $table->date('expiry_date')->nullable();
            $table->integer('usage_limit')->nullable();
            $table->integer('used_count')->default(0);
            $table->string('status', 20)->default('active');
            $table->timestamps();
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade');
            $table->string('name', 100);
            $table->string('email', 150)->nullable();
            $table->string('phone', 20);
            $table->string('password')->nullable();
            $table->foreignId('lead_id')->nullable()->constrained('leads')->onDelete('set null');
            $table->string('status', 20)->default('active');
            $table->timestamps();

            $table->index('phone');
        });

        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade');
            $table->string('quotation_no', 30)->unique();
            $table->foreignId('lead_id')->nullable()->constrained('leads')->onDelete('set null');
            $table->string('customer_name', 120)->nullable();
            $table->foreignId('package_id')->nullable()->constrained('packages')->onDelete('set null');
            $table->foreignId('coupon_id')->nullable()->constrained('coupons')->onDelete('set null');
            $table->decimal('sub_total', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('gst_amount', 12, 2)->default(0);
            $table->decimal('final_amount', 12, 2)->default(0);
            $table->string('status', 30)->default('draft');
            $table->date('valid_till')->nullable();
            $table->string('pdf_path')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('quotation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained('quotations')->onDelete('cascade');
            $table->string('item_type', 30);
            $table->string('description', 255);
            $table->smallInteger('qty')->default(1);
            $table->decimal('amount', 12, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('itineraries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade');
            $table->foreignId('quotation_id')->nullable()->constrained('quotations')->onDelete('cascade');
            $table->foreignId('package_id')->nullable()->constrained('packages')->onDelete('set null');
            $table->string('title', 150)->nullable();
            $table->timestamps();
        });

        Schema::create('itinerary_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('itinerary_id')->constrained('itineraries')->onDelete('cascade');
            $table->smallInteger('day_number');
            $table->string('title', 150);
            $table->text('description')->nullable();
            $table->foreignId('hotel_id')->nullable()->constrained('hotels')->onDelete('set null');
            $table->string('meals', 50)->nullable();
            $table->timestamps();
        });

        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade');
            $table->string('booking_no', 30)->unique();
            $table->foreignId('lead_id')->nullable()->constrained('leads')->onDelete('set null');
            $table->foreignId('quotation_id')->nullable()->constrained('quotations')->onDelete('set null');
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('set null');
            $table->foreignId('package_id')->nullable()->constrained('packages')->onDelete('set null');
            $table->date('travel_date')->nullable();
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('due_amount', 12, 2)->default(0);
            $table->string('status', 30)->default('pending');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index('status');
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade');
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->string('payment_type', 30)->default('partial');
            $table->string('payment_mode', 40)->nullable();
            $table->string('txn_reference', 80)->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->timestamps();
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade');
            $table->string('invoice_no', 30)->unique();
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->onDelete('set null');
            $table->decimal('amount', 12, 2)->default(0);
            $table->decimal('gst_amount', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->string('status', 30)->default('unpaid');
            $table->date('issued_at')->nullable();
            $table->string('pdf_path')->nullable();
            $table->timestamps();
        });

        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade');
            $table->string('category', 60);
            $table->decimal('amount', 12, 2);
            $table->string('description', 255)->nullable();
            $table->date('expense_date')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('vendor_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade');
            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->date('payment_date')->nullable();
            $table->string('reference', 80)->nullable();
            $table->timestamps();
        });

        Schema::create('whatsapp_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade');
            $table->string('name', 80);
            $table->string('event_trigger', 60);
            $table->text('message_body');
            $table->string('status', 20)->default('active');
            $table->timestamps();
        });

        Schema::create('whatsapp_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade');
            $table->foreignId('lead_id')->nullable()->constrained('leads')->onDelete('set null');
            $table->string('phone', 20);
            $table->text('message')->nullable();
            $table->string('status', 30)->default('queued');
            $table->dateTime('sent_at')->nullable();
            $table->timestamps();
        });

        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade');
            $table->string('name', 80);
            $table->string('event_trigger', 60);
            $table->string('subject', 180);
            $table->text('body');
            $table->string('status', 20)->default('active');
            $table->timestamps();
        });

        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade');
            $table->foreignId('lead_id')->nullable()->constrained('leads')->onDelete('set null');
            $table->string('email', 150);
            $table->string('subject', 180)->nullable();
            $table->string('status', 30)->default('queued');
            $table->dateTime('sent_at')->nullable();
            $table->timestamps();
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('title', 150);
            $table->string('message', 500)->nullable();
            $table->string('url')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade');
            $table->string('setting_key', 80);
            $table->text('setting_value')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'setting_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('email_logs');
        Schema::dropIfExists('email_templates');
        Schema::dropIfExists('whatsapp_logs');
        Schema::dropIfExists('whatsapp_templates');
        Schema::dropIfExists('vendor_payments');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('itinerary_days');
        Schema::dropIfExists('itineraries');
        Schema::dropIfExists('quotation_items');
        Schema::dropIfExists('quotations');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('coupons');
    }
};
