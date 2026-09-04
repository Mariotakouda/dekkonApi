<?php

use App\Enums\CartStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->string('status')->default(CartStatus::ACTIVE->value);
            $table->timestamps();

            $table->index('customer_id');
            $table->index('status');
        });

        // Un seul panier ACTIVE par client (section 39 du cahier des charges)
        DB::statement('
            CREATE UNIQUE INDEX carts_customer_active_unique
            ON carts (customer_id)
            WHERE status = \'ACTIVE\'
        ');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS carts_customer_active_unique');
        Schema::dropIfExists('carts');
    }
};
