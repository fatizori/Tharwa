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
            'id' => 1,
            'description' => 'Courant vers épargne',
            'code' => 'CVE',
            'type' => 0,
            'valeur' => 0
        ]);
        Commission::create([
            'id' => 2,
            'description' => 'Epargne vers courant',
            'code' => 'EVC',
            'type' => 0,
            'valeur' => 0.10
        ]);
        Commission::create([
            'id' => 3,
            'description' => 'Courant vers devise',
            'code' => 'CVD',
            'type' => 0,
            'valeur' => 2.00
        ]);
        Commission::create([
            'id' => 4,
            'description' => 'Devise vers courant',
            'code' => 'DVC',
            'type' => 0,
            'valeur' => 1.5
        ]);
        Commission::create([
            'id' => 5,
            'description' => 'Vers un autre client THARWA',
            'code' => 'VCT',
            'type' => 0,
            'valeur' => 1.00
        ]);
        Commission::create([
            'id' => 6,
            'description' => 'Vers un client d une autre banque',
            'code' => 'VCE',
            'type' => 0,
            'valeur' => 2.00
        ]);
        Commission::create([
            'id' => 7,
            'description' => 'Virement reçu depuis une autre banque',
            'code' => 'VRE',
            'type' => 0,
            'valeur' => 0.5
        ]);
        Commission::create([
            'id' => 8,
            'description' => 'Commission mensuelle frais de gestion compte courant',
            'code' => 'CMC',
            'type' => 1,
            'valeur' => 100
        ]);
        Commission::create([
            'id' => 9,
            'description' => 'Commission mensuelle frais de gestion compte épargne',
            'code' => 'CME',
            'type' => 1,
            'valeur' => 50
        ]);
        Commission::create([
            'id' => 10,
            'description' => 'Commission mensuelle frais de gestion compte devise',
            'code' => 'CMD',
            'type' => 1,
            'valeur' => 200
        ]);
        Commission::create([
            'id' => 11,
            'description' => 'Commission sur un order de vision : total des commissions sur les virements générés',
            'code' => 'CVT',
            'type' => 0,
            'valeur' => 0
        ]);
    }
}
