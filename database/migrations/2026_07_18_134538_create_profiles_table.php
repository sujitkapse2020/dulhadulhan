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
        if (Schema::hasTable('profiles')) {
            return;
        }

        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
 
            $table->string('profile_for')->nullable();
            $table->string('gender')->nullable();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->date('dob')->nullable();
            $table->unsignedTinyInteger('age')->nullable();
 
            $table->unsignedSmallInteger('height')->nullable();
            $table->unsignedSmallInteger('weight')->nullable();
 
            $table->string('marital_status')->nullable();
            $table->string('mother_tongue')->nullable();
 
            $table->foreignId('religion_id')->nullable()->constrained('religions')->nullOnDelete();
            $table->foreignId('caste_id')->nullable()->constrained('castes')->nullOnDelete();
            $table->foreignId('sub_caste_id')->nullable()->constrained('sub_castes')->nullOnDelete();
            $table->string('gotra')->nullable();
 
            $table->string('manglik')->nullable();
            $table->string('blood_group')->nullable();
 
            $table->string('physical_status')->nullable();
            $table->string('body_type')->nullable();
            $table->string('complexion')->nullable();
 
            $table->text('about_me')->nullable();
            $table->text('hobbies')->nullable();
            $table->string('eating_habits')->nullable();
            $table->string('drinking_habits')->nullable();
            $table->string('smoking_habits')->nullable();
            
 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
