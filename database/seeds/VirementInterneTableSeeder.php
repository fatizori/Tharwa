<?php

use Illuminate\Database\Seeder;
use App\Models\VirementInterne;

class VirementInterneTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        VirementInterne::create([
            'id'=> 1,
            'num_acc_sender' => 1,
            'code_bnk_sender' => 'THW',
            'code_curr_sender'=> 'DZD',
            'num_acc_receiver' => 4,
            'code_bnk_receiver' => 'THW',
            'code_curr_receiver' => 'EUR',
            'montant_virement' => 1000,
            'status'=> 1,
            'type' => 0,
            'id_commission' => 'CVD',
            'montant_commission' => 20
        ]);

        VirementInterne::create([
            'id'=> 2,
            'num_acc_sender' => 1,
            'code_bnk_sender' => 'THW',
            'code_curr_sender'=> 'DZD',
            'num_acc_receiver' => 2,
            'code_bnk_receiver' => 'THW',
            'code_curr_receiver' => 'DZD',
            'montant_virement' => 2000,
            'status'=> 1,
            'type' => 0,
            'id_commission' => 'VCT',
            'montant_commission' => 20
        ]);

        VirementInterne::create([
            'id'=> 3,
            'num_acc_sender' => 2,
            'code_bnk_sender' => 'THW',
            'code_curr_sender'=> 'DZD',
            'num_acc_receiver' => 3,
            'code_bnk_receiver' => 'THW',
            'code_curr_receiver' => 'DZD',
            'montant_virement' => 1500,
            'status'=> 1,
            'type' => 0,
            'id_commission' => 'CVE',
            'montant_commission' => 0
        ]);

        VirementInterne::create([
            'id'=> 4,
            'num_acc_sender' => 3,
            'code_bnk_sender' => 'THW',
            'code_curr_sender'=> 'DZD',
            'num_acc_receiver' => 2,
            'code_bnk_receiver' => 'THW',
            'code_curr_receiver' => 'DZD',
            'montant_virement' => 10000,
            'status'=> 1,
            'type' => 0,
            'id_commission' => 'EVC',
            'montant_commission' => 10
        ]);

        VirementInterne::create([
            'id'=> 5,
            'num_acc_sender' => 4,
            'code_bnk_sender' => 'THW',
            'code_curr_sender'=> 'EUR',
            'num_acc_receiver' => 6,
            'code_bnk_receiver' => 'THW',
            'code_curr_receiver' => 'USD',
            'montant_virement' => 300000,
            'status'=> 0,
            'type' => 0,
            'id_commission' => 'VCT',
            'montant_commission' => 3000
        ]);

        VirementInterne::create([
            'id'=> 6,
            'num_acc_sender' => 6,
            'code_bnk_sender' => 'THW',
            'code_curr_sender'=> 'USD',
            'num_acc_receiver' => 2,
            'code_bnk_receiver' => 'THW',
            'code_curr_receiver' => 'DZD',
            'montant_virement' => 250000,
            'status'=> 0,
            'type' => 0,
            'id_commission' => 'DVC',
            'montant_commission' => 3750
        ]);

        VirementInterne::create([
            'id'=> 7,
            'num_acc_sender' => 1,
            'code_bnk_sender' => 'THW',
            'code_curr_sender'=> 'DZD',
            'num_acc_receiver' => 3,
            'code_bnk_receiver' => 'THW',
            'code_curr_receiver' => 'DZD',
            'montant_virement' => 400000,
            'status'=> 0,
            'type' => 0,
            'id_commission' => 'VCT',
            'montant_commission' => 4000
        ]);

      
    }
}
