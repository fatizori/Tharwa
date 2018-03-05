<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBanquiersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banquiers', function (Blueprint $table) {
            $table->engine='InnoDB';
            $table->increments('id');

            // Schema declaration
            $table->string('nom');
            $table->string('prenom');
            $table->string('adresse');
            $table->string('photo');
            $table->integer('id_createur');
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
         Schema::dropIfExists('banquiers');
    }
}
