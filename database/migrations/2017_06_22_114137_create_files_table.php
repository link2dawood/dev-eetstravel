<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('flight_id')->nullable();
            $table->integer('hotel_id')->nullable();
            $table->integer('restaurant_id')->nullable();
            $table->integer('event_id')->nullable();
            $table->integer('guide_id')->nullable();
            $table->integer('transfer_id')->nullable();
            $table->integer('cruises_id')->nullable();
            $table->integer('tour_id')->nullable();
            $table->integer('comment_id')->nullable();
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
        Schema::dropIfExists('files');
    }
}
