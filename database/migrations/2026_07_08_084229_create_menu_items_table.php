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
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('image');
            $table->string('image_alt')->nullable();
            $table->decimal('rating', 2, 1)->default(0);
            $table->string('time', 10)->nullable();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('category', 50);
            $table->boolean('popular')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
