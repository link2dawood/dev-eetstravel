<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TourAddTransfer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    Schema::table('tours', function (Blueprint $table) {
		    $table->integer('transfer_id')->nullable()->index()->unsigned();
		    $table->foreign('transfer_id')->references('id')->on('tour_packages')->onDelete('cascade');
	    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
	    Schema::table('tours', function (Blueprint $table) {
	    	$table->dropColumn('transfer_id');
	    });
    }
}
