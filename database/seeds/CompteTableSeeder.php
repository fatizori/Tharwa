<?php
use Illuminate\Database\Seeder;
use App\Models\Compte;

class CompteTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('comptes')->insert([
            'id' => 1,
            'id_client' => 3
            
        ]);

    }
}