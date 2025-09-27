<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeColumnTypeAddForeignKeyToursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tours', function (Blueprint $table){
            $table->unsignedInteger('city_begin')->nullable()->change();
            $table->unsignedInteger('city_end')->nullable()->change();

            //add missing foreign Key
            $table->foreign('city_begin')->references('id')->on('cities');
            $table->foreign('city_end')->references('id')->on('cities');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tours', function (Blueprint $table){
            $table->dropForeign('tours_city_begin_foreign');
            $table->dropForeign('tours_city_end_foreign');

        });
        Schema::table('tours', function (Blueprint $table){
            $table->integer('city_begin')->change();
            $table->integer('city_end')->change();
        });
    }
}
