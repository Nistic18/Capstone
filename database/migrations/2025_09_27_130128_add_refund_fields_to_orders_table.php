<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up()
{
    Schema::table('orders', function (Blueprint $table) {
        $table->enum('refund_status', ['None', 'Pending', 'Approved', 'Rejected'])
              ->default('None');
        $table->text('refund_reason')->nullable();
    });
}

public function down()
{
    Schema::table('orders', function (Blueprint $table) {
        $table->dropColumn(['refund_status', 'refund_reason']);
    });
}

};
