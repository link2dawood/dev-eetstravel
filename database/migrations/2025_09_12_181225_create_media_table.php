<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('media', function (Blueprint $table) {
            // Add any missing columns
            if (!Schema::hasColumn('media', 'file_name')) {
                $table->string('file_name')->nullable();
            }
            if (!Schema::hasColumn('media', 'file_path')) {
                $table->string('file_path')->nullable();
            }
            if (!Schema::hasColumn('media', 'mime_type')) {
                $table->string('mime_type')->nullable();
            }
            if (!Schema::hasColumn('media', 'size')) {
                $table->integer('size')->nullable();
            }
            if (!Schema::hasColumn('media', 'original_name')) {
                $table->string('original_name')->nullable();
            }
            if (!Schema::hasColumn('media', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable();
            }

            // Optional: Add foreign key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('media', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['file_name', 'file_path', 'mime_type', 'size', 'original_name', 'user_id']);
        });
    }
};