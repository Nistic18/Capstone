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
        // Add low_stock_threshold to products table
        Schema::table('products', function (Blueprint $table) {
            $table->integer('low_stock_threshold')->default(10)->after('quantity');
        });

        // Create inventory_logs table
        Schema::create('inventory_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['in', 'out']);
            $table->integer('quantity');
            $table->integer('old_quantity');
            $table->integer('new_quantity');
            $table->string('reason')->nullable(); // restock, sale, adjustment, damaged, returned
            $table->text('notes')->nullable();
            $table->timestamps();

            // Add indexes for better query performance
            $table->index(['product_id', 'created_at']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop inventory_logs table first (foreign key constraint)
        Schema::dropIfExists('inventory_logs');
        
        // Remove low_stock_threshold from products table
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('low_stock_threshold');
        });
    }
};