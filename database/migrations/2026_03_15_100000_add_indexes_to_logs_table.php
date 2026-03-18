<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('logs', function (Blueprint $table) {
            // Add indexes for frequently queried columns
            $table->index('detected_plate_number');
            $table->index('rfid_code');
            $table->index('created_at');
            $table->index(['owner_id', 'created_at']);
            $table->index(['vehicle_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('logs', function (Blueprint $table) {
            $table->dropIndex(['detected_plate_number']);
            $table->dropIndex(['rfid_code']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['owner_id', 'created_at']);
            $table->dropIndex(['vehicle_id', 'created_at']);
        });
    }
};
