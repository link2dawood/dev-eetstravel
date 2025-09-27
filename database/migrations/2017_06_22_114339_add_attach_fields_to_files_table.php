<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAttachFieldsToFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('files', function (Blueprint $table) {
            $table->string('attach_file_name')->nullable();
            $table->integer('attach_file_size')->nullable()->after('attach_file_name');
            $table->string('attach_content_type')->nullable()->after('attach_file_size');
            $table->timestamp('attach_updated_at')->nullable()->after('attach_content_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('files', function (Blueprint $table) {
            $table->dropColumn('attach_file_name');
            $table->dropColumn('attach_file_size');
            $table->dropColumn('attach_content_type');
            $table->dropColumn('attach_updated_at');
        });
    }
}
