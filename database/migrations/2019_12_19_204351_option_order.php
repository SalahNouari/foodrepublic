<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OptionOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('option_order', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('option_id');
            $table->string('qty');
            $table->string('type');
            $table->integer('tracking_id');
            $table->unsignedBigInteger('order_id');
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
        //
    }
}
