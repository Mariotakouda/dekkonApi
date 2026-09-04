<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_promotions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignUuid('promotion_id')->nullable()->constrained('promotions')->nullOnDelete();
            $table->decimal('discount_amount', 12, 2);
            $table->timestamp('created_at')->useCurrent();

            $table->index('order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_promotions');
    }
};
