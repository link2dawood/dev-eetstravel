<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->float('price')->nullable();
	        $table->integer('restaurant_id')->nullable()->index()->unsigned();
	        $table->foreign('restaurant_id')->references('id')->on('restaurants')->onDelete('cascade');
	        $table->integer('hotel_id')->nullable()->index()->unsigned();
	        $table->foreign('hotel_id')->references('id')->on('hotels')->onDelete('cascade');
            $table->timestamps();
        });

	    Schema::table('tour_packages', function (Blueprint $table) {
		    $table->integer('menu_id');
	    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
	    Schema::table('tour_packages', function (Blueprint $table) {
		    $table->dropColumn('menu_id');
	    });

        Schema::dropIfExists('menus');
    }
}
