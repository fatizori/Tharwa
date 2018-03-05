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
            'name' => 'hana',
            'address'=>'Alger',
            'wilaya'=> 'Alger',
            'commune' => 'ben aknoun',
            'function' => 'Developpeur',
            'type' => 0
        ]);

    }
}