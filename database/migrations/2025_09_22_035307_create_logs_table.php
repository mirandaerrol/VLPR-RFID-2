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
        Schema::create('logs', function (Blueprint $table) {
            $table->id('logs_id'); 
            $table->unsignedBigInteger('vehicle_id')->nullable();
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->string('rfid_code')->nullable(); 
            $table->string('detection_method')->nullable();
            $table->string('detected_plate_number')->nullable();
            $table->string('vehicle_type')->nullable();

            $table->timestamps();

            $table->foreign('vehicle_id')->references('vehicle_id')->on('vehicles')->onDelete('cascade');
            $table->foreign('owner_id')->references('owner_id')->on('vehicle_owner')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs');
    }
};