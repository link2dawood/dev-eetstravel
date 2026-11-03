<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;
if (DB::getDriverName() !== 'mysql') throw new RuntimeException('MySQL required');
$before = DB::table('clients')->orderBy('id')->get()->toArray();
$passwords = []; $plaintext = 0; $encrypted = 0;
foreach ($before as $row) {
    if ($row->password !== null && $row->password !== '' && !password_get_info($row->password)['algo']) {
        $passwords[$row->id] = App\Services\ClientPortalPassword::legacyValue($row->password);
        try { Illuminate\Support\Facades\Crypt::decryptString($row->password); $encrypted++; }
        catch (Throwable $e) { $plaintext++; }
    }
}
$exit = Artisan::call('migrate', ['--path' => 'database/migrations/2026_10_30_170000_hash_client_portal_passwords.php', '--force' => true]);
echo Artisan::output();
if ($exit) throw new RuntimeException('Password migration failed');
$after = DB::table('clients')->orderBy('id')->get()->toArray();
foreach ($after as $row) {
    if (isset($passwords[$row->id]) && !Hash::check($passwords[$row->id], $row->password)) throw new RuntimeException('Portal password preservation check failed');
}
foreach ([$before, $after] as $rows) foreach ($rows as $row) unset($row->password);
if (serialize($before) !== serialize($after)) throw new RuntimeException('Unexpected non-password client data change');
echo "PASS: $plaintext plaintext and $encrypted encrypted portal passwords replaced with one-way hashes. Passwords still match; all other client values preserved. No credentials exported." . PHP_EOL;
