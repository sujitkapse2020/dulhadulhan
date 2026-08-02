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
        Schema::create('membership_plans', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->decimal('price', 10, 2)->default(0);
            $table->unsignedInteger('duration')->nullable();

            $table->unsignedInteger('contact_limit')->nullable();
            $table->boolean('chat')->default(false);
            $table->boolean('video_call')->default(false);
            $table->boolean('priority_listing')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membership_plans');
    }
};
