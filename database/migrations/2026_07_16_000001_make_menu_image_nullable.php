<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Make the `image` column nullable on menu_items.
     * Previously it was TEXT NOT NULL which caused SQL errors
     * when creating a menu item without uploading an image.
     */
    public function up(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->text('image')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            // Revert to NOT NULL (set empty string as default to avoid data loss)
            $table->text('image')->nullable(false)->change();
        });
    }
};
