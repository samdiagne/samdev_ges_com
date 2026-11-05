<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('achat_reglements', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique()->nullable();
            $table->foreignId('achat_id')->constrained('achats')->cascadeOnDelete();
            $table->foreignId('session_caisse_id')->nullable()->constrained('sessions_caisse')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('montant', 12, 2);
            $table->string('mode_paiement')->default('cash'); // cash, wave, om, virement, etc.
            $table->dateTime('date_reglement');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Migrer les versements existants pour tous les achats ayant déjà un montant_paye > 0
        $achats = DB::table('achats')->where('montant_paye', '>', 0)->get();
        foreach ($achats as $achat) {
            DB::table('achat_reglements')->insert([
                'reference'         => 'REG-' . date('Ymd') . '-' . str_pad($achat->id, 4, '0', STR_PAD_LEFT),
                'achat_id'          => $achat->id,
                'session_caisse_id' => $achat->session_caisse_id,
                'user_id'           => $achat->user_id,
                'montant'           => $achat->montant_paye,
                'mode_paiement'     => $achat->mode_paiement ?: 'cash',
                'date_reglement'    => $achat->date_achat ?? now(),
                'notes'             => 'Paiement initial migré',
                'created_at'        => $achat->created_at ?? now(),
                'updated_at'        => $achat->updated_at ?? now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('achat_reglements');
    }
};
