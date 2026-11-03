<?php
namespace Tests\Unit;

use App\User;
use App\Http\Controllers\SnappyMailController;
use App\Services\MailboxCredentials;
use App\Services\MailboxVerifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class EmailSafetyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']); DB::purge('sqlite');
        Schema::create('users', function ($t) {
            $t->increments('id'); $t->string('name')->default('QA'); $t->string('email_login')->nullable(); $t->text('email_password')->nullable(); $t->boolean('email_password_encrypted')->default(false); $t->timestamps();
        });
        config(['snappymail.url' => '/snappymail', 'snappymail.features.sso_enabled' => true, 'snappymail.features.direct_login' => true, 'snappymail.features.admin_access' => true, 'snappymail.features.user_config' => true]);
    }
    private function user($admin = false)
    {
        $user = $this->getMockBuilder(User::class)->onlyMethods(['hasRole', 'hasPermissionTo'])->getMock();
        $user->setTable('users');
        $user->method('hasRole')->willReturn($admin); $user->method('hasPermissionTo')->willReturn(true);
        $user->forceFill(['name' => 'QA']); $user->save(); Auth::setUser($user);
        return $user;
    }
    public function test_password_writes_encrypt_and_mark_credentials_without_double_encryption()
    {
        $user = new User; $user->email_password = 'Test-mailbox-password';
        $this->assertNotSame('Test-mailbox-password', $user->email_password);
        $this->assertTrue($user->email_password_encrypted);
        $this->assertSame('Test-mailbox-password', app(MailboxCredentials::class)->password($user));
        $encrypted = $user->email_password; $user->email_password = $encrypted;
        $this->assertSame($encrypted, $user->email_password);
        $user->email_password = null; $this->assertNull($user->email_password);
        $this->expectException(\RuntimeException::class); app(MailboxCredentials::class)->password($user);
    }
    public function test_missing_encryption_flag_does_not_prevent_decryption_and_invalid_ciphertext_fails()
    {
        $user = new User; $user->setRawAttributes(['email_password' => Crypt::encryptString('Test-password'), 'email_password_encrypted' => false]);
        $this->assertSame('Test-password', app(MailboxCredentials::class)->password($user));
        $user->setRawAttributes(['email_password' => 'damaged', 'email_password_encrypted' => true]);
        $this->expectException(\RuntimeException::class); app(MailboxCredentials::class)->password($user);
    }
    public function test_missing_mailbox_has_explicit_configuration_redirect_without_loop()
    {
        $this->user();
        $response = (new SnappyMailController)->sso(Request::create('/email/webmail'));
        $this->assertSame(route('snappymail.configure'), $response->getTargetUrl());
        $this->assertNotEmpty(session('error'));
    }
    public function test_email_permission_middleware_rejects_guests_and_denied_users()
    {
        $middleware = new \App\Http\Middleware\EmailAccess;
        $request = Request::create('/email/configure'); $request->setUserResolver(fn() => null);
        $this->assertSame(route('login'), $middleware->handle($request, fn() => response('bypass'))->getTargetUrl());
        $request->setUserResolver(fn() => new class { public function hasPermissionTo($p) { return false; } });
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $middleware->handle($request, fn() => response('bypass'));
    }
    public function test_configuration_verifies_before_saving_and_persists_success_on_configuration_page()
    {
        $user = $this->user();
        $verifier = $this->createMock(MailboxVerifier::class); $verifier->expects($this->once())->method('verify')->with('qa@example.test', ' Test-password ')->willReturn(true); app()->instance(MailboxVerifier::class, $verifier);
        $response = (new SnappyMailController)->saveConfiguration(Request::create('/', 'POST', ['email_login' => 'qa@example.test', 'email_password' => ' Test-password ']));
        $this->assertSame(route('snappymail.configure'), $response->getTargetUrl()); $this->assertNotEmpty(session('success'));
        $this->assertSame(' Test-password ', Crypt::decryptString(DB::table('users')->where('id', $user->id)->value('email_password')));
        $this->assertEquals(1, DB::table('users')->where('id', $user->id)->value('email_password_encrypted'));
    }
    public function test_failed_configuration_verification_does_not_change_account()
    {
        $user = $this->user();
        $verifier = $this->createMock(MailboxVerifier::class); $verifier->method('verify')->willReturn(false); app()->instance(MailboxVerifier::class, $verifier);
        try { (new SnappyMailController)->saveConfiguration(Request::create('/', 'POST', ['email_login' => 'qa@example.test', 'email_password' => 'Test-password'])); $this->fail('Expected rejection'); }
        catch (\Illuminate\Validation\ValidationException $e) { $this->assertNull($user->fresh()->email_password); $this->assertArrayHasKey('email_login', $e->errors()); }
    }
    public function test_feature_flags_and_admin_status_restrictions()
    {
        $this->user();
        foreach (['direct_login' => 'direct', 'admin_access' => 'admin', 'user_config' => 'configure', 'sso_enabled' => 'sso'] as $flag => $method) {
            config(['snappymail.features.' . $flag => false]);
            try { (new SnappyMailController)->$method(Request::create('/')); $this->fail('Disabled feature allowed'); }
            catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) { $this->assertSame(403, $e->getStatusCode()); }
            config(['snappymail.features.' . $flag => true]);
        }
        foreach (['admin', 'status'] as $method) {
            try { (new SnappyMailController)->$method(); $this->fail('Nonadmin allowed'); }
            catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) { $this->assertSame(403, $e->getStatusCode()); }
        }
        $this->user(true);
        $this->assertSame(url('/snappymail') . '/?admin', (new SnappyMailController)->admin()->getTargetUrl());
        $this->assertSame(url('/snappymail') . '/', (new SnappyMailController)->status()->getData(true)['url']);
    }
    public function test_direct_and_legacy_url_use_same_working_base()
    {
        $this->user(); config(['snappymail.url' => '/mail']);
        $this->assertSame(url('/snappymail') . '/', (new SnappyMailController)->direct()->getTargetUrl());
    }
    public function test_destructive_email_route_does_not_accept_get()
    {
        $request = Request::create('/email/remove/123/INBOX', 'POST');
        $route = app('router')->getRoutes()->match($request);
        $this->assertSame('email.remove', $route->getName()); $this->assertNotContains('GET', $route->methods());
        $csrf = new class(app(), app('encrypter')) extends \App\Http\Middleware\VerifyCsrfToken {
            public function excluded($request) { return $this->inExceptArray($request); }
        };
        $this->assertFalse($csrf->excluded($request));
    }
    public function test_raw_admin_gateway_cannot_bypass_role_and_data_paths_are_rejected()
    {
        $this->user();
        foreach ([['/snappymail/?admin', ''], ['/snappymail/data/config.ini', 'data/config.ini'], ['/snappymail/snappymail/v/include.php', 'snappymail/v/include.php']] as [$url, $path]) {
            try { (new SnappyMailController)->gateway(Request::create($url), $path); $this->fail('Gateway bypass'); }
            catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) { $this->assertContains($e->getStatusCode(), [403, 404]); }
        }
    }
    public function test_legacy_forms_have_required_variables_and_render()
    {
        $this->user();
        $controller = app(\App\Http\Controllers\EmailController::class);
        $compose = $controller->getComposeForm(Request::create('/'));
        $this->assertArrayHasKey('mail', $compose->getData()); $this->assertNull($compose->getData()['mail']);
        $this->assertIsString($compose->render());
        $this->assertIsString($controller->getMoveToForm(Request::create('/'))->render());
        $this->assertIsString($controller->composeEmailTemplate(Request::create('/'))->render());
    }
    public function test_http_email_and_raw_webmail_require_email_permission()
    {
        $user = $this->getMockBuilder(User::class)->onlyMethods(['hasRole', 'hasPermissionTo'])->getMock();
        $user->setTable('users'); $user->forceFill(['id' => 55]);
        $user->method('hasPermissionTo')->willReturn(false); $user->method('hasRole')->willReturn(false);
        $this->actingAs($user);
        foreach (['/email', '/email/configure', '/email/webmail', '/email/webmail/status', '/snappymail/?admin'] as $url) $this->getJson($url)->assertForbidden();
    }
    public function test_http_mailboxless_users_receive_configuration_help()
    {
        $this->actingAs($this->user());
        foreach (['/email', '/email/folder/INBOX', '/email/search', '/email/readAll', '/email/webmail'] as $url) $this->get($url)->assertRedirect(route('snappymail.configure'));
    }
    public function test_mailbox_passwords_and_sso_tokens_are_not_logged()
    {
        \Illuminate\Support\Facades\Log::spy();
        $middleware = new class extends \App\Http\Middleware\ErrorHandlingMiddleware {
            public function capture($request) { $this->logIncomingRequest($request, 'qa'); }
        };
        $middleware->capture(Request::create('/snappymail/?Sso&hash=qa-secret-token', 'POST', ['Password' => 'qa-secret-password']));
        $middleware->capture(Request::create('/email/configure', 'POST', ['email_login' => 'qa-secret-address@example.test', 'email_password' => 'qa-secret-password']));
        \Illuminate\Support\Facades\Log::shouldHaveReceived('info')->twice()->withArgs(function ($message, $context) {
            return strpos(json_encode($context), 'qa-secret') === false;
        });
        $this->assertTrue(true);
    }
}
