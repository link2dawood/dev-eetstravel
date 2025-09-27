<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateToursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tours', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->longText('overview')->nullable();
            $table->longText('remark')->nullable();
            $table->date('departure_date')->nullable();
            $table->date('retirement_date')->nullable();
            $table->string('pax')->nullable();
            $table->string('pax_free')->nullable();
            $table->string('rooms')->nullable();
            $table->string('country_begin')->nullable();
            $table->integer('city_begin')->nullable();
            $table->string('country_end')->nullable();
            $table->integer('city_end')->nullable();
            $table->date('invoice')->nullable();
            $table->date('ga')->nullable();
            $table->integer('status')->nullable();
            $table->integer('author')->nullable()->unsigned();
            $table->integer('assigned_user')->nullable()->unsigned();
            $table->foreign('author')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('assigned_user')->references('id')->on('users')->onDelete('cascade');
            $table->string('itinerary_tl')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tours');
    }
}
