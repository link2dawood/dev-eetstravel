<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ManyServersEmail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    Schema::table('users', function (Blueprint $table) {
		    $table->integer('email_server');
	    });
	    Schema::table('email_folders', function (Blueprint $table) {
		    $table->integer('email_server');
	    });

	    Schema::table('emails', function (Blueprint $table) {
		    $table->integer('email_server');
	    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
	    Schema::table('users', function (Blueprint $table) {
		    $table->dropColumn('email_server');
	    });
	    Schema::table('email_folders', function (Blueprint $table) {
		    $table->dropColumn('email_server');
	    });
	    Schema::table('emails', function (Blueprint $table) {
		    $table->dropColumn('email_server');
	    });
    }
}
