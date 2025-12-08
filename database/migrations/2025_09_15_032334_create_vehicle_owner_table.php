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
        Schema::create('vehicle_owner', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id('owner_id');
            $table->string('f_name');
            $table->string('l_name');
            $table->string('address');
            $table->string('contact_number');
            $table->string('school_year')->nullable();
            $table->enum('type_of_owner', ['student', 'employee']);
            $table->string('valid_id');
            // ADDED: RFID Code directly attached to the owner
            $table->string('rfid_code')->nullable()->unique(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_owner');
    }
};