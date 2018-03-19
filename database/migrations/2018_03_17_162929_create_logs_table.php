<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->engine='InnoDB';
            $table->increments('id');
            // Schema declaration
            $table->string('email_sub');
            $table->string('email_obj');
            $table->text('message');
            $table->string('status');
            $table->string('type');
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
        Schema::dropIfExists('logs');
    }
}
