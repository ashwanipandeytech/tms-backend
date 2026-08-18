<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Subscribers / Tenant Companies Table
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('domain')->nullable()->unique();
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->string('subscription_plan')->default('starter');
            $table->timestamp('subscription_expires_at')->nullable();
            $table->timestamps();
        });

        // Roles Table
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade');
            $table->string('name', 50);
            $table->string('description', 255)->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'name']);
        });

        // Permissions Table
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('module', 50);
            $table->string('action', 50);
            $table->string('description', 255)->nullable();
            $table->timestamps();

            $table->unique(['module', 'action']);
        });

        // Role Permissions Table
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->foreignId('permission_id')->constrained('permissions')->onDelete('cascade');
            $table->primary(['role_id', 'permission_id']);
        });

        // Users Table
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade');
            $table->foreignId('role_id')->nullable()->constrained('roles')->onDelete('set null');
            $table->string('name', 100);
            $table->string('email', 150)->unique();
            $table->string('phone', 20)->nullable();
            $table->string('password');
            $table->string('avatar')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->dateTime('last_login')->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->index('role_id');
            $table->index('company_id');
        });

        // Password Reset Tokens
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // Sessions Table
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        // Sanctum Personal Access Tokens Table
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personal_access_tokens');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('companies');
    }
};
