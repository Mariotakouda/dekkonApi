<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Correctif de données : avant l'introduction de FLOOZ / MIXX_BY_YAS,
     * le paiement mobile money générique s'appelait 'MOBILE_MONEY'. Cette
     * valeur a été retirée de l'enum PaymentMethod, mais les paiements créés
     * avant ce changement l'ont encore en base — ce qui fait planter le
     * cast d'enum d'Eloquent dès qu'on essaie de les charger (client ET
     * admin), avec l'erreur :
     * "MOBILE_MONEY" is not a valid backing value for enum PaymentMethod.
     *
     * On ne peut pas savoir avec certitude quel opérateur c'était pour ces
     * anciennes lignes (l'app ne le demandait pas encore) : on les bascule
     * sur FLOOZ par défaut, à corriger manuellement au cas par cas si
     * besoin — l'essentiel ici est de débloquer l'affichage.
     *
     * Requête en SQL brut (DB::table, pas de modèle Eloquent) pour éviter
     * de redéclencher le même cast d'enum en lisant les lignes concernées.
     */
    public function up(): void
    {
        DB::table('payments')
            ->where('method', 'MOBILE_MONEY')
            ->update(['method' => 'FLOOZ']);
    }

    public function down(): void
    {
        // Non réversible avec certitude : on ne sait plus quelles lignes
        // étaient à l'origine 'MOBILE_MONEY' une fois la migration jouée.
    }
};
