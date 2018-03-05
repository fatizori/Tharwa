<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComptesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comptes', function (Blueprint $table) {
            $table->engine='InnoDB';
            $table->increments('id');

            // Schema declaration
            $table->string('code_banque')->default("THW");
            $table->string('code_monnaie')->default("DZA");
            $table->integer('type')->default(0);
            $table->float('balance');
            $table->integer('statut')->default(0);
            $table->timestamps();
            // Constraints declaration
            $table->integer('id_client')->references('id')->on('users');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comptes');
    }
}
