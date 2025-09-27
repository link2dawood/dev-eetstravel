<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHotelAgreementsTable extends Migration
{
	/**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hotel_agreements', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('hotel_id')->nullable();
			$table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('name');	
            $table->text('description')->nullable();			
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
        Schema::dropIfExists('hotel_agreements');
    }
}
