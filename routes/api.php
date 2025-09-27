<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Modern API Routes with Sanctum Authentication
Route::middleware(['auth:sanctum'])->group(function () {

    // Tour API Routes
    Route::prefix('tours')->name('api.tours.')->group(function () {
        Route::get('/', 'Api\TourApiController@index')->name('index');
        Route::post('/', 'Api\TourApiController@store')->name('store');
        Route::get('/search', 'Api\TourApiController@search')->name('search');
        Route::get('/calendar', 'Api\TourApiController@calendar')->name('calendar');
        Route::get('/statistics', 'Api\TourApiController@statistics')->name('statistics');
        Route::post('/bulk-action', 'Api\TourApiController@bulkAction')->name('bulk-action');

        Route::get('/{tour}', 'Api\TourApiController@show')->name('show');
        Route::put('/{tour}', 'Api\TourApiController@update')->name('update');
        Route::delete('/{tour}', 'Api\TourApiController@destroy')->name('destroy');
        Route::post('/{tour}/clone', 'Api\TourApiController@clone')->name('clone');
    });

    // Dashboard API Routes
    Route::prefix('dashboard')->name('api.dashboard.')->group(function () {
        Route::get('/', 'Api\DashboardController@index')->name('index');
        Route::get('/statistics', 'Api\DashboardController@statistics')->name('statistics');
        Route::get('/recent-activity', 'Api\DashboardController@recentActivity')->name('recent-activity');
        Route::get('/upcoming-deadlines', 'Api\DashboardController@upcomingDeadlines')->name('upcoming-deadlines');
        Route::get('/charts', 'Api\DashboardController@charts')->name('charts');
        Route::post('/cache/clear', 'Api\DashboardController@clearCache')->name('cache.clear');
    });

    // Client API Routes
    Route::prefix('clients')->name('api.clients.')->group(function () {
        Route::get('/', 'Api\ClientApiController@index')->name('index');
        Route::post('/', 'Api\ClientApiController@store')->name('store');
        Route::get('/search', 'Api\ClientApiController@search')->name('search');
        Route::get('/{client}', 'Api\ClientApiController@show')->name('show');
        Route::put('/{client}', 'Api\ClientApiController@update')->name('update');
        Route::delete('/{client}', 'Api\ClientApiController@destroy')->name('destroy');
        Route::get('/{client}/tours', 'Api\ClientApiController@tours')->name('tours');
    });

    // User API Routes
    Route::prefix('users')->name('api.users.')->group(function () {
        Route::get('/', 'Api\UserApiController@index')->name('index');
        Route::get('/search', 'Api\UserApiController@search')->name('search');
        Route::get('/{user}', 'Api\UserApiController@show')->name('show');
        Route::put('/{user}', 'Api\UserApiController@update')->name('update');
        Route::get('/{user}/tours', 'Api\UserApiController@tours')->name('tours');
        Route::get('/{user}/tasks', 'Api\UserApiController@tasks')->name('tasks');
    });

    // Task API Routes
    Route::prefix('tasks')->name('api.tasks.')->group(function () {
        Route::get('/', 'Api\TaskApiController@index')->name('index');
        Route::post('/', 'Api\TaskApiController@store')->name('store');
        Route::get('/search', 'Api\TaskApiController@search')->name('search');
        Route::get('/overdue', 'Api\TaskApiController@overdue')->name('overdue');
        Route::get('/{task}', 'Api\TaskApiController@show')->name('show');
        Route::put('/{task}', 'Api\TaskApiController@update')->name('update');
        Route::delete('/{task}', 'Api\TaskApiController@destroy')->name('destroy');
        Route::patch('/{task}/status', 'Api\TaskApiController@updateStatus')->name('update-status');
    });

    // Settings API Routes
    Route::prefix('settings')->name('api.settings.')->group(function () {
        Route::get('/cache/status', 'Api\SettingsApiController@cacheStatus')->name('cache.status');
        Route::post('/cache/clear', 'Api\SettingsApiController@clearCache')->name('cache.clear');
        Route::post('/cache/warm-up', 'Api\SettingsApiController@warmUpCache')->name('cache.warm-up');
    });
});

Route::group(['prefix' => 'v1'], function () {
    //    Route::resource('task', 'TasksController');

    Route::post('users/{userId}/emailfolders', '\App\Http\Controllers\Api\EmailsController@getFolderList');
    Route::any('users/{userId}/emails', '\App\Http\Controllers\Api\EmailsController@getEmails');
	Route::any('users/{userId}/touremails', '\App\Http\Controllers\Api\EmailsController@touremails');
    Route::any('users/{userId}/emails/createFolder', '\App\Http\Controllers\Api\EmailsController@createFolder');

    Route::post('users/{userId}/attachment', '\App\Http\Controllers\Api\EmailsController@downloadAttachments');
    Route::any('users/{userId}/emails/send', '\App\Http\Controllers\Api\EmailsController@sendEmail');

    Route::post('users/{userId}/email/{emailId}/delete', '\App\Http\Controllers\Api\EmailsController@deleteEmail');
    Route::post('users/{userId}/email/{emailId}/move', '\App\Http\Controllers\Api\EmailsController@moveEmail');
    Route::any('users/{userId}/email/{emailId}/get', '\App\Http\Controllers\Api\EmailsController@getEmail');
	Route::any('getTours', '\App\Http\Controllers\Api\EmailsController@getTours');
	Route::any('getArchiveTours', '\App\Http\Controllers\Api\EmailsController@getArchiveTours');

    Route::get('dashboard/announcements', '\App\Http\Controllers\Api\DashboardController@getAnnouncements');
    Route::get('dashboard/inbox', '\App\Http\Controllers\Api\DashboardController@getInbox');
    Route::get('dashboard/activities', '\App\Http\Controllers\Api\DashboardController@getActivities');
    Route::get('dashboard/tasks', '\App\Http\Controllers\Api\DashboardController@getTasks');
    Route::get('dashboard/tours', '\App\Http\Controllers\Api\DashboardController@getLatestTours');
    Route::get('dashboard/chat_groups', '\App\Http\Controllers\Api\DashboardController@getChatGroups');
    Route::get('dashboard/create_task_popup', '\App\Http\Controllers\Api\DashboardController@getTaskCreatePopup');
    Route::get('dashboard/tour_users', '\App\Http\Controllers\Api\DashboardController@getTourUsers');
    Route::get('dashboard/modal_add_tour', '\App\Http\Controllers\Api\DashboardController@getModalAddTour');
    //Please do not remove this if you want adminlte:route and adminlte:link commands to works correctly.
    #adminlte_api_routes

});
Route::any('test', '\App\Http\Controllers\Api\EmailsController@parseEmails');