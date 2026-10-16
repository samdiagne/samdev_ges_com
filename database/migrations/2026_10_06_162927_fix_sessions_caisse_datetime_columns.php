<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Convertit ouvert_le et ferme_le de TIMESTAMP → DATETIME.
     *
     * MySQL TIMESTAMP stocke en UTC et reconvertit selon la timezone de
     * la connexion lors de la lecture/écriture.  Quand le serveur PHP est
     * en Europe/Berlin (UTC+2) et l'app Laravel en Africa/Dakar (UTC+0),
     * le décalage provoque des dates incohérentes.
     *
     * DATETIME est insensible au fuseau de la connexion MySQL :
     * il stocke et restitue exactement la valeur saisie.
     */
    public function up(): void
    {
        // 1. Corriger les données existantes AVANT le changement de type.
        //    Les ferme_le stockées en TIMESTAMP ont subi une conversion (-1h).
        //    On réajuste les lignes déjà fermées.
        //    NOTE : on ne touche pas à ouvert_le car elle était stockée avant
        //    le bug de fermeture (les sessions ouvertes et non encore fermées
        //    peuvent avoir des ouvert_le correctes ou incorrectes selon le moment).
        //    La meilleure approche est de laisser les données telles quelles et
        //    simplement changer le type de colonne pour éviter de futurs bugs.

        Schema::table('sessions_caisse', function (Blueprint $table) {
            // Modifier le type des colonnes (TIMESTAMP → DATETIME)
            // useCurrent() est ignoré pour DATETIME, donc on passe nullable()
            $table->dateTime('ouvert_le')->change();
            $table->dateTime('ferme_le')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('sessions_caisse', function (Blueprint $table) {
            $table->timestamp('ouvert_le')->change();
            $table->timestamp('ferme_le')->nullable()->change();
        });
    }
};
