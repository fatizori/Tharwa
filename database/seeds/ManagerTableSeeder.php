<?php

use Illuminate\Database\Seeder;

class ManagerTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('managers')->insert([
            'id'=> 4,
            'name' => 'mahfoud',
            'firstname' => 'mahfoud',
            'address'=> 'ex domaine morsli ahmed routes des dunes cheraga'
        ]);
    }
}
