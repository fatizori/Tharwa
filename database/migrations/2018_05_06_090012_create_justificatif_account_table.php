<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJustificatifAccountTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('justificatif_account', function (Blueprint $table) {
            $table->engine='InnoDB';
            $table->increments('id');

            // Schema declaration
            $table->string('object',190);
            $table->text('justification');
            $table->integer('id_account')->unsigned();
            $table->integer('id_banker')->unsigned();
            $table->tinyInteger('status')->default(0);
            $table->foreign('id_account')->references('id')->on('accounts');
            $table->foreign('id_banker')->references('id')->on('bankers');
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
        Schema::dropIfExists('justificatif_account');
    }
}
