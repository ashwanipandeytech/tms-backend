<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_sources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade');
            $table->string('name', 50);
            $table->timestamps();
        });

        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade');
            $table->string('name', 100);
            $table->string('email', 150)->nullable();
            $table->string('phone', 20);
            $table->foreignId('source_id')->nullable()->constrained('lead_sources')->onDelete('set null');
            $table->string('destination', 100)->nullable();
            $table->date('travel_date')->nullable();
            $table->smallInteger('pax_adults')->default(0);
            $table->smallInteger('pax_children')->default(0);
            $table->decimal('budget', 12, 2)->nullable();
            $table->string('status', 30)->default('new');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index('status');
            $table->index('assigned_to');
            $table->index('source_id');
            $table->index('company_id');
        });

        Schema::create('lead_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade');
            $table->foreignId('lead_id')->constrained('leads')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('activity_type', 50);
            $table->string('description', 500);
            $table->timestamps();

            $table->index('lead_id');
        });

        Schema::create('follow_ups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade');
            $table->foreignId('lead_id')->constrained('leads')->onDelete('cascade');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->date('follow_up_date');
            $table->time('follow_up_time')->nullable();
            $table->string('type', 30)->default('call');
            $table->string('remarks', 500)->nullable();
            $table->boolean('remind_whatsapp')->default(false);
            $table->boolean('remind_email')->default(false);
            $table->string('status', 30)->default('pending');
            $table->timestamps();

            $table->index('follow_up_date');
            $table->index('lead_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('follow_ups');
        Schema::dropIfExists('lead_activities');
        Schema::dropIfExists('leads');
        Schema::dropIfExists('lead_sources');
    }
};
