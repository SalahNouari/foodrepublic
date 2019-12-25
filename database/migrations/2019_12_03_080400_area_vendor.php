<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AreaVendor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('areas_vendor', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('areas_id');
            $table->unsignedBigInteger('vendor_id');
            $table->integer('fee')->nullable();
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
