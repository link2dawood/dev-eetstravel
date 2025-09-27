<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->increments('id');
	        $table->string('name');
	        $table->string('note');
	        $table->string('rate');
	        $table->integer('tour_id')->nullable()->index()->unsigned();
            $table->timestamps();
        });
	    Schema::create('quotation_rows', function (Blueprint $table) {
		    $table->increments('id');
		    $table->integer('quotation_id')->nullable()->index()->unsigned();
		    $table->foreign('quotation_id')->references('id')->on('quotations')->onDelete('cascade');
		    $table->timestamps();
	    });
	    Schema::create('quotation_values', function (Blueprint $table) {
		    $table->increments('id');
		    $table->string('key');
		    $table->string('value');
		    $table->integer('quotation_row_id')->nullable()->index()->unsigned();
		    $table->foreign('quotation_row_id')->references('id')->on('quotation_rows')->onDelete('cascade');
		    $table->timestamps();
	    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
	    Schema::dropIfExists('quotation_values');
	    Schema::dropIfExists('quotation_rows');
	    Schema::dropIfExists('quotations');
    }
}
