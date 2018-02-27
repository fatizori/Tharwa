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
        factory(User::class,5)->create();

        //Set the first created user as manager (role == 2) TODO We have juste one?

        DB::table( 'users' )->where( 'id', 1 )->update(
            ['role' => 2]
        );

    }
}
