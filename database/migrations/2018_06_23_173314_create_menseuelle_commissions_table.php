<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenseuelleCommissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mensuelle_commissions', function (Blueprint $table) {
            $table->engine='InnoDB';
            $table->increments('id');
            // Schema declaration
            $table->string('type');
            $table->integer('id_account');
            $table->float('amount');
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
        Schema::dropIfExists('mensuelle_commissions');
    }
}
