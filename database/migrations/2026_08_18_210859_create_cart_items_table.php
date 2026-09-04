<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('cart_id')->constrained('carts')->cascadeOnDelete();
            $table->foreignUuid('product_variant_id')->constrained('product_variants')->cascadeOnDelete();
            $table->integer('quantity');
            $table->decimal('unit_price', 12, 2); // snapshot indicatif, jamais source de vérité (section 14)
            $table->timestamps();

            $table->unique(['cart_id', 'product_variant_id']);
            $table->index('cart_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
