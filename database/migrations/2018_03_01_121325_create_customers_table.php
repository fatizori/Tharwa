<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use \Illuminate\Support\Facades\DB;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->engine='InnoDB';
            $table->integer('id',0,1);
            $table->foreign('id')->references('id')->on('users');

            // Schema declaration
            $table->string('name');
            $table->string('address');
            $table->string('function');
            $table->string('wilaya');
            $table->string('commune');
            $table->string('photo')->nullable();
            $table->integer('type');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customers');
    }
}
