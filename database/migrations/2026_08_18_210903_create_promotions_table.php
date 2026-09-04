<?php

use App\Enums\PromotionType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('type'); // PromotionType
            $table->decimal('value', 12, 2); // pourcentage ou montant fixe selon "type"
            $table->decimal('minimum_amount', 12, 2)->nullable();
            $table->decimal('maximum_discount_amount', 12, 2)->nullable(); // plafond si type = PERCENTAGE
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->integer('usage_limit')->nullable(); // null = illimité
            $table->integer('usage_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('code');
            $table->index('is_active');
            $table->index(['starts_at', 'ends_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
