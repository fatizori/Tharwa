<?php

use Illuminate\Database\Seeder;
use App\Models\Manager;

class ManagerTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Manager::create([
            'id'=> 4,
            'name' => 'mahfoud',
            'firstname' => 'mahfoud',
            'address'=> 'ex domaine morsli ahmed routes des dunes cheraga',
            'photo' => 'photo.jpg',
        ]);
    }
}
