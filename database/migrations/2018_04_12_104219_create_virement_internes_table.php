<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVirementInternesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('virement_internes', function (Blueprint $table) {
            $table->engine='InnoDB';
            $table->increments('id');

            // Schema declaration
            $table->integer('num_acc_sender');
            $table->string('code_bnk_sender');
            $table->string('code_curr_sender');
            $table->integer('num_acc_receiver');
            $table->string('code_bnk_receiver');
            $table->string('code_curr_receiver');
            $table->dateTime('date_virement');
            $table->decimal('montant_virement');
            $table->integer('status');
            $table->integer('type');
            $table->integer('id_commission');
            $table->decimal('montant_commission');
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
        Schema::dropIfExists('virement_internes');
    }
}
