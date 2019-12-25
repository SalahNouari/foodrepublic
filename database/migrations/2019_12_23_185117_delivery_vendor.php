<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeliveryVendor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_vendor', function (Blueprint $table) {

        $table->bigIncrements('id');
        $table->unsignedBigInteger('vendor_id');
        $table->unsignedBigInteger('delivery_id');
        $table->string('distance')->nullable();
        $table->string('duration')->nullable();
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
