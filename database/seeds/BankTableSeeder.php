<?php

use Illuminate\Database\Seeder;
use App\Models\Bank;

class BankTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Bank::create([
            'id'=> 'THW',
            'email' => 'tharwa@gamail.com',
            'name' => 'Tharwa',
            'address'=> 'ex domaine morsli ahmed routes des dunes cheraga',
            'social_reason' => 'tharwa',
            'status' => 1
        ]);

        Bank::create([
            'id'=> 'BNA',
            'email' => 'bna@gamail.com',
            'name' => 'bank national algerien',
            'address'=> 'ex domaine morsli ahmed routes des dunes cheraga',
            'social_reason' => 'bna',
            'status' => 1
        ]);

        Bank::create([
            'id'=> 'BDR',
            'email' => 'BADR@gamail.com',
            'name' => 'bank  algerien',
            'address'=> 'ex domaine morsli ahmed routes des dunes cheraga',
            'social_reason' => 'badr',
            'status' => 1
        ]);
    }
}
