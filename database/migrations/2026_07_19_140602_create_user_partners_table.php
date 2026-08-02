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
        Schema::create('user_partners', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->unique()
                ->constrained()
                ->cascadeOnDelete();

            $table->unsignedTinyInteger('age_from')->nullable();
            $table->unsignedTinyInteger('age_to')->nullable();

            $table->unsignedSmallInteger('height_from')->nullable();
            $table->unsignedSmallInteger('height_to')->nullable();

            $table->foreignId('religion_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('caste_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('education_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('occupation_id')->nullable()->constrained('occupations')->nullOnDelete();

            $table->string('income')->nullable();

            $table->foreignId('country_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('state_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete();

            $table->string('marital_status')->nullable();
            $table->string('mother_tongue')->nullable();
            $table->string('diet')->nullable();
            $table->string('smoking')->nullable();
            $table->string('drinking')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_partners');
    }
};
