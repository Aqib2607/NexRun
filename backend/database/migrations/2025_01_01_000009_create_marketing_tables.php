<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('referred_customer_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedInteger('reward_points')->default(0);
            $table->enum('reward_status', ['pending', 'earned', 'cancelled'])->default('pending');
            $table->timestamps();

            $table->unique(['referrer_id', 'referred_customer_id']);
        });

        Schema::create('loyalty_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();
            $table->enum('transaction_type', ['earn', 'redeem', 'expire', 'adjustment', 'referral_bonus']);
            $table->integer('points'); // positive for earn, negative for redeem
            $table->unsignedInteger('balance_after')->default(0);
            $table->string('remarks')->nullable();
            $table->morphs('source'); // polymorphic: order_id, referral_id, etc.
            $table->timestamp('created_at')->useCurrent();

            $table->index('customer_id');
            $table->index('transaction_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_transactions');
        Schema::dropIfExists('referrals');
    }
};
