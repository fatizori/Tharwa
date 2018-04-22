<?php
use Illuminate\Database\Seeder;
use App\Models\User;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(User::class,6)->create();

        //Set the first created user as manager (role == 2) TODO We have juste one?
        DB::table( 'users' )->where( 'id', 1 )->update(
            [   'role' => 2 ,
                'phone_number' => '213697332045',
                'email' => 'tharwabank002@netcourrier.com'
            ]
        );

        DB::table( 'users' )->where( 'id', 2 )->update(
            [
                'role' => 1 ,
                'phone_number' => '213557332045',
                'email' => 'fatima12@gmail.com'
            ]
        );

        DB::table( 'users' )->where( 'id', 3 )->update(
            [   'role' => 1 ,
                'phone_number' => '213697332045',
                'email' => 'mahfoud10info@gmail.com'
            ]
        );

        DB::table( 'users' )->where( 'id', 4 )->update(
            [   'role' => 1 ,
                'phone_number' => '213697332045',
                'email' => 'en_senouci@esi.dz'
            ]
        );

        DB::table( 'users' )->where( 'id', 5 )->update(
            [   'role' => 0 ,
                'phone_number' => '213697332045',
                'email' => 'ez_taklit@esi.dz'
            ]
        );

        DB::table( 'users' )->where( 'id', 6 )->update(
            [   'role' => 0 ,
                'phone_number' => '213697332045',
                'email' => 'tharwa.client@gmail.com'
            ]
        );
    }
}
