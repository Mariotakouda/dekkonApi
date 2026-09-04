<?php

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('order_id')->constrained('orders')->restrictOnDelete();
            $table->string('transaction_reference')->nullable(); // ID retourné par Fedapay
            $table->string('provider')->nullable(); // ex: "fedapay"
            $table->string('method'); // PaymentMethod
            $table->string('status')->default(PaymentStatus::PENDING->value);
            $table->decimal('amount', 12, 2);
            $table->timestamp('paid_at')->nullable();
            $table->json('metadata')->nullable(); // réponse brute du gateway (utile debug)
            $table->timestamps();

            $table->unique(['provider', 'transaction_reference']);
            $table->index('order_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
