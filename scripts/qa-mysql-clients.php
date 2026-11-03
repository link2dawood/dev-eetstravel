<?php
// Local MySQL client workflows; all fixture writes are rolled back.
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Client;
use App\Http\Requests\StoreClientRequest;
use App\Http\Controllers\ClientController;
if (DB::getDriverName() !== 'mysql') throw new RuntimeException('MySQL required');
$results = [];
$check = function ($name, $fn) use (&$results) {
    try { if ($fn() === false) throw new RuntimeException('Assertion failed'); $results[] = ['check' => $name, 'status' => 'passed']; }
    catch (Throwable $e) { $results[] = ['check' => $name, 'status' => 'failed', 'error' => $e->getMessage()]; }
};
$tables = ['clients', 'client_contacts', 'tours', 'client_invoices', 'files', 'comments'];
foreach ($tables as $table) {
    $engine = DB::selectOne('SELECT ENGINE AS engine FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?', [$table]);
    if (!$engine || $engine->engine !== 'InnoDB') throw new RuntimeException('Transactional table required: ' . $table);
}
$snapshot = fn() => collect($tables)->mapWithKeys(fn($t) => [$t => hash('sha256', serialize(DB::table($t)->orderBy('id')->get()->toArray()))])->all();
$before = $snapshot();
DB::beginTransaction();
try {
    $controller = app(ClientController::class);
    $city = DB::table('cities')->join('countries', 'countries.alias', '=', 'cities.country')->select('cities.id', 'cities.country')->first();
    if (!$city) throw new RuntimeException('Existing city/country fixture required');
    $tag = 'qa-client-' . bin2hex(random_bytes(5));
    $data = ['name' => "Müller & Söhne $tag", 'country' => $city->country, 'city' => (string)$city->id, 'address' => '123 QA Example Street', 'work_phone' => '+49 12345678', 'work_email' => "$tag@example.test", 'contact_email' => "$tag-portal@example.test", 'password' => 'QA-Portal-Local-Only', 'id' => 2147483647, 'created_at' => '2000-01-01', 'dep_date' => '2000-01-01'];
    $make = function ($input) use ($app) {
        $request = StoreClientRequest::create('/clients', 'POST', $input); $request->setContainer($app)->setRedirector($app['redirect']); $request->headers->set('Accept', 'application/json'); $request->validateResolved(); return $request;
    };
    $check('CL9 accented company creates with validated fields', fn() => $controller->store($make($data))->getStatusCode() === 200);
    $client = Client::where('work_email', $data['work_email'])->firstOrFail();
    $check('CL4 forged primary key/date fields ignored', fn() => $client->id != 2147483647 && !$client->dep_date && $client->created_at->year !== 2000);
    $check('CL8 new password is a verified one-way hash', fn() => Hash::check($data['password'], $client->password) && password_get_info($client->password)['algoName'] === 'argon2id');
    $check('CL8 password excluded from serialization', fn() => !array_key_exists('password', $client->toArray()));
    $check('CL9 duplicate email produces a field error', function () use ($make, $data) {
        try { $make($data); }
        catch (Illuminate\Validation\ValidationException $e) { return isset($e->errors()['work_email']); }
        return false;
    });
    DB::table('client_contacts')->insert(['client_id' => $client->id, 'full_name' => 'QA Contact', 'email' => "$tag-contact@example.test"]);
    $check('Contact helper renders this existing client', fn() => strpos($controller->getClientContacts(Request::create('/', 'GET', ['clientId' => $client->id, 'itemCount' => 0]))['content'], 'QA Contact') !== false);
    $check('CL2 unknown contact parent is not-found', function () use ($controller) {
        try { $controller->getClientContacts(Request::create('/', 'GET', ['clientId' => 2147483647])); }
        catch (Illuminate\Database\Eloquent\ModelNotFoundException $e) { return true; }
        return false;
    });
    $login = Request::create('/', 'POST', ['contact_email' => $data['contact_email'], 'password' => $data['password']]); $login->setLaravelSession(app('session.store'));
    $check('CL8 portal login validates the new hash', fn() => (new App\Http\Controllers\TMSClient\LoginController)->Clientauth($login)->getTargetUrl() === route('TMS-Client.home') && session('CLIENT_ID') == $client->id);
    app()->instance('request', Request::create('/', 'POST', [], [], [], ['HTTP_ACCEPT' => 'application/json']));
    foreach (['tours', 'client_invoices'] as $table) {
        $source = DB::table($table)->first(); if (!$source) throw new RuntimeException('Reference fixture required: ' . $table);
        DB::table($table)->where('id', $source->id)->update(['client_id' => $client->id]);
        $check('CL6 web delete blocked by ' . $table, fn() => $controller->destroy($client->id)->getStatusCode() === 422 && Client::find($client->id) !== null);
        $check('CL6 API delete blocked by ' . $table, fn() => app(App\Http\Controllers\Api\ClientApiController::class)->destroy($client)->getStatusCode() === 422 && Client::find($client->id) !== null);
        DB::table($table)->where('id', $source->id)->update(['client_id' => $source->client_id]);
    }
    $check('Unused client soft-delete preserves contact data', fn() => $controller->destroy($client->id)->getStatusCode() === 200 && Client::find($client->id) === null && DB::table('client_contacts')->where('client_id', $client->id)->count() === 1);
    $check('CL8 all stored nonempty portal passwords are hashes', function () {
        foreach (DB::table('clients')->whereNotNull('password')->where('password', '<>', '')->pluck('password') as $value) if (!password_get_info($value)['algo']) return false;
        return true;
    });
} catch (Throwable $e) { $results[] = ['check' => 'Workflow setup', 'status' => 'failed', 'error' => $e->getMessage()]; }
finally { while (DB::transactionLevel()) DB::rollBack(); app('session.store')->forget(['CLIENT_ID', 'CLIENT_LOGIN']); }
$check('All client/contact/reference/file/comment values preserved after rollback', fn() => $before === $snapshot());
file_put_contents(storage_path('app/qa-mysql-clients-results.json'), json_encode(['date' => date(DATE_ATOM), 'results' => $results], JSON_PRETTY_PRINT));
foreach ($results as $r) echo strtoupper($r['status']) . ' ' . $r['check'] . (isset($r['error']) ? ': ' . $r['error'] : '') . PHP_EOL;
exit(count(array_filter($results, fn($r) => $r['status'] === 'failed')) ? 1 : 0);
