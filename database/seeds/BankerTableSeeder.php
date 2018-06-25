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
            'name' => 'imane',
            'firstname' => 'senouci',
            'address'=> 'ex domaine morsli ahmed routes des dunes cheraga',
            'photo' => 'photo.jpg',
            'id_creator' => 1
        ]);

        Banker::create([
            'id'=> 3,
            'name' => 'mahfoud',
            'firstname' => 'cheikh aissa',
            'address'=> 'ex domaine morsli ahmed routes des dunes cheraga',
            'photo' => 'photo.jpg',
            'id_creator' => 1
        ]);

        Banker::create([
            'id'=> 4,
            'name' => 'nihad',
            'firstname' => 'banat',
            'address'=> 'ex domaine morsli ahmed routes des dunes cheraga',
            'photo' => 'photo.jpg',
            'id_creator' => 1
        ]);

    }
}
