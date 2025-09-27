<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAvatarFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar_file_name')->nullable();
            $table->integer('avatar_file_size')->nullable()->after('avatar_file_name');
            $table->string('avatar_content_type')->nullable()->after('avatar_file_size');
            $table->timestamp('avatar_updated_at')->nullable()->after('avatar_content_type');
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
            $table->dropColumn('avatar_file_name');
            $table->dropColumn('avatar_file_size');
            $table->dropColumn('avatar_content_type');
            $table->dropColumn('avatar_updated_at');
        });
    }
}
