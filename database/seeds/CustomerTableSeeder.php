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
                'name' => 'Karim',
                'address'=>'Alger qq part',
                'wilaya'=> 'Alger',
                'commune' => 'ben aknoun',
                'fonction' => 'Developpeur',
                'type' => 0,
                'photo' => 'customer1.jpg',
        ]);

        Customer::create([
            'id' => 6,
            'name' => 'Khadidja',
            'address'=>'Oran qq part',
            'wilaya'=> 'Oran',
            'commune' => 'ben Khadda',
            'fonction' => 'Tester',
            'type' => 0,
            'photo' => 'customer1.jpg',
        ]);

    }
}