<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('landing_page_contents', function (Blueprint $table) {
            $table->id();
            $table->string('section')->unique(); // e.g. hero, about, articles
            $table->string('title')->nullable();
            $table->text('content')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_page_contents');
    }
};
