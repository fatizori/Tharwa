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
            'id'=> 1,
            'nom' => 'mahfoud',
            'prenom' => 'mahfoud',
            'adresse'=> 'ex domaine morsli ahmed routes des dunes cheraga'
        ]);
    }
}
