<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountsManagementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts_management', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            // Schema declaration
            $table->integer('banker_id')->unsigned();
            $table->integer('account_id')->unsigned();
            $table->tinyInteger('operation')->default(0);
            $table->string('object')->nullable();
            $table->string('justification');
            $table->timestamp('created_at')->default(\Illuminate\Support\Facades\DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(\Illuminate\Support\Facades\DB::raw('CURRENT_TIMESTAMP'))->nullable();
            // Constraints declaration
            $table->foreign('banker_id')->references('id')->on('bankers');
            $table->foreign('account_id')->references('id')->on('accounts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accounts_management');
    }
}
