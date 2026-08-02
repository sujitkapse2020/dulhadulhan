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
        Schema::create('user_families', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('father_name')->nullable();
            $table->string('father_occupation')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('mother_occupation')->nullable();

            $table->unsignedTinyInteger('brothers')->default(0);
            $table->unsignedTinyInteger('married_brothers')->default(0);
            $table->unsignedTinyInteger('sisters')->default(0);
            $table->unsignedTinyInteger('married_sisters')->default(0);

            $table->string('family_type')->nullable();
            $table->string('family_status')->nullable();
            $table->string('family_values')->nullable();
            $table->string('native_place')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_families');
    }
};
