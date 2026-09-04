<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_promotion', function (Blueprint $table) {
            $table->foreignUuid('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignUuid('promotion_id')->constrained('promotions')->cascadeOnDelete();
            $table->timestamps();

            $table->primary(['product_id', 'promotion_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_promotion');
    }
};
