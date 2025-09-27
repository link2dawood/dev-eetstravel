<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSeasonsPricesRoomTypeHotels extends Migration
{
      public function up()
    {
        Schema::create('seasons_prices_room_type_hotels', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('season_id')->nullable();
			$table->integer('count');
			$table->integer('price');				
			$table->integer('room_type_id');            	
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
       Schema::dropIfExists('seasons_prices_room_type_hotels');
    }
   
}
