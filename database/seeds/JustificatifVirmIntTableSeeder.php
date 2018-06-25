<?php

use Illuminate\Database\Seeder;
use App\Models\JustificatifVirmInt;

class JustificatifVirmIntTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        JustificatifVirmInt::create([
            'id'=> 1,
            'url_justif' => 'justif.png',
            'status' => 0,
            'date_action_banker'=> null,
            'id_vrm' => 5,
            'id_banker' => 0
        ]);

        JustificatifVirmInt::create([
            'id'=> 2,
            'url_justif' => 'justif1.png',
            'status' => 0,
            'date_action_banker'=> null,
            'id_vrm' => 6,
            'id_banker' => 0
        ]);

        JustificatifVirmInt::create([
            'id'=> 3,
            'url_justif' => 'justif2.png',
            'status' => 0,
            'date_action_banker'=> null,
            'id_vrm' => 7,
            'id_banker' => 0
        ]);
    }
}
