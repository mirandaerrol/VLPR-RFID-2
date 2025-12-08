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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id('vehicle_id');
            $table->unsignedBigInteger('owner_id'); 
            // REMOVED: rfid_id (RFID is now on the owner)
            $table->string('plate_number');
            $table->timestamps();

            $table->foreign('owner_id')->references('owner_id')->on('vehicle_owner')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};