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
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('invoice_id')->length('55');
            $table->integer('amount');
            $table->integer('discount');
            $table->integer('shipping_charge');
            $table->string('paid')->length(11)->default(0);
            $table->string('due')->length(11)->default(0);
            $table->integer('customer_id');
            $table->string('customer_ip')->length(55)->nullable();
            $table->string('admin_note')->nullable();
            $table->string('note')->nullable();
            $table->string('user_id')->nullable();
            $table->string('order_status')->length('55');
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
        Schema::dropIfExists('orders');
    }
};
