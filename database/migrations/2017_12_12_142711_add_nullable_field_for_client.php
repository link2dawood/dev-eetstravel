<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNullableFieldForClient extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('address')->nullable()->change();
            $table->string('work_phone')->nullable()->change();
            $table->string('contact_phone')->nullable()->change();
            $table->string('work_email')->nullable()->change();
            $table->string('contact_email')->nullable()->change();
            $table->string('work_fax')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('address')->change();
            $table->string('work_phone')->change();
            $table->string('contact_phone')->change();
            $table->string('work_email')->change();
            $table->string('contact_email')->change();
            $table->string('work_fax')->change();
        });
    }
}
