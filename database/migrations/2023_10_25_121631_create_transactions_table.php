<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->integer('ref_id')->nullable();
            $table->string('user')->nullable();
            $table->integer('user_id')->nullable();
            $table->string('type');
            $table->string('amount');
            $table->string('method');
            $table->string('sender')->nullable();
            $table->string('trx_id')->nullable();
            $table->string('carrier')->nullable();
            $table->string('description')->nullable();
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
        Schema::dropIfExists('transactions');
    }
};
