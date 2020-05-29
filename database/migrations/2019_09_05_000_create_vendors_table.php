<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVendorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->boolean('verified')->default(false);
            $table->boolean('card_on_delivery')->default(false);
            $table->boolean('cash_on_delivery')->default(false);
            $table->longText('bio')->nullable();

            $table->string('account_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('address');
            $table->string('instagram')->nullable();
            $table->string('twitter')->nullable();
            $table->integer('minimum_order')->default(1000)->nullable();
            $table->integer('pos_charge')->default(0)->nullable();
            $table->string('phone');
            $table->string('token')->nullable();
            $table->string('branch')->nullable();
            $table->string('lat');
            $table->string('city');
            $table->string('lng');
            $table->string('place_id')->nullable();
            $table->string('image')->nullable()->default('https://cdn2.iconfinder.com/data/icons/ui-1/60/05-512.png');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('vendors');
    }
}
