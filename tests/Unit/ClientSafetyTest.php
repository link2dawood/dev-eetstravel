<?php
namespace Tests\Unit;

use App\Client;
use App\Http\Controllers\ClientController;
use App\Http\Requests\StoreClientRequest;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ClientSafetyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']); DB::purge('sqlite');
        Schema::create('clients', function ($t) {
            $t->increments('id'); foreach (['name', 'address', 'country', 'city', 'account_no', 'company_address', 'invoice_address', 'work_phone', 'contact_phone', 'work_email', 'contact_email', 'work_fax', 'password', 'dep_date'] as $f) $t->string($f)->nullable();
            $t->timestamps(); $t->softDeletes();
        });
        Schema::create('countries', function ($t) { $t->increments('id'); $t->string('alias'); $t->string('name'); });
        Schema::create('cities', function ($t) { $t->increments('id'); $t->string('name'); $t->string('country'); });
        DB::table('countries')->insert(['alias' => 'DE', 'name' => 'Germany']); DB::table('cities')->insert(['id' => 1, 'name' => 'Berlin', 'country' => 'DE']);
        foreach (['tours', 'client_invoices'] as $table) Schema::create($table, function ($t) { $t->increments('id'); $t->integer('client_id'); $t->softDeletes(); });
        Schema::create('client_contacts', function ($t) { $t->increments('id'); $t->integer('client_id'); foreach (['full_name', 'mobile_phone', 'work_phone', 'email'] as $field) $t->string($field)->nullable(); $t->timestamps(); });
        Schema::create('files', function ($t) { $t->increments('id'); $t->integer('client_id'); });
        Schema::create('comments', function ($t) { $t->increments('id'); $t->integer('reference_id'); $t->integer('reference_type'); $t->timestamps(); $t->softDeletes(); });
    }
    private function data($extra = []) { return array_replace(['name' => 'Müller & Söhne', 'country' => 'DE', 'city' => '1', 'address' => '123 Example Street', 'work_phone' => '+49 12345678', 'work_email' => 'qa-client@example.test'], $extra); }
    private function request($data = [], $files = [])
    {
        $request = StoreClientRequest::create('/clients', 'POST', $this->data($data), [], $files);
        $request->setContainer($this->app)->setRedirector($this->app['redirect']); $request->headers->set('Accept', 'application/json'); $request->validateResolved();
        return $request;
    }
    private function controller() { return app(ClientController::class); }
    public function test_unicode_create_ignores_identity_and_date_fields_and_hashes_password()
    {
        $password = str_repeat('x', 128);
        $response = $this->controller()->store($this->request(['id' => 9999, 'created_at' => '2000-01-01', 'dep_date' => '2000-01-01', 'password' => $password]));
        $client = Client::first();
        $this->assertSame(200, $response->getStatusCode()); $this->assertSame('Müller & Söhne', $client->name);
        $this->assertNotEquals(9999, $client->id); $this->assertNull($client->dep_date); $this->assertNotEquals(2000, $client->created_at->year);
        $this->assertTrue(Hash::check($password, $client->password)); $this->assertArrayNotHasKey('password', $client->toArray());
    }
    public function test_create_reports_duplicate_email_and_malformed_inputs()
    {
        $this->controller()->store($this->request());
        foreach ([['work_email' => 'qa-client@example.test'], ['name' => ['bad']], ['city' => ['bad']], ['address' => str_repeat('x', 192)]] as $bad) {
            try { $this->request($bad); $this->fail('Expected field errors'); }
            catch (\Illuminate\Validation\ValidationException $e) { $this->assertArrayHasKey(key($bad), $e->errors()); }
        }
        $this->assertSame(1, Client::count());
    }
    public function test_both_upload_fields_reject_unsafe_files()
    {
        foreach (['attach', 'attach_bus'] as $field) {
            try { $this->request([], [$field => [UploadedFile::fake()->createWithContent('evil.svg', '<svg><script>x</script></svg>')]]); $this->fail('Upload bypass'); }
            catch (\Illuminate\Validation\ValidationException $e) { $this->assertArrayHasKey($field === 'attach' ? 'attach.0' : $field, $e->errors()); }
        }
    }
    public function test_tours_and_invoices_block_deletion_including_archived_records()
    {
        $this->controller()->store($this->request()); $client = Client::first();
        app()->instance('request', Request::create('/', 'POST', [], [], [], ['HTTP_ACCEPT' => 'application/json']));
        foreach (['tours', 'client_invoices'] as $table) {
            foreach ([null, now()] as $deletedAt) {
                DB::table($table)->insert(['client_id' => $client->id, 'deleted_at' => $deletedAt]);
                $response = $this->controller()->destroy($client->id);
                $this->assertSame(422, $response->getStatusCode()); $this->assertFalse($response->getData(true)['success']); $this->assertNotNull(Client::find($client->id));
                DB::table($table)->delete();
            }
        }
        $this->controller()->destroy($client->id); $this->assertTrue(Client::withTrashed()->find($client->id)->trashed());
    }
    public function test_legacy_and_resource_delete_use_existing_permission_and_contact_helper_is_protected()
    {
        foreach (['/clients/1/update' => ['POST', 'clients.edit'], '/api/getClientContacts' => ['GET', 'clients.show'], '/clients/1' => ['DELETE', 'client.destroy'], '/clients/1/deleteMsg' => ['GET', 'client.destroy']] as $url => [$method, $permission]) {
            $request = Request::create($url, $method); $route = app('router')->getRoutes()->match($request); $this->assertSame($permission, $route->getAction('permissions'));
            $request->setRouteResolver(fn() => $route); $request->setUserResolver(fn() => new class { public $id = 1; public function hasPermissionTo($p) { return false; } });
            try { (new \App\Http\Middleware\PermissionsRequiredMiddleware)->handle($request, fn() => response('bypass')); $this->fail('Permission bypass'); }
            catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) { $this->assertSame(403, $e->getStatusCode()); }
        }
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        $this->controller()->getClientContacts(Request::create('/', 'GET', ['clientId' => 999999]));
    }
    public function test_portal_login_uses_hashes_and_rejects_wrong_password_without_debug_dump()
    {
        $this->controller()->store($this->request(['password' => 'Test-portal-password', 'contact_email' => 'qa-portal@example.test']));
        $request = Request::create('/', 'POST', ['contact_email' => 'qa-portal@example.test', 'password' => 'Test-portal-password']); $request->setLaravelSession(app('session.store'));
        $response = (new \App\Http\Controllers\TMSClient\LoginController)->Clientauth($request);
        $this->assertSame(route('TMS-Client.home'), $response->getTargetUrl()); $this->assertTrue(session('CLIENT_LOGIN'));
        $request->merge(['password' => 'wrong-password']);
        $this->assertSame(url('TMS-Client/login'), (new \App\Http\Controllers\TMSClient\LoginController)->Clientauth($request)->getTargetUrl());
    }
    public function test_detail_fields_render_escaped_legacy_values()
    {
        $source = file_get_contents(resource_path('views/clients/show.blade.php'));
        foreach (['client' => ['name', 'address', 'work_phone', 'contact_phone', 'work_email', 'contact_email', 'work_fax'], 'contact' => ['mobile_phone', 'work_phone', 'email']] as $variable => $fields) {
            foreach ($fields as $field) {
                preg_match('/<td>\s*\{\{\s*\$' . $variable . '->' . $field . '\s*\}\}\s*<\/td>/', $source, $fragment);
                $this->assertNotEmpty($fragment, $variable . '.' . $field);
                $record = (object)[$field => '<script>legacy</script>'];
                $html = (string)$this->blade($fragment[0], [$variable => $record]);
                $this->assertStringContainsString('&lt;script&gt;', $html); $this->assertStringNotContainsString('<script>', $html);
            }
        }
    }
}
