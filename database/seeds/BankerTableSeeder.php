<?php

use Illuminate\Database\Seeder;
use App\Models\Banker;

class BankerTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Banker::create([
            'id'=> 2,
            'name' => 'nihad',
            'firstname' => 'banat',
            'address'=> 'ex domaine morsli ahmed routes des dunes cheraga',
            'photo' => 'photo.jpg',
            'id_creator' => 1
        ]);

    }
}
