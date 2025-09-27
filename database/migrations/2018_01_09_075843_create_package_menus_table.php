<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackageMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('package_menus', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('tour_package_id');
            $table->integer('count');
            $table->integer('menu_id');
            $table->timestamps();
        });

        Schema::table('tour_packages', function (Blueprint $table) {
        	$table->dropColumn('menu_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('package_menus');
    }
}
