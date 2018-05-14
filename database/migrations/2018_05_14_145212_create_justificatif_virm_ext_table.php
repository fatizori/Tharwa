<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJustificatifVirmExtTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('justificatif_virm_ext', function (Blueprint $table) {
            $table->engine='InnoDB';
            $table->increments('id');

            // Schema declaration
            $table->string('url_justif');
            $table->integer('status');
            $table->date('date_action_banker')->nullable();
            $table->integer('id_vrm');
            $table->integer('id_banker')->nullable();
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
        Schema::dropIfExists('justificatif_virm_ext');
    }
}
