<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Crypt;

class SecureMailboxCredentials extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('users', 'email_password_encrypted')) Schema::table('users', function ($t) { $t->boolean('email_password_encrypted')->default(false); });
        DB::transaction(function () {
            foreach (DB::table('users')->whereNotNull('email_password')->where('email_password', '<>', '')->get(['id', 'email_password', 'email_password_encrypted']) as $row) {
                $user = new \App\User; $user->setRawAttributes((array)$row);
                $plain = app(\App\Services\MailboxCredentials::class)->password($user);
                try { Crypt::decryptString($row->email_password); $encrypted = $row->email_password; }
                catch (\Throwable $e) { $encrypted = Crypt::encryptString($plain); }
                DB::table('users')->where('id', $row->id)->update(['email_password' => $encrypted, 'email_password_encrypted' => true]);
            }
        });
    }
    public function down()
    {
        // Never restore plaintext mailbox credentials.
    }
}
