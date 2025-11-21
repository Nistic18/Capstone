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
        Schema::table('reseller_applications', function (Blueprint $table) {
            // Remove old pdf_file column if it exists
            if (Schema::hasColumn('reseller_applications', 'pdf_file')) {
                $table->dropColumn('pdf_file');
            }
            
            // Remove old location columns if they exist
            if (Schema::hasColumn('reseller_applications', 'country')) {
                $table->dropColumn(['country', 'province', 'city', 'zip_code']);
            }
            
            // Add new document photo columns
            $table->string('business_permit_photo')->nullable()->after('business_license_id');
            $table->string('sanitation_cert_photo')->nullable()->after('business_permit_photo');
            $table->string('govt_id_photo_1')->nullable()->after('sanitation_cert_photo');
            $table->string('govt_id_photo_2')->nullable()->after('govt_id_photo_1');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reseller_applications', function (Blueprint $table) {
            // Remove new columns
            $table->dropColumn([
                'business_permit_photo',
                'sanitation_cert_photo',
                'govt_id_photo_1',
                'govt_id_photo_2'
            ]);
            
            // Restore old columns
            $table->string('pdf_file')->nullable();
            $table->string('country')->nullable();
            $table->string('province')->nullable();
            $table->string('city')->nullable();
            $table->string('zip_code')->nullable();
        });
    }
};