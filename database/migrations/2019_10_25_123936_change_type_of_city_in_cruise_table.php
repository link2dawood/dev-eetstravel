<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTypeOfCityInCruiseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cruises', function (Blueprint $table){
            $table->unsignedInteger('city_to')->change();
            $table->unsignedInteger('city_from')->change();
            //add missing foreign Key
            $table->foreign('city_to')->references('id')->on('cities');
            $table->foreign('city_from')->references('id')->on('cities');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cruises', function (Blueprint $table) {
            $table->dropForeign('cruises_city_to_foreign');
            $table->dropForeign('cruises_city_from_foreign');
        });
        Schema::table('cruises', function (Blueprint $table){
            $table->string('city_to')->change();
            $table->string('city_from')->change();
        });
    }
}
