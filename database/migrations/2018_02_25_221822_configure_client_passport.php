<?php

use Illuminate\Database\Migrations\Migration;
use \Illuminate\Support\Facades\Artisan;
use \Illuminate\Support\Facades\DB;

class ConfigureClientPassport extends Migration
{
    /**
     * This migration is for
     *
     * @return void
     */
    public function up()
    {
        /*
           * This command will create the encryption keys needed to generate secure access tokens.
           * In addition, the command will create "personal access" and "password grant"
           * clients which will be used to generate access tokens
            */
            Artisan::call( 'passport:install', array('-n' => true) );

            // Set Password Grant Client secret to known key
            DB::table( 'oauth_clients' )->where( 'password_client', 1 )->update(
                ['secret' => env('CLIENT_SECRET')]
            );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
