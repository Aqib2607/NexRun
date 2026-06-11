<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('session_id')->nullable();
            $table->timestamp('viewed_at')->useCurrent();

            $table->index(['customer_id', 'product_id']);
            $table->index('viewed_at');
        });

        Schema::create('recommendation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->enum('recommendation_type', ['similar', 'trending', 'frequently_bought', 'personalized']);
            $table->boolean('was_clicked')->default(false);
            $table->boolean('was_purchased')->default(false);
            $table->timestamp('generated_at')->useCurrent();

            $table->index('customer_id');
            $table->index('recommendation_type');
        });

        Schema::create('sales_summary_daily', function (Blueprint $table) {
            $table->date('sales_date')->primary();
            $table->unsignedInteger('total_orders')->default(0);
            $table->decimal('total_revenue', 14, 2)->default(0);
            $table->unsignedInteger('total_customers')->default(0);
            $table->decimal('average_order_value', 10, 2)->default(0);
            $table->unsignedInteger('total_items_sold')->default(0);
            $table->timestamps();
        });

        Schema::create('product_analytics', function (Blueprint $table) {
            $table->foreignId('product_id')->primary()->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('views')->default(0);
            $table->unsignedInteger('purchases')->default(0);
            $table->decimal('revenue_generated', 14, 2)->default(0);
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->unsignedInteger('review_count')->default(0);
            $table->decimal('conversion_rate', 5, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('customer_analytics', function (Blueprint $table) {
            $table->foreignId('customer_id')->primary()->constrained('users')->cascadeOnDelete();
            $table->unsignedInteger('total_orders')->default(0);
            $table->decimal('lifetime_value', 14, 2)->default(0);
            $table->decimal('average_order_value', 10, 2)->default(0);
            $table->date('first_order_date')->nullable();
            $table->date('last_order_date')->nullable();
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('module_name', 50);
            $table->string('action_type', 20); // created, updated, deleted, login, etc.
            $table->string('entity_name', 50);
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('user_id');
            $table->index('module_name');
            $table->index('action_type');
            $table->index('entity_name');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('customer_analytics');
        Schema::dropIfExists('product_analytics');
        Schema::dropIfExists('sales_summary_daily');
        Schema::dropIfExists('recommendation_logs');
        Schema::dropIfExists('product_views');
    }
};
