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
            $table->timestamps();
            // Constraints declaration
            $table->integer('id_createur');

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
