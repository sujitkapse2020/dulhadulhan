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
        Schema::create('user_profile_views', function (Blueprint $table) {
            $table->id();

            $table->foreignId('viewer_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('profile_owner_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamp('viewed_at')->useCurrent();

            $table->timestamps();

            $table->index(['profile_owner_id', 'viewed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profile_views');
    }
};
