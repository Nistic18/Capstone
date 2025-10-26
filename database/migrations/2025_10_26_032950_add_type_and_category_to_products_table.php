<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('product_type_id')->nullable()->after('user_id');
            $table->unsignedBigInteger('product_category_id')->nullable()->after('product_type_id');

            // Optional: Add foreign keys (if you’ll have type/category tables)
            $table->foreign('product_type_id')->references('id')->on('product_types')->onDelete('set null');
            $table->foreign('product_category_id')->references('id')->on('product_categories')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['product_type_id']);
            $table->dropForeign(['product_category_id']);
            $table->dropColumn(['product_type_id', 'product_category_id']);
        });
    }
};
