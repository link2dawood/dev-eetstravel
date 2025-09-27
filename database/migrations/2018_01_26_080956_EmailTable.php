<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EmailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    Schema::create('emails', function (Blueprint $table) {
		    $table->increments('id');
		    $table->integer('user_id')->nullable();
		    $table->integer('message_id');
		    $table->string('folder');
		    $table->string('user_login');
		    $table->string('subject')->nullable();
		    $table->text('content');
		    $table->text('attachments');
		    $table->text('body_text')->nullable();
		    $table->text('body_html')->nullable();
		    $table->string('cc')->nullable();
		    $table->dateTime('date')->nullable();
		    $table->string('from');
		    $table->string('headers')->nullable();
		    $table->string('mail_id')->nullable();
		    $table->integer('number');
		    $table->integer('size')->nullable();
		    $table->string('to')->nullable();
		    $table->boolean('has_attachments')->nullable();
		    $table->boolean('is_answered')->nullable();
		    $table->boolean('is_deleted')->nullable();
		    $table->boolean('is_draft')->nullable();
		    $table->boolean('is_seen')->nullable();
		    $table->integer('message_number');
		    $table->timestamps();
	    });
	    Schema::create('email_folders', function (Blueprint $table) {
		    $table->increments('id');
		    $table->string('name');
		    $table->string('user_login');
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
	    Schema::drop('emails');
	    Schema::drop('email_folders');
    }
}
