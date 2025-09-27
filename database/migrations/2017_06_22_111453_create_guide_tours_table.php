<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGuideToursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guide_tour', function (Blueprint $table) {
            $table->increments('id')->unique()->index()->unsigned();
			$table->integer('guide_id')->unsigned()->index();
			$table->foreign('guide_id')->references('id')->on('guides')->onDelete('cascade');
			$table->integer('tour_id')->unsigned()->index();
			$table->foreign('tour_id')->references('id')->on('tours')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('guide_tour');
    }
}
