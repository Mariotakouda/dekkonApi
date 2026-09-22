<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Numéro utilisé pour le push Mobile Money (Flooz / Mixx by YAS),
            // saisi par le client au moment de payer — peut différer du
            // téléphone de son compte ou de son adresse de livraison.
            $table->string('phone_number')->nullable()->after('method');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('phone_number');
        });
    }
};
