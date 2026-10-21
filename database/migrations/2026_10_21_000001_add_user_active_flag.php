<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up() {
        if (!Schema::hasColumn('users','is_active')) Schema::table('users',function(Blueprint $table) { $table->boolean('is_active')->default(true); });
    }
    public function down() {
        // Preserve account activation decisions on rollback.
    }
};
