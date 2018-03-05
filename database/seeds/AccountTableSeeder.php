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
        DB::table('accounts')->insert([
            'id' => 1,
            'id_customer' => 3
        ]);

    }
}