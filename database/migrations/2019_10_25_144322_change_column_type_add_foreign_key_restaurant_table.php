<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeColumnTypeAddForeignKeyRestaurantTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('restaurants', function (Blueprint $table){
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
        Schema::table('restaurants', function (Blueprint $table){
            $table->dropForeign('restaurants_city_foreign');
        });
        Schema::table('restaurants', function (Blueprint $table){
            $table->string('city')->change();
        });
    }
}
