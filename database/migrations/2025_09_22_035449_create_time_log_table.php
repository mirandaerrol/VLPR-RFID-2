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
        Schema::create('time_log', function (Blueprint $table) {
            $table->id('time_log_id');
            $table->unsignedBigInteger('logs_id');
            $table->timestamp('time_in')->nullable(false);
            $table->timestamp('time_out')->nullable();
            $table->timestamps();

            $table->foreign('logs_id')->references('logs_id')->on('logs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_log');
    }
};