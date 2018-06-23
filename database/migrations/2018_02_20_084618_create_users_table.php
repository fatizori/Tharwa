<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use \Illuminate\Support\Facades\DB;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->engine='InnoDB';
            $table->increments('id');

            // Schema declaration
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone_number',50);
            $table->string('fcm_token',200)->nullable();
            $table->integer('role')->default(0);
            $table->string('nonce_auth',4);
            $table->dateTime('expire_date_nonce');
            $table->timestamps();
            // Constraints declaration
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}

