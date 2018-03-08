<?php
namespace database\seeds;
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
        
    }
}
