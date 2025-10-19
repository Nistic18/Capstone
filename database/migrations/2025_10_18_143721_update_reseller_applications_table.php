<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reseller_applications', function (Blueprint $table) {
            // Add user_id if it doesn't exist
            if (!Schema::hasColumn('reseller_applications', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            }
            
            // Add city if it doesn't exist
            if (!Schema::hasColumn('reseller_applications', 'city')) {
                $table->string('city')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('reseller_applications', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'city']);
        });
    }
};