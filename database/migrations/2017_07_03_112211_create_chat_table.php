<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChatTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('chats', function (Blueprint $table) {
			$table->increments('id');
			$table->string('title');
			$table->text('description')->nullable();
			$table->integer('type');
			$table->integer('author');
			$table->timestamps();
		});

		Schema::create('chat_user', function (Blueprint $table) {
			$table->increments('id')->unique()->index()->unsigned();
			$table->integer('chat_id')->unsigned()->index();
			$table->foreign('chat_id')->references('id')->on('chats')->onDelete('cascade');
			$table->integer('user_id')->unsigned()->index();
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
		});

		Schema::create('chat_messages', function (Blueprint $table) {
			$table->increments('id')->unique()->index()->unsigned();
			$table->integer('chat_id')->unsigned()->index();
			$table->foreign('chat_id')->references('id')->on('chats')->onDelete('cascade');
			$table->integer('user_id')->unsigned()->index();
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
			$table->text('message');
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
		Schema::dropIfExists('chat_user');
		Schema::dropIfExists('chat_messages');
		Schema::dropIfExists('chats');
	}
}
