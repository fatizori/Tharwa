<?php

use Illuminate\Database\Seeder;
use App\Models\Commission;

class CommissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Commission::create([
            'id' => 'CVE',
            'description' => 'Courant vers épargne',
            'type' => 0,
            'valeur' => 0
        ]);
        Commission::create([
            'id' => 'EVC',
            'description' => 'Epargne vers courant',
            'type' => 0,
            'valeur' => 0.10
        ]);
        Commission::create([
            'id' => 'CVD',
            'description' => 'Courant vers devise',
            'type' => 0,
            'valeur' => 2.00
        ]);
        Commission::create([
            'id' => 'DVC',
            'description' => 'Devise vers courant',
            'type' => 0,
            'valeur' => 1.5
        ]);
        Commission::create([
            'id' => 'VCT',
            'description' => 'Vers un autre client THARWA',
            'type' => 0,
            'valeur' => 1.00
        ]);
        Commission::create([
            'id' => 'VCE',
            'description' => 'Vers un client d une autre banque',
            'type' => 0,
            'valeur' => 2.00
        ]);
        Commission::create([
            'id' => 'VRE',
            'description' => 'Virement reçu depuis une autre banque',
            'type' => 0,
            'valeur' => 0.5
        ]);
        Commission::create([
            'id' => 'CMC',
            'description' => 'Commission mensuelle frais de gestion compte courant',
            'type' => 1,
            'valeur' => 100
        ]);
        Commission::create([
            'id' => 'CME',
            'description' => 'Commission mensuelle frais de gestion compte épargne',
            'type' => 1,
            'valeur' => 50
        ]);
        Commission::create([
            'id' => 'CMD',
            'description' => 'Commission mensuelle frais de gestion compte devise',
            'type' => 1,
            'valeur' => 200
        ]);
        Commission::create([
            'id' => 'CVT',
            'description' => 'Commission sur un order de vision : total des commissions sur les virements générés',
            'type' => 0,
            'valeur' => 0
        ]);
    }
}
