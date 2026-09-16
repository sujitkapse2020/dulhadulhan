<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'delete_reason')) {
                $table->text('delete_reason')->nullable();
            }
        });

        Schema::table('profiles', function (Blueprint $table) {
            $columns = [
                'user_id' => fn () => $table->foreignId('user_id')->nullable()->after('id'),
                'profile_for' => fn () => $table->string('profile_for')->nullable(),
                'gender' => fn () => $table->string('gender')->nullable(),
                'first_name' => fn () => $table->string('first_name')->nullable(),
                'middle_name' => fn () => $table->string('middle_name')->nullable(),
                'last_name' => fn () => $table->string('last_name')->nullable(),
                'dob' => fn () => $table->date('dob')->nullable(),
                'age' => fn () => $table->unsignedTinyInteger('age')->nullable(),
                'height' => fn () => $table->unsignedSmallInteger('height')->nullable(),
                'weight' => fn () => $table->unsignedSmallInteger('weight')->nullable(),
                'marital_status' => fn () => $table->string('marital_status')->nullable(),
                'mother_tongue' => fn () => $table->string('mother_tongue')->nullable(),
                'religion_id' => fn () => $table->foreignId('religion_id')->nullable(),
                'caste_id' => fn () => $table->foreignId('caste_id')->nullable(),
                'sub_caste_id' => fn () => $table->foreignId('sub_caste_id')->nullable(),
                'gotra' => fn () => $table->string('gotra')->nullable(),
                'manglik' => fn () => $table->string('manglik')->nullable(),
                'blood_group' => fn () => $table->string('blood_group')->nullable(),
                'physical_status' => fn () => $table->string('physical_status')->nullable(),
                'body_type' => fn () => $table->string('body_type')->nullable(),
                'complexion' => fn () => $table->string('complexion')->nullable(),
                'about_me' => fn () => $table->text('about_me')->nullable(),
                'hobbies' => fn () => $table->text('hobbies')->nullable(),
                'eating_habits' => fn () => $table->string('eating_habits')->nullable(),
                'drinking_habits' => fn () => $table->string('drinking_habits')->nullable(),
                'smoking_habits' => fn () => $table->string('smoking_habits')->nullable(),
            ];

            foreach ($columns as $column => $definition) {
                if (! Schema::hasColumn('profiles', $column)) {
                    $definition();
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('delete_reason');
        });

        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn([
                'user_id', 'profile_for', 'gender', 'first_name', 'middle_name', 'last_name', 'dob', 'age',
                'height', 'weight', 'marital_status', 'mother_tongue', 'religion_id', 'caste_id', 'sub_caste_id',
                'gotra', 'manglik', 'blood_group', 'physical_status', 'body_type', 'complexion', 'about_me',
                'hobbies', 'eating_habits', 'drinking_habits', 'smoking_habits',
            ]);
        });
    }
};