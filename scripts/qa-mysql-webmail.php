<?php
// Webmail checks with fake mailbox values. No IMAP/SMTP connections or real sends.
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\SnappyMailController;
if (DB::getDriverName() !== 'mysql') throw new RuntimeException('MySQL required');
$results = [];
$check = function ($name, $fn) use (&$results) {
    try { if ($fn() === false) throw new RuntimeException('Assertion failed'); $results[] = ['check' => $name, 'status' => 'passed']; }
    catch (Throwable $e) { $results[] = ['check' => $name, 'status' => 'failed', 'error' => get_class($e)]; }
};
$before = hash('sha256', serialize(DB::table('users')->orderBy('id')->get()->toArray()));
$check('All stored mailbox passwords are encrypted and flags agree', function () {
    foreach (DB::table('users')->whereNotNull('email_password')->where('email_password', '<>', '')->get() as $row) {
        if (!$row->email_password_encrypted || Crypt::decryptString($row->email_password) === '') return false;
    }
    return true;
});
$check('Runtime data is outside public directory', fn() => is_dir(storage_path('app/snappymail-data/_data_')) && !file_exists(public_path('snappymail/data')));
$controller = new SnappyMailController;
$user = App\User::firstOrFail();
// Process-only attributes; no user record is changed.
$user->email_login = null; $user->email_password = null; Auth::setUser($user);
$check('Missing mailbox redirects to configure without loop', fn() => $controller->sso(Request::create('/email/webmail'))->getTargetUrl() === route('snappymail.configure'));
$check('Direct login uses installed webmail URL', fn() => strpos($controller->direct()->getTargetUrl(), '/snappymail/') !== false);
$user->email_login = 'qa-webmail@example.test'; $user->email_password = 'QA-Webmail-Local-Only';
$check('SSO creates a local one-time token without credentials in URL', function () use ($controller) {
    $url = $controller->sso(Request::create('/email/webmail'))->getTargetUrl();
    return strpos($url, '/?Sso&hash=') !== false && strpos($url, 'QA-Webmail') === false && strpos($url, 'qa-webmail') === false;
});
$check('User serialization excludes mailbox password', fn() => !array_key_exists('email_password', $user->toArray()));
$check('Missing legacy IMAP class is detected without connecting', fn() => app(App\Http\Controllers\EmailController::class)->setClient() === false);
$check('Saved users remain byte-for-byte unchanged', fn() => $before === hash('sha256', serialize(DB::table('users')->orderBy('id')->get()->toArray())));
$summary = ['date' => date(DATE_ATOM), 'results' => $results];
file_put_contents(storage_path('app/qa-mysql-webmail-results.json'), json_encode($summary, JSON_PRETTY_PRINT));
foreach ($results as $r) echo strtoupper($r['status']) . ' ' . $r['check'] . (isset($r['error']) ? ': ' . $r['error'] : '') . PHP_EOL;
exit(count(array_filter($results, fn($r) => $r['status'] === 'failed')) ? 1 : 0);
