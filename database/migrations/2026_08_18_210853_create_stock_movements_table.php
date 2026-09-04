<?php

use App\Enums\StockMovementType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_variant_id')->constrained('product_variants')->cascadeOnDelete();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type'); // StockMovementType
            $table->integer('quantity'); // toujours positif, le sens est donné par "type"
            $table->string('reason')->nullable();
            $table->string('reference_type')->nullable(); // ex: App\Models\Order
            $table->uuid('reference_id')->nullable(); // ex: id de la commande à l'origine du mouvement
            $table->timestamps();

            $table->index('product_variant_id');
            $table->index('type');
            $table->index(['reference_type', 'reference_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};