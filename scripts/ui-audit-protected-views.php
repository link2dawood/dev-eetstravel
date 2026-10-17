<?php
// Render protected layout previews in a local CLI process; no role/user is saved.
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
config(['session.driver'=>'array']);
$app->instance('middleware.disable', true);
$user=App\User::findOrFail(1);
$user->setRelation('permissions', Spatie\Permission\Models\Permission::all());
$user->setRelation('roles', collect());
Auth::guard()->setUser($user);
View::share('errors',new Illuminate\Support\ViewErrorBag());
$paths=['/transaction','/transaction/create','/office','/office/create','/taxes','/taxes/create','/templates','/templates/0',
    '/settings','/settings/create','/officeInvoices/create/1','/tour_expenses/create/1','/utility_expenses/create/1',
    '/employes-salary/create/1','/office_earning/create/1','/office_balance/create/1','/holiday','/holiday/create','/comparison','/comparison/create'];
$directory=storage_path('app/ui-audit/protected-previews');
if(!is_dir($directory))mkdir($directory,0775,true);
$results=[];
foreach($paths as$index=>$path){
    try{
        $request=Illuminate\Http\Request::create('http://127.0.0.1:8010'.$path);
        $request->setLaravelSession(app('session')->driver());
        $request->setUserResolver(function()use($user){return$user;});
        $app->instance('request',$request);
        $response=$app['router']->dispatch($request);
        $file=$index.'.html';file_put_contents($directory.'/'.$file,$response->getContent());
        $results[]=['path'=>$path,'status'=>$response->getStatusCode(),'file'=>$file];
        echo $path.' '.$response->getStatusCode().PHP_EOL;
    }catch(Throwable$exception){$results[]=['path'=>$path,'error'=>get_class($exception).': '.$exception->getMessage()];echo $path.' RENDER ERROR'.PHP_EOL;}
}
file_put_contents($directory.'/results.json',json_encode($results,JSON_PRETTY_PRINT));
