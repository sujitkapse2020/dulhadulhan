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
        Schema::create('sub_castes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caste_id')->constrained('castes')->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();
 
            $table->unique(['caste_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_castes');
    }
};
