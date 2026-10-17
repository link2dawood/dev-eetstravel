<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Route;

$directory = storage_path('app/ui-audit');
if (!is_dir($directory)) mkdir($directory, 0775, true);
$tables = [
    'HotelController'=>'hotels', 'EventController'=>'events', 'GuideController'=>'guides', 'RestaurantController'=>'restaurants',
    'ClientController'=>'clients', 'ClientInvoiceController'=>'client_invoices', 'InvoicesController'=>'invoices_tours',
    'TransactionController'=>'transactions', 'OfficeController'=>'office_fees', 'OfficeInvoiceController'=>'office_invoices',
    'TaxController'=>'invoice_taxes', 'TourExpenseController'=>'office_tours', 'UtilityExpenseController'=>'office_utility_expenses',
    'EmployesSalaryController'=>'office_employes_salary', 'OfficeEarningController'=>'office_earnings', 'BalanceAmountController'=>'office_balance',
    'TransferController'=>'transfers', 'BusController'=>'buses', 'DriverController'=>'drivers', 'TourController'=>'tours',
    'TourPackageController'=>'tour_packages', 'TaskController'=>'tasks', 'StatusController'=>'status',
    'RoomTypesController'=>'room_types', 'RateController'=>'rates', 'CurrencyRateController'=>'currency_rates',
    'CurrenciesController'=>'currencies', 'CriteriaController'=>'criterias', 'CommentController'=>'comments',
    'CruisesController'=>'cruises', 'FlightController'=>'flights', 'SettingController'=>'settings',
    'AnnouncementController'=>'announcements', 'ChatController'=>'chats', 'UserController'=>'users',
    'RoleController'=>'roles', 'PermissionController'=>'permissions', 'QuotationController'=>'quotations',
    'GuestListController'=>'guest_lists', 'ComparisonController'=>'comparisons', 'MenuController'=>'menus',
    'SeasonsController'=>'seasons', 'AgreementsController'=>'hotel_agreements', 'OfferController'=>'hotel_offers',
];
$ids = [];
foreach ($tables as $controller => $table) {
    if (!Schema::hasTable($table) || !Schema::hasColumn($table, 'id')) continue;
    $query = DB::table($table);
    if (Schema::hasColumn($table, 'deleted_at')) $query->whereNull('deleted_at');
    $ids[$controller] = $query->min('id');
}
$pages = []; $excluded = [];
foreach (Route::getRoutes() as $route) {
    if (!in_array('GET', $route->methods())) continue;
    $action = $route->getActionName();
    if (strpos(ltrim($action, chr(92)), 'App') !== 0 || strpos($action, '@') === false) continue;
    [$class, $method] = explode('@', $action);
    $controller = class_basename($class);
    $uri = $route->uri();
    if (in_array($controller, ['TourController','ComparisonController']) && $method === 'show') {
        $excluded[] = ['uri'=>$uri, 'reason'=>'Opening the page can initialize comparison records; use an archived response or an isolated database']; continue;
    }
    if (preg_match('/\\\\Api\\\\|\\\\TMSClient\\\\|\\\\TMSSupplier\\\\/', $class)) continue;
    if (in_array($controller, ['EmailController', 'SnappyMailController', 'gCalendarController', 'RoomListController'])) {
        $excluded[] = ['uri'=>$uri, 'reason'=>'Live service or GET action with side effects; source review only']; continue;
    }
    if (!in_array($method, ['index','create','show','edit','calendar','dashboard','main','getCurrentOffers','getPastOffers','getCurrentBookings','cancellationPolicies','profile'])) continue;
    $parameters = $route->parameterNames();
    $id = $ids[$controller] ?? null;
    if ($controller === 'TemplatesController') $id = 0;
    if (in_array($controller, ['OfficeInvoiceController','TourExpenseController','UtilityExpenseController','EmployesSalaryController','OfficeEarningController','BalanceAmountController']) && $method === 'create') $id = $ids['OfficeController'] ?? null;
    if ($controller === 'TourPackageController' && $method === 'create') $id = DB::table('tour_days')->min('id');
    if (in_array($controller, ['QuotationController','GuestListController']) && $method === 'create') $id = $ids['TourController'] ?? null;
    if ($parameters && $id === null) { $excluded[]=['uri'=>$uri,'reason'=>'No matching record for detail/form']; continue; }
    $path = $uri;
    foreach ($parameters as $parameter) $path = preg_replace('/\{' . preg_quote($parameter, '/') . '\??\}/', (string) $id, $path);
    $path = '/' . ltrim($path, '/');
    if (preg_match('/delete|destroy|logout|clear|import|repair|send|sso|sync|generate/i', $path)) continue;
    $pages[$path] = ['path'=>$path, 'route'=>$route->getName(), 'controller'=>$controller, 'method'=>$method, 'guest'=>false];
}
foreach (['/login','/register','/password/reset','/TMS-Client/login','/TMS-Supplier/login'] as $path) {
    $pages[$path] = ['path'=>$path,'controller'=>'Authentication','method'=>'form','guest'=>true];
}
foreach (['/supplier_search','/activities','/images/index','/chat/main'] as $path) {
    if (!isset($pages[$path])) $pages[$path]=['path'=>$path,'controller'=>'Main navigation','method'=>'index','guest'=>false];
}
$baseline=[];
foreach (DB::select('SHOW TABLES') as $row) {
    $table=array_values((array)$row)[0]; $hashes=[];
    foreach(DB::table($table)->get() as $record){$hash=hash('sha256',json_encode($record));$hashes[$hash]=($hashes[$hash]??0)+1;}
    $baseline[$table]=$hashes;
}
file_put_contents($directory.'/before.json', json_encode($baseline));
file_put_contents($directory.'/inventory.json', json_encode(['pages'=>array_values($pages),'excluded'=>$excluded], JSON_PRETTY_PRINT));
echo count($pages).' display routes inventoried; '.count($excluded).' exclusions documented.'.PHP_EOL;
