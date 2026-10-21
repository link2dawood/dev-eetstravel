<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up() {
        if (!Schema::hasTable('media')) {
            Schema::create('media', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->nullableMorphs('model');
                $table->uuid('uuid')->nullable()->unique();
                $table->string('collection_name')->default('default');
                $table->string('name')->default('');
                $table->string('file_name')->nullable();
                $table->string('file_path')->nullable();
                $table->string('mime_type')->nullable();
                $table->unsignedBigInteger('size')->nullable();
                $table->string('original_name')->nullable();
                $table->unsignedInteger('user_id')->nullable();
                $table->string('disk')->default('public');
                $table->string('conversions_disk')->nullable();
                foreach (['manipulations','custom_properties','generated_conversions','responsive_images'] as $column) $table->json($column)->nullable();
                $table->unsignedInteger('order_column')->nullable()->index();
                $table->timestamps();
            });
        }
        foreach (['file_name','file_path','mime_type','original_name'] as $column) {
            if (!Schema::hasColumn('media',$column)) Schema::table('media',function(Blueprint $table) use($column) { $table->string($column)->nullable(); });
        }
        foreach (['size','user_id'] as $column) {
            if (!Schema::hasColumn('media',$column)) Schema::table('media',function(Blueprint $table) use($column) { $table->unsignedInteger($column)->nullable(); });
        }
    }
    public function down() {
        // Preserve existing uploaded-file metadata when rolling back.
    }
};
