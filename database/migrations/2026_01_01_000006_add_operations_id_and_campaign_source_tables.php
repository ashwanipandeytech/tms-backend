<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'operations_id')) {
                $table->foreignId('operations_id')->nullable()->after('package_id')->constrained('users')->nullOnDelete();
            }
        });

        Schema::table('leads', function (Blueprint $table) {
            if (!Schema::hasColumn('leads', 'campaign_source')) {
                $table->string('campaign_source', 100)->nullable()->after('source_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'operations_id')) {
                $table->dropForeign(['operations_id']);
                $table->dropColumn('operations_id');
            }
        });

        Schema::table('leads', function (Blueprint $table) {
            if (Schema::hasColumn('leads', 'campaign_source')) {
                $table->dropColumn('campaign_source');
            }
        });
    }
};
