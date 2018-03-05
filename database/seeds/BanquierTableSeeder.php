<?php
use Illuminate\Database\Seeder;
use App\Models\Banquier;

class BanquierTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('banquiers')->insert([
            'id'=> 2,
            'nom' => 'nihad',
            'prenom' => 'banat',
            'adresse'=> 'ex domaine morsli ahmed routes des dunes cheraga'
        ]);

    }
}