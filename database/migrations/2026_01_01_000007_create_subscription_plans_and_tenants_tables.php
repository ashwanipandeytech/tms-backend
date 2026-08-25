<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->decimal('monthly_price', 10, 2)->default(0);
            $table->decimal('yearly_price', 10, 2)->default(0);
            $table->integer('base_user_seats')->default(5);
            $table->decimal('addon_seat_price', 10, 2)->default(5.00);
            $table->json('modules'); // e.g. ["leads", "followups", "packages", "inventory", "bookings", "finance"]
            $table->string('database_type', 20)->default('shared'); // 'shared' or 'dedicated'
            $table->string('status', 20)->default('active');
            $table->timestamps();
        });

        Schema::table('companies', function (Blueprint $table) {
            if (!Schema::hasColumn('companies', 'subdomain')) {
                $table->string('subdomain', 100)->nullable()->unique()->after('name');
            }
            if (!Schema::hasColumn('companies', 'plan_id')) {
                $table->foreignId('plan_id')->nullable()->after('subdomain')->constrained('subscription_plans')->nullOnDelete();
            }
            if (!Schema::hasColumn('companies', 'addon_user_seats')) {
                $table->integer('addon_user_seats')->default(0)->after('plan_id');
            }
            if (!Schema::hasColumn('companies', 'subscription_status')) {
                $table->string('subscription_status', 30)->default('active')->after('addon_user_seats');
            }
            if (!Schema::hasColumn('companies', 'billing_cycle')) {
                $table->string('billing_cycle', 20)->default('monthly')->after('subscription_status');
            }
            if (!Schema::hasColumn('companies', 'subscription_starts_at')) {
                $table->timestamp('subscription_starts_at')->nullable()->after('billing_cycle');
            }
            if (!Schema::hasColumn('companies', 'subscription_ends_at')) {
                $table->timestamp('subscription_ends_at')->nullable()->after('subscription_starts_at');
            }
            if (!Schema::hasColumn('companies', 'database_type')) {
                $table->string('database_type', 20)->default('shared')->after('subscription_ends_at');
            }
            if (!Schema::hasColumn('companies', 'database_name')) {
                $table->string('database_name', 100)->nullable()->after('database_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (Schema::hasColumn('companies', 'database_name')) {
                $table->dropColumn(['database_name', 'database_type', 'subscription_ends_at', 'subscription_starts_at', 'billing_cycle', 'subscription_status', 'addon_user_seats']);
                $table->dropForeign(['plan_id']);
                $table->dropColumn(['plan_id', 'subdomain']);
            }
        });

        Schema::dropIfExists('subscription_plans');
    }
};
