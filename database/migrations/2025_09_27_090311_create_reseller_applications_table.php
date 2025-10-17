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
        Schema::create('reseller_applications', function (Blueprint $table) {
            $table->id();
            $table->string('email_address');
            $table->string('business_name');
            $table->string('address');
            $table->string('country');
            $table->string('province');
            $table->string('zip_code');
            $table->string('business_license_id');
            $table->string('phone_number');
            $table->string('pdf_file'); // path or filename of uploaded PDF
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reseller_applications');
    }
};
