<?php
use Illuminate\Database\Seeder;
use App\Models\Account;

class AccountTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Account::create([
            'id' => 1,
            'id_customer' => 5,
            'status' => 1,
            'balance' => 20000
        ]);
        Account::create([
            'id' => 2,
            'id_customer' => 6,
            'status' => 1
        ]);
        Account::create([
            'id' => 3,
            'id_customer' => 6,
            'status' => 1,
            'type' => 2
        ]);
        Account::create([
            'id' => 4,
            'id_customer' => 5,
            'status' => 1,
            'type' => 3
        ]);

    }
}