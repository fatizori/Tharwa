<?php

use Illuminate\Database\Seeder;
use App\Models\MensuelleCommission;

class MensuelleCommissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MensuelleCommission::create([
            'id'=> 1,
            'amount' => 45000
        ]);

        MensuelleCommission::create([
            'id'=> 2,
            'amount' => 25900
        ]);

        MensuelleCommission::create([
            'id'=> 3,
            'amount' => 50090
        ]);

        MensuelleCommission::create([
            'id'=> 4,
            'amount' => 28563
        ]);

        MensuelleCommission::create([
            'id'=> 5,
            'amount' => 75842
        ]);

        MensuelleCommission::create([
            'id'=> 6,
            'amount' => 20320
        ]);
    }

}
