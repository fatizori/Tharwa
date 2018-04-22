<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use \Illuminate\Support\Facades\DB;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->engine='InnoDB';
            $table->increments('id');
            // Schema declaration
            $table->string('currency_code')->default('DZD');
            $table->integer('type')->default(1);
            $table->double('balance')->default(0);
            $table->integer('status')->default(0);
            $table->timestamps();
            // Constraints declaration
            $table->integer('id_customer')->references('id')->on('customers')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accounts');
    }
}
