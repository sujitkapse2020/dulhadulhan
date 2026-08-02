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
        Schema::create('user_occupations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('occupation_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('company_name')->nullable();
            $table->string('designation')->nullable();
            $table->decimal('annual_income', 15, 2)->nullable();
            $table->string('currency', 3)->nullable();
            $table->string('work_location')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_occupations');
    }
};
