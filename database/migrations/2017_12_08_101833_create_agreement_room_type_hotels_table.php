<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAgreementRoomTypeHotelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hotel_agreements_room_type_hotels', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('agreement_id')->nullable();
			$table->integer('count');	
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
       Schema::dropIfExists('hotel_agreements_room_type_hotels');
    }
}
