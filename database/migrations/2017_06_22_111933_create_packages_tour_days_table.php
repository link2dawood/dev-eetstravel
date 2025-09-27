<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackagesTourDaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packages_tour_days', function (Blueprint $table) {
            $table->increments('id')->unique()->index()->unsigned();
			$table->integer('tour_package_id')->unsigned()->index();
			$table->foreign('tour_package_id')->references('id')->on('tour_packages')->onDelete('cascade');
			$table->integer('tour_day_id')->unsigned()->index();
			$table->foreign('tour_day_id')->references('id')->on('tour_days')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('packages_tour_days');
    }
}
