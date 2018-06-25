<?php

use Illuminate\Database\Seeder;
use App\Models\JustificatifVirmExt;

class JustificatifVirmExtTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        JustificatifVirmExt::create([
            'id'=> 1,
            'url_justif' => 'justif.png',
            'status' => 0,
            'date_action_banker'=> null,
            'id_vrm' => 1,
            'id_banker' => 0
        ]);

        JustificatifVirmExt::create([
            'id'=> 2,
            'url_justif' => 'justif.png',
            'status' => 0,
            'date_action_banker'=> null,
            'id_vrm' => 2,
            'id_banker' => 0
        ]);

        JustificatifVirmExt::create([
            'id'=> 3,
            'url_justif' => 'justif.png',
            'status' => 0,
            'date_action_banker'=> null,
            'id_vrm' => 3,
            'id_banker' => 0
        ]);

        JustificatifVirmExt::create([
            'id'=> 4,
            'url_justif' => 'justif.png',
            'status' => 0,
            'date_action_banker'=> null,
            'id_vrm' => 4,
            'id_banker' => 0
        ]);
    }
}
