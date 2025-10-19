<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('landing_page_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('landing_page_content_id')->constrained()->onDelete('cascade');
            $table->string('section')->nullable();
            $table->string('order')->nullable();
            $table->string('title')->nullable();
            $table->text('content')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_page_cards');
    }
};
