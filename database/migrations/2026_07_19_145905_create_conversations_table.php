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
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user1')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('user2')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->text('last_message')->nullable();
            $table->timestamp('last_message_time')->nullable();

            $table->timestamps();

            $table->unique(['user1', 'user2']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
