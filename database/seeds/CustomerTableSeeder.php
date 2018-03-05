<?php
use Illuminate\Database\Seeder;


class CustomerTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('customers')->insert([
            'id' => 3,
            'nom' => 'hana',
            'adresse'=>'Alger',
            'wilaya'=> "Alger",
            "commune"=>"ben aknoun",
            "fonction"=>"Developpeur",
            "type"=> 0,
            
        ]);

    }
}