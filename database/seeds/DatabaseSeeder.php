<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call('UserTableSeeder');
        $this->call('BankerTableSeeder');
        $this->call('CustomerTableSeeder');
        $this->call('AccountTableSeeder');
        $this->call('ManagerTableSeeder');
        $this->call('CommissionTableSeeder');
        $this->call('BankTableSeeder');
        $this->call('VirementExterneeTableSeeder');
        $this->call('JustificatifVirmExtTableSeeder');
        $this->call('VirementInterneTableSeeder');
        $this->call('JustificatifVirmIntTableSeeder');
        $this->call('MensuelleCommissionTableSeeder');
    }
}
