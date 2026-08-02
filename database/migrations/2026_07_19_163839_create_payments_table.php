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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_subscription_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('gateway')->nullable();
            $table->string('transaction_id')->nullable();

            $table->decimal('amount', 10, 2)->default(0);
            $table->string('currency', 3)->nullable();

            $table->string('status')->default('pending');

            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->unique(['gateway', 'transaction_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
