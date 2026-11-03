<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Artisan;
if (DB::getDriverName() !== 'mysql') throw new RuntimeException('MySQL required');
$fingerprints = []; $plaintext = 0; $missingFlags = 0;
foreach (DB::table('users')->whereNotNull('email_password')->where('email_password', '<>', '')->get() as $row) {
    $user = new App\User; $user->setRawAttributes((array)$row);
    $password = app(App\Services\MailboxCredentials::class)->password($user);
    $fingerprints[$row->id] = hash_hmac('sha256', $password, config('app.key'));
    try { Crypt::decryptString($row->email_password); }
    catch (Throwable $e) { $plaintext++; }
    if (!$row->email_password_encrypted) $missingFlags++;
}
$exit = Artisan::call('migrate', ['--path' => 'database/migrations/2026_10_30_150000_secure_mailbox_credentials.php', '--force' => true]);
echo Artisan::output();
if ($exit) throw new RuntimeException('Credential migration failed');
foreach ($fingerprints as $id => $fingerprint) {
    $row = DB::table('users')->where('id', $id)->first();
    if (!$row->email_password_encrypted || !hash_equals($fingerprint, hash_hmac('sha256', Crypt::decryptString($row->email_password), config('app.key')))) throw new RuntimeException('Credential preservation check failed');
}
echo "PASS: $plaintext plaintext mailbox passwords encrypted; $missingFlags missing flags repaired; credential values preserved. No passwords printed or exported." . PHP_EOL;
