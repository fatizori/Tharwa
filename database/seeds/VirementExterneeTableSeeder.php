<?php

use Illuminate\Database\Seeder;
use App\Models\VirementExterne;

class VirementExterneeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        VirementExterne::create([
            'id'=> 1,
            'num_acc' => 1,
            'code_bnk' => 'THW',
            'code_curr'=> 'DZD',
            'num_acc_ext' => 20,
            'code_bnk_ext' => 'BNA',
            'code_curr_ext' => 'DZD',
            'name_ext' => 'amel Azi',
            'amount_vir' => 450000,
            'sens' => 0,
            'status'=> 0,
            'url_xml'=> 'THW1VersBNA20',
            'id_commission' => 'VCE',
            'amount_commission' => 9000
        ]);


        VirementExterne::create([
            'id'=>2,
            'num_acc' => 15,
            'code_bnk' => 'BNA',
            'code_curr'=> 'DZD',
            'num_acc_ext' => 2,
            'code_bnk_ext' => 'THW',
            'code_curr_ext' => 'DZD',
            'name_ext' => 'Ilhem Aissaoui',
            'amount_vir' => 380000,
            'sens' => 1,
            'status'=> 0,
            'url_xml'=> 'BNA15VersTHW2',
            'id_commission' => 'VRE',
            'amount_commission' => 1900
        ]);

        VirementExterne::create([
            'id'=> 3,
            'num_acc' => 4,
            'code_bnk' => 'THW',
            'code_curr'=> 'EUR',
            'num_acc_ext' => 100,
            'code_bnk_ext' => 'BDR',
            'code_curr_ext' => 'DZD',
            'name_ext' => 'karim ',
            'amount_vir' => 450000,
            'sens' => 0,
            'status'=> 0,
            'url_xml'=> 'THW4VersBDR100',
            'id_commission' => 'VCE',
            'amount_commission' => 9000
        ]);

        VirementExterne::create([
            'id'=> 4,
            'num_acc' => 6,
            'code_bnk' => 'THW',
            'code_curr'=> 'USD',
            'num_acc_ext' => 50,
            'code_bnk_ext' => 'BDR',
            'code_curr_ext' => 'DZD',
            'name_ext' => 'Hala zitouni',
            'amount_vir' => 295000,
            'sens' => 1,
            'status'=> 0,
            'url_xml'=> 'BDR50VersTHW6',
            'id_commission' => 'VRE',
            'amount_commission' => 1475
        ]);


    }
}
