<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds 'master' to the role enum column in the users table.
     */
    public function up(): void
    {
        // MySQL requires ALTER TABLE to modify ENUM values
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'guard', 'master') DEFAULT 'guard'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'guard') DEFAULT 'guard'");
    }
};
