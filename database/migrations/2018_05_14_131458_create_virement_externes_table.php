<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVirementExternesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('virement_externes', function (Blueprint $table) {
            $table->engine='InnoDB';
            $table->increments('id');

            // Schema declaration
            $table->integer('num_acc');
            $table->string('code_bnk');
            $table->string('code_curr');
            $table->integer('num_acc_ext');
            $table->string('code_bnk_ext');
            $table->string('code_curr_ext');
            $table->decimal('amount_vir');
            $table->boolean('sens');
            $table->integer('status');
            $table->string('url_xml');
            $table->string('id_commission');
            $table->decimal('amount_commission');
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
        Schema::dropIfExists('virement_externes');
    }
}
