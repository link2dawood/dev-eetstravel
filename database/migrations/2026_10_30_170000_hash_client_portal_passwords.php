<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class HashClientPortalPasswords extends Migration
{
    public function up()
    {
        DB::transaction(function () {
            foreach (DB::table('clients')->whereNotNull('password')->where('password', '<>', '')->lockForUpdate()->get(['id', 'password']) as $row) {
                if (password_get_info($row->password)['algo']) continue;
                $plain = \App\Services\ClientPortalPassword::legacyValue($row->password);
                DB::table('clients')->where('id', $row->id)->update(['password' => \App\Services\ClientPortalPassword::hash($plain)]);
            }
        });
    }
    public function down() { /* One-way hashes must never be replaced with plaintext. */ }
}
