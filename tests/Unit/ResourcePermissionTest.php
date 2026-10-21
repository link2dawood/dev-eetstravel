<?php

namespace Tests\Unit;

use App\Http\Middleware\PermissionsRequiredMiddleware;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class ResourcePermissionTest extends TestCase
{
    private function handleRoute(string $name, array $grants, ?string $explicit = null)
    {
        $request = Request::create('/permission-check', 'POST');
        $route = (new Route('POST', '/permission-check', fn () => 'ok'))->name($name);
        if ($explicit !== null) {
            $route->setAction(array_merge($route->getAction(), ['permissions' => $explicit]));
        }
        $request->setRouteResolver(fn () => $route);
        $user = new class($grants) {
            public $id = 1;
            private $grants;
            public function __construct(array $grants) { $this->grants = $grants; }
            public function hasPermissionTo($permission) { return in_array($permission, $this->grants, true); }
        };
        $request->setUserResolver(fn () => $user);
        return (new PermissionsRequiredMiddleware())->handle($request, fn () => response('ok'));
    }

    public function resourceSaves(): iterable
    {
        foreach (['hotel', 'clients', 'tour', 'task', 'users'] as $module) {
            yield $module . ' store' => [$module . '.store', $module . '.create'];
            yield $module . ' update' => [$module . '.update', $module . '.edit'];
        }
    }

    /** @dataProvider resourceSaves */
    public function test_resource_save_uses_the_existing_form_permission(string $route, string $permission): void
    {
        $this->assertSame('ok', $this->handleRoute($route, [$permission])->getContent());
    }

    public function test_read_only_permission_cannot_create(): void
    {
        $this->expectException(HttpException::class);
        $this->handleRoute('hotel.store', ['hotel.index', 'hotel.show']);
    }

    public function test_create_permission_cannot_update(): void
    {
        $this->expectException(HttpException::class);
        $this->handleRoute('hotel.update', ['hotel.create']);
    }

    public function test_explicit_permission_is_preserved(): void
    {
        $this->assertSame('ok', $this->handleRoute('hotel.store', ['special.approve'], 'special.approve')->getContent());
    }

    public function test_explicit_store_permission_is_not_rewritten(): void
    {
        $this->expectException(HttpException::class);
        $this->handleRoute('hotel.store', ['hotel.create'], 'hotel.store');
    }
}
