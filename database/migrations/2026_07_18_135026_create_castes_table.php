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
        Schema::create('castes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('religion_id')->constrained('religions')->cascadeOnDelete();
            $table->string('name');
            $table->string('status')->default('active');
            $table->timestamps();
 
            $table->unique(['religion_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('castes');
    }
};
