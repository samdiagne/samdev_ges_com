<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Supprime le comportement implicite "ON UPDATE CURRENT_TIMESTAMP" sur
     * ventes.vendu_le et factures.emise_le (causé par MySQL/MariaDB sur la
     * première colonne TIMESTAMP NOT NULL sans DEFAULT explicite), et restaure
     * vendu_le depuis created_at pour les ventes écrasées lors de la migration
     * du 2026-11-22 00:21:26.
     */
    public function up(): void
    {
        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            DB::statement('ALTER TABLE ventes MODIFY COLUMN vendu_le TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP');
            DB::statement('ALTER TABLE factures MODIFY COLUMN emise_le TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP');

            // Restaurer vendu_le uniquement pour les ventes écrasées lors du backfill du 2026-11-22 00:21:26
            DB::statement("
                UPDATE ventes
                SET vendu_le = created_at
                WHERE vendu_le = '2026-11-22 00:21:26'
                  AND created_at IS NOT NULL
            ");
        }
    }

    public function down(): void
    {
        // On ne restaure pas le bug ON UPDATE CURRENT_TIMESTAMP
    }
};
