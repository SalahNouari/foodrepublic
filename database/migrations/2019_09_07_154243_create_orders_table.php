<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('tracking_id');
            $table->unsignedBigInteger('vendor_id');
            $table->integer('duration')->default(0);
            $table->integer('distance')->default(0);
            $table->integer('table_no')->default(0);
            $table->integer('delivery_fee')->default(0);
            $table->integer('total')->default(0);
            $table->integer('grand_total')->default(0);
            $table->integer('service_charge')->default(0);
            $table->integer('change_amount')->default(0);
            $table->integer('pos_charge')->default(0);
            $table->integer('payment_method')->default(0);
            $table->integer('status')->default(0);
            $table->integer('user_status')->default(0);
            $table->integer('delivery_status')->default(0);
            $table->boolean('paid')->default(false);
            $table->boolean('read')->default(false);
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('address_id')->nullable();
            $table->foreign('address_id')->references('id')->on('adresses')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('delivery_id');
            $table->foreign('delivery_id')->references('id')->on('deliveries')->onDelete('cascade')->onUpdate('cascade');

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
}
