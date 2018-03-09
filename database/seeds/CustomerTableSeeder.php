<?php
use Illuminate\Database\Seeder;
use App\Models\Customer;


class CustomerTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Customer::create([
                'id' => 5,
                'name' => 'hana',
                'address'=>'Alger',
                'wilaya'=> 'Alger',
                'commune' => 'ben aknoun',
                'function' => 'Developpeur',
                'type' => 0,
                'photo' => 'photo.jpg',
        ]);

    }
}