<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBankersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bankers', function (Blueprint $table) {
            $table->engine='InnoDB';
            $table->increments('id');
            // Schema declaration
            $table->string('name');
            $table->string('firstname');
            $table->string('address');
            $table->string('photo');
            $table->timestamps();
            // Constraints declaration
            $table->integer('id_creator');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bankers');
    }
}
