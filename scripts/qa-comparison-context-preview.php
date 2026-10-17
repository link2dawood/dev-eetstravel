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
View::share('errors', new Illuminate\Support\ViewErrorBag());
foreach (['/comparison', '/comparison/create'] as $index => $path) {
    $request = Illuminate\Http\Request::create('http://127.0.0.1:8010'.$path);
    $request->setLaravelSession(app('session')->driver());
    $request->setUserResolver(fn () => $user);
    $app->instance('request', $request);
    $response = $app['router']->dispatch($request);
    if ($response->getStatusCode() !== 200) throw new RuntimeException('Comparison preview failed');
    file_put_contents(storage_path('app/ui-audit/ui15-comparison-'.$index.'.html'), $response->getContent());
}
echo "Comparison context previews rendered; no records or permissions saved.\n";