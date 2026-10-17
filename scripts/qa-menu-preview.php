<?php
// Read-only CLI preview. Permission relations are changed only in memory.
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
config(['session.driver' => 'array']);
$app->instance('middleware.disable', true);
$user = App\User::findOrFail(1);
$user->setRelation('permissions', Spatie\Permission\Models\Permission::all());
$user->setRelation('roles', collect());
Auth::guard()->setUser($user);
// Exercise the two menu gates even if these permission names are absent locally.
Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
    return in_array($ability, ['hotel.index', 'driver.index'], true) ? true : null;
});
View::share('errors', new Illuminate\Support\ViewErrorBag());
foreach (['/hotel/1/edit', '/driver/1/edit'] as $index => $path) {
    $request = Illuminate\Http\Request::create('http://127.0.0.1:8010'.$path);
    $request->setLaravelSession(app('session')->driver());
    $request->setUserResolver(fn () => $user);
    $app->instance('request', $request);
    $response = $app['router']->dispatch($request);
    if ($response->getStatusCode() !== 200) throw new RuntimeException('Menu preview failed');
    file_put_contents(storage_path('app/ui-audit/ui27-menu-'.$index.'.html'), $response->getContent());
}
echo "Menu previews rendered; no records or permissions saved.\n";
