<?php
namespace Tests\Unit;

use App\Http\Middleware\PermissionsRequiredMiddleware;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class AccountingPermissionsTest extends TestCase
{
    private function runRoute(string $name, array $grants)
    {
        $request = Request::create('/test', 'POST');
        $route = (new Route('POST', '/test', fn () => 'ok'))->name($name);
        $request->setRouteResolver(fn () => $route);
        $user = new class($grants) {
            public $id = 1;
            private $grants;
            public function __construct($grants) { $this->grants = $grants; }
            public function hasPermissionTo($permission) { return in_array($permission, $this->grants); }
        };
        $request->setUserResolver(fn () => $user);
        return (new PermissionsRequiredMiddleware())->handle($request, fn () => response('ok'));
    }

    public function test_billing_create_uses_existing_transaction_permission()
    {
        $this->assertSame('ok', $this->runRoute('accounting.create', ['transactions.create'])->getContent());
    }

    public function test_read_only_role_cannot_create_office()
    {
        $this->expectException(HttpException::class);
        $this->runRoute('office.store', ['office.index', 'office.show']);
    }

    public function test_office_invoice_update_uses_existing_office_edit_permission()
    {
        $this->assertSame('ok', $this->runRoute('office_invoices.update', ['office.edit'])->getContent());
    }

    public function test_tax_read_permission_cannot_create_tax()
    {
        $this->expectException(HttpException::class);
        $this->runRoute('taxes.store', ['taxes.show']);
    }
}
