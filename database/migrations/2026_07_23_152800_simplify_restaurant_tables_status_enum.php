<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First convert any 'occupied' tables to 'available' to prevent data truncation
        DB::table('restaurant_tables')
            ->where('status', 'occupied')
            ->update(['status' => 'available']);

        // Alter the ENUM column
        DB::statement("ALTER TABLE restaurant_tables MODIFY COLUMN status ENUM('available', 'disabled') NOT NULL DEFAULT 'available'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE restaurant_tables MODIFY COLUMN status ENUM('available', 'occupied', 'disabled') NOT NULL DEFAULT 'available'");
    }
};
