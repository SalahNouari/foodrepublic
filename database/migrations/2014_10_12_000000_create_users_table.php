<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('first_name')->nullable();
            $table->string('surname')->nullable();
            $table->string('fcm')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('phone')->unique()->nullable();
            $table->bigInteger('points')->nullable()->default(0);
            $table->bigInteger('orders')->nullable()->default(0);
            $table->bigInteger('wallet')->nullable()->default(0);
            $table->string('verification_code')->default(null)->nullable();
            $table->string('verification_type');
            $table->integer('city');
            $table->integer('area');
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('image')->default('https://ih0.redbubble.net/image.210602545.3386/flat,1000x1000,075,f.u1.jpg');
            $table->string('role')->default('user');
            $table->rememberToken();
            $table->timestamps();

            $table->unsignedBigInteger('state_id');
            $table->foreign('state_id')->references('id')->on('states')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('area_id');
            $table->foreign('area_id')->references('id')->on('areas')->onDelete('cascade')->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
