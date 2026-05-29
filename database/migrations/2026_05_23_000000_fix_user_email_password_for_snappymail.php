<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixUserEmailPasswordForSnappymail extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'email_password_encrypted')) {
                $table->boolean('email_password_encrypted')->default(false)->after('email_password');
            }
        });

        // Laravel's Crypt::encryptString output is typically ~250+ chars; varchar(191) silently truncates it.
        Schema::table('users', function (Blueprint $table) {
            $table->text('email_password')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'email_password_encrypted')) {
                $table->dropColumn('email_password_encrypted');
            }
            $table->string('email_password', 191)->nullable()->change();
        });
    }
}
