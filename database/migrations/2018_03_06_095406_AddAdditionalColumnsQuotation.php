<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdditionalColumnsQuotation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    Schema::table('quotations', function (Blueprint $table) {
		    $table->text('additional_columns');
		    $table->text('additional_column_values');
	    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
	    Schema::table('quotations', function (Blueprint $table) {
		    $table->dropColumn('additional_columns');
		    $table->dropColumn('additional_column_values');
	    });
    }
}
