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
        // ── orders table additions ───────────────────────────────────────────
        Schema::table('orders', function (Blueprint $table) {
            $table->string('invoice_number', 30)->nullable()->unique()->after('id');
            $table->string('customer_name', 100)->nullable()->after('invoice_number');
        });

        // Extend the status enum to include 'confirmed'
        // We use a raw ALTER for maximum compatibility with MySQL
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM(
            'pending','confirmed','preparing','ready','completed','cancelled'
        ) NOT NULL DEFAULT 'pending'");

        // ── order_items table additions ───────────────────────────────────────
        Schema::table('order_items', function (Blueprint $table) {
            $table->text('notes')->nullable()->after('quantity');
        });

        // ── payments table additions ──────────────────────────────────────────
        Schema::table('payments', function (Blueprint $table) {
            $table->enum('status', ['pending', 'paid'])->default('pending')->after('amount');
            $table->decimal('change_amount', 12, 2)->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['invoice_number', 'customer_name']);
        });

        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM(
            'pending','preparing','ready','completed','cancelled'
        ) NOT NULL DEFAULT 'pending'");

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('notes');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['status', 'change_amount']);
        });
    }
};
