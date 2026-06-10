<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParametresSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('parametres')->insertOrIgnore([
            'nom'               => 'Mon Restaurant',
            'ninea'             => '',
            'rc'                => '',
            'telephone'         => '+221 XX XXX XX XX',
            'whatsapp'          => '+221 XX XXX XX XX',
            'email'             => 'contact@monrestaurant.sn',
            'adresse'           => 'Dakar, Sénégal',
            'devise'            => 'FCFA',
            'prefixe_facture'   => 'FAC',
            'compteur_facture'  => 1,
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);
    }
}
