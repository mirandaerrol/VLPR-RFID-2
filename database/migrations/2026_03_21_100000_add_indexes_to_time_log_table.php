<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds performance indexes to the time_log table for frequently queried columns.
     */
    public function up(): void
    {
        Schema::table('time_log', function (Blueprint $table) {
            // Index for finding open sessions (time_out IS NULL)
            $table->index('time_out');
            // Index for ordering by time_in
            $table->index('time_in');
            // Composite index for the most common query pattern
            $table->index(['logs_id', 'time_out']);
            // Index for ordering by updated_at (used in latest_detection)
            $table->index('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('time_log', function (Blueprint $table) {
            $table->dropIndex(['time_out']);
            $table->dropIndex(['time_in']);
            $table->dropIndex(['logs_id', 'time_out']);
            $table->dropIndex(['updated_at']);
        });
    }
};
