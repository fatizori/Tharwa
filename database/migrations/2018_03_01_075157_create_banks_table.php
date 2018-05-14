<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use \Illuminate\Support\Facades\DB;

class CreateBanksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banks', function (Blueprint $table) {
            $table->engine='InnoDB';
            $table->string('id',3);
            $table->primary('id');
            // Schema declaration
            $table->string('email')->unique();
            $table->string('address');
            $table->string('nom');
            $table->string('social_reason');
            $table->boolean('status')->default(1);
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
        Schema::dropIfExists('banks');
    }
}
