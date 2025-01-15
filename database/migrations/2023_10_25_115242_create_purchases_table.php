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
        Schema::create('purchases', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('invoice_id');
            $table->integer('supplier_id');
            $table->string('amount')->length('11');
            $table->string('discount')->length('11');
            $table->string('paid')->length('11');
            $table->string('due')->length('11');
            $table->string('quantity')->length('11');
            $table->string('status')->length('20');
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
        Schema::dropIfExists('purchases');
    }
};
