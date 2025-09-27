<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeColumnTypeAddForeignKeyHotelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hotels', function (Blueprint $table){
            $table->unsignedInteger('city')->nullable()->change();
            //add missing foreign Key
            $table->foreign('city')->references('id')->on('cities');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hotels', function (Blueprint $table){
            $table->dropForeign('hotels_city_foreign');
        });
        Schema::table('hotels', function (Blueprint $table){
            $table->string('city')->change();
        });
    }
}
