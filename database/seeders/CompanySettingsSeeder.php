<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanySettingsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('company_settings')->insertOrIgnore([
            'name'            => 'Al-Adab Tech Store',
            'ninea'           => '',
            'rc'              => '',
            'phone'           => '+221 XX XXX XX XX',
            'whatsapp'        => '+221 XX XXX XX XX',
            'email'           => 'contact@aladab-tech.sn',
            'address'         => 'Dakar, Sénégal',
            'tva_rate'        => 0.00,
            'currency'        => 'FCFA',
            'invoice_prefix'  => 'FAC',
            'invoice_counter' => 1,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);
    }
}