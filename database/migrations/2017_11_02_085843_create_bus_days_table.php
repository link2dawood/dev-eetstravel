<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBusDaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bus_days', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('tour_id')->nullable();
            $table->integer('transfer_id')->nullable();
            $table->integer('tour_package_id')->nullable();
            $table->date('date');
            $table->string('status_id')->nullable();
            $table->integer('bus_id')->nullable();
            $table->string('name_trip')->nullable();
            $table->string('country_trip')->nullable();
            $table->integer('city_trip')->nullable();
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
        Schema::dropIfExists('bus_days');
    }
}
