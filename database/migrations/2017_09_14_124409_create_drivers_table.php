<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('phone');
            $table->string('email');
	        $table->integer('transfer_id')->nullable()->index()->unsigned();
	        $table->foreign('transfer_id')->references('id')->on('transfers')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::table('tour_packages', function (Blueprint $table) {
	        $table->integer('driver_id')->nullable()->index()->unsigned();
	        $table->foreign('driver_id')->references('id')->on('drivers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('drivers');

    }
}
