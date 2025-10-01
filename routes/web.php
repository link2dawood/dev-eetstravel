<?php
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Auth::routes();

Route::get('/clear', function () {

    Artisan::call('cache:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');
    return "Cleared!";
});
Route::get('getgooglecities/', 'ImportController@getGoogleCities');


//Route::get('/', function () {
//    return view('vendor.adminlte.auth.login');
//});
Route::get('/', ['uses'        => '\App\Http\Controllers\ScaffoldInterface\AppController@dashboard',
                     'middleware'  => ['web'],
                     'permissions' => 'dashboard.index',
                     'as'          => ''
]);

//Route::group(['middleware' => 'auth'], function () {
    //    Route::get('/link1', function ()    {
//        // Uses Auth Middleware
//    });

    //Please do not remove this if you want adminlte:route and adminlte:link commands to works correctly.
    #adminlte_routes
//});
Route::group(['middleware' => 'perm'], function () {
    Route::group(['middleware' => 'web'], function () {
        Route::resource(
            'hotel',
            '\App\Http\Controllers\HotelController'
        );
        Route::post('hotel/{id}/update', '\App\Http\Controllers\HotelController@update');
        Route::get('hotel/{id}/delete', '\App\Http\Controllers\HotelController@destroy')->name('hotel.destroy');
        Route::get('hotel/{id}/deleteMsg', '\App\Http\Controllers\HotelController@DeleteMsg');
        Route::get('api/getItemContactView', '\App\Http\Controllers\HotelController@getItemContactView');
        Route::get('api/getItemsContacts', '\App\Http\Controllers\HotelController@getItemsContacts');
    });

//event Routes
    Route::group(['middleware' => 'web'], function () {
        Route::resource('event', '\App\Http\Controllers\EventController');
        Route::post('event/{id}/update', '\App\Http\Controllers\EventController@update');
        Route::get('event/{id}/delete', '\App\Http\Controllers\EventController@destroy')->name('event.destroy');
        Route::get('event/{id}/deleteMsg', '\App\Http\Controllers\EventController@DeleteMsg');
    });

//gCalendar Routes
Route::group(['middleware' => 'web'], function () {
    Route::get('calendars', '\App\Http\Controllers\gCalendarController@getCalendars');
});

//guide Routes
    Route::group(['middleware' => 'web'], function () {
        Route::resource('guide', '\App\Http\Controllers\GuideController');
        Route::post('guide/{id}/update', '\App\Http\Controllers\GuideController@update');
        Route::get('guide/{id}/delete', '\App\Http\Controllers\GuideController@destroy')->name('guide.destroy');
        Route::get('guide/{id}/deleteMsg', '\App\Http\Controllers\GuideController@DeleteMsg');
    });

//restaurant Routes
    Route::group(['middleware' => 'web'], function () {
        Route::resource('restaurant', '\App\Http\Controllers\RestaurantController');
        Route::post('restaurant/{id}/update', '\App\Http\Controllers\RestaurantController@update');
        Route::get('restaurant/{id}/delete', '\App\Http\Controllers\RestaurantController@destroy')->name('restaurant.destroy');
        Route::get('restaurant/{id}/deleteMsg', '\App\Http\Controllers\RestaurantController@DeleteMsg');
        Route::get('restaurant/api/data', 'RestaurantController@data')->name('restaurant_data');
    });

//client of the agency
Route::group(['middleware' => 'web'], function () {
    Route::resource('clients', '\App\Http\Controllers\ClientController');
    Route::post('clients/{id}/update', '\App\Http\Controllers\ClientController@update');
    Route::get('clients/{id}/delete', '\App\Http\Controllers\ClientController@destroy')->name('client.destroy');
    Route::get('clients/{id}/deleteMsg', '\App\Http\Controllers\ClientController@DeleteMsg');
	Route::get('api/getClientContacts', '\App\Http\Controllers\ClientController@getClientContacts');
	
	Route::get('/countries','\App\Http\Controllers\ClientController@getCountries');
Route::get('/states/{countryCode}','\App\Http\Controllers\ClientController@getStates');
});

//Invoices of TMS
Route::group(['middleware' => 'web'], function () {
   	Route::resource('invoices', '\App\Http\Controllers\InvoicesController');
   	Route::post('invoices/{id}/update', '\App\Http\Controllers\InvoicesController@update')->name('invoice.update');
    Route::get('invoices/{id}/delete', '\App\Http\Controllers\InvoicesController@destroy')->name('invoices.destroy');
    Route::get('invoices/{id}/deleteMsg', '\App\Http\Controllers\InvoicesController@DeleteMsg');
    Route::get('invoices/api/data', 'InvoicesController@data')->name('invoices_data');
	 Route::get('invoicesClientTransaction/api/data/{invoiceId}', 'InvoicesController@clientTransactionData')->name('client_invoices_data');
	Route::get('supplierdropdown/{id}', 'InvoicesController@supplierDropdown');
	Route::get('add_payment/{id}', '\App\Http\Controllers\InvoicesController@add_payment')->name('add_payment');
	Route::post('payment_store/{id}', '\App\Http\Controllers\InvoicesController@payment_store')->name('payment.store');
	
});
//Account of TMS
Route::group(['middleware' => 'web'], function () {
	Route::resource('transaction', '\App\Http\Controllers\TransactionController');
	Route::get('transaction/api/data', 'TransactionController@data')->name('transaction_data');
	Route::get('transaction/{id}/delete', '\App\Http\Controllers\TransactionController@destroy')->name('transaction.destroy');
	Route::get('transaction/{id}/deleteMsg', '\App\Http\Controllers\TransactionController@DeleteMsg');
   	Route::resource('accounting', '\App\Http\Controllers\ClientInvoiceController');
   	Route::post('accounting/{id}/update', '\App\Http\Controllers\ClientInvoiceController@update')->name('accounts.update');
    Route::get('accounting/{id}/delete', '\App\Http\Controllers\ClientInvoiceController@destroy')->name('accounts.destroy');
    Route::get('accounting/{id}/deleteMsg', '\App\Http\Controllers\ClientInvoiceController@DeleteMsg');
    Route::get('accounting/api/data', 'ClientInvoiceController@data')->name('accounts_data');
	Route::get('ClientAccountingData/api/data/{clientId}', 'ClientInvoiceController@ClientAccountingData')->name('client_accounting_data');
	Route::get('accountingServiceTransaction/create/{tour_id}', 'ClientInvoiceController@serviceTransactionCreate')->name('service_transaction_create');
 Route::get('accountingServiceTransaction/api/data/{pay_to}/{invoice_id}', 'ClientInvoiceController@serviceTransactionData')->name('service_transaction_data');
	Route::get('/accounting/{id}/export/{pdf_type}', 'ClientInvoiceController@pdfExport')->name('accounting_pdf_export');
	Route::get('/accounting/{id}/excel', 'ClientInvoiceController@excelExport')->name('accounting_excel_export');
	Route::get('api/getItemInvoiceView', '\App\Http\Controllers\ClientInvoiceController@getItemInvoiceView');
	Route::get('api/getInvoiceItem', '\App\Http\Controllers\ClientInvoiceController@getInvoiceItem');
	Route::get('accounting/api/getTourquotation/{id}', '\App\Http\Controllers\ClientInvoiceController@getTourquotation');
	Route::get('api/getInvoicePayments/{id}', '\App\Http\Controllers\ClientInvoiceController@getInvoicePayments');
	Route::get('api/getPaymentView', '\App\Http\Controllers\ClientInvoiceController@getPaymentView');
	Route::get('addInvoicePayment/{id}', '\App\Http\Controllers\ClientInvoiceController@add_payment')->name('add__invoice_payment');
	Route::post('inv_payment_store/{id}', '\App\Http\Controllers\ClientInvoiceController@payment_store')->name('inv_payment.store');
});
	
//Reporting of TMS
Route::group(['middleware' => 'web'], function () {
   	Route::resource('reporting', '\App\Http\Controllers\ReportingController');
   	Route::post('reporting/{id}/update', '\App\Http\Controllers\ReportingController@update')->name('reporting.update');
    Route::get('reporting/{id}/delete', '\App\Http\Controllers\ReportingController@destroy')->name('reporting.destroy');
    Route::get('reporting/{id}/deleteMsg', '\App\Http\Controllers\ReportingController@DeleteMsg');
    Route::get('reporting/api/data', 'ReportingController@data')->name('reporting_data');
	Route::get('/reporting_supplier_show', 'ReportingController@show')->name('reporting_supplier_show');
	Route::get('reporting/hotel/{id}/show', '\App\Http\Controllers\ReportingController@hotel_show')->name('reporting.hotel.show');
 	Route::get('reporting/event/{id}/show', '\App\Http\Controllers\ReportingController@event_show')->name('reporting.event.show');
 	Route::get('reporting/guide/{id}/show', '\App\Http\Controllers\ReportingController@guide_show')->name('reporting.guide.show');
	Route::get('reporting/restaurant/{id}/show', '\App\Http\Controllers\ReportingController@restaurant_show')->name('reporting.restaurant.show');
});
//Officefee of TMS
Route::group(['middleware' => 'web'], function () {
   	Route::resource('office', '\App\Http\Controllers\OfficeController');
	
   	Route::post('office/{id}/update', '\App\Http\Controllers\OfficeController@update')->name('office.update');
    Route::get('office/{id}/delete', '\App\Http\Controllers\OfficeController@destroy')->name('office.destroy');
    Route::get('office/{id}/deleteMsg', '\App\Http\Controllers\OfficeController@DeleteMsg');
});
	
//OfficeInvoices of TMS
	
Route::group(['middleware' => 'web'], function () {
   	Route::resource('officeInvoices', '\App\Http\Controllers\OfficeInvoiceController');
	Route::get('officeInvoices/create/{id}', '\App\Http\Controllers\OfficeInvoiceController@create')->name('officeInvoices.create');
	Route::post('/officeInvoices/store', 'OfficeInvoiceController@store')->name('officeInvoices.stored');
	Route::get('/officeInvoices/{id}/export/{pdf_type}', 'OfficeInvoiceController@pdfExport')->name('office_invoices_pdf_export');


//------- Office Invoice details -----//
		Route::get('officeInvoicesdetail/{id}', '\App\Http\Controllers\OfficeInvoiceController@office_invoice_details')->name('office_invoices_detail.show');
	Route::get('/office-invoices-details/api/data/{id}', 'OfficeInvoiceController@getOfficeInvoicesdeatailsdata')->name('officeinvoicegetdatadetail');

   	Route::post('officeInvoices/{id}/update', '\App\Http\Controllers\OfficeInvoiceController@update')->name('office_invoices.update');
    Route::get('officeInvoices/{id}/delete', '\App\Http\Controllers\OfficeInvoiceController@destroy')->name('office_invoices.destroy');
    Route::get('officeInvoices/{id}/deleteMsg', '\App\Http\Controllers\OfficeInvoiceController@DeleteMsg');
    Route::get('officeInvoices/api/data/{id}', 'OfficeInvoiceController@data')->name('office_invoices_data');
});
//Taxes of TMS
Route::group(['middleware' => 'web'], function () {
   	Route::resource('taxes', '\App\Http\Controllers\TaxController');
      
    Route::get('taxes/api/dat', 'TaxController@data')->name('taxes_data');
    Route::post('taxes/{id}/update', '\App\Http\Controllers\TaxController@update')->name('taxes_update');
    
 	
});
//Tour Expenses of offices of TMS
Route::group(['middleware' => 'web'], function () {
   	Route::resource('tour_expenses', '\App\Http\Controllers\TourExpenseController');
	Route::get('tour_expenses/create/{id}', '\App\Http\Controllers\TourExpenseController@create')->name('tour_expenses.create');
   	Route::post('tour_expenses/{id}/update', '\App\Http\Controllers\TourExpenseController@update')->name('tour_expenses.update');
    Route::get('tour_expenses/{id}/delete', '\App\Http\Controllers\TourExpenseController@destroy')->name('tour_expenses.destroy');
    Route::get('tour_expenses/{id}/deleteMsg', '\App\Http\Controllers\TourExpenseController@DeleteMsg');
    Route::get('tour_expenses/api/data/{id}', 'TourExpenseController@data')->name('tour_expenses_data');
});
//Utility Expenses of offices TMS
Route::group(['middleware' => 'web'], function () {
   	Route::resource('utility_expenses', '\App\Http\Controllers\UtilityExpenseController');
	Route::get('utility_expenses/create/{id}', '\App\Http\Controllers\UtilityExpenseController@create')->name('utility_expenses.create');
   	Route::post('utility_expenses/{id}/update', '\App\Http\Controllers\UtilityExpenseController@update')->name('utility_expenses.update');
    Route::get('utility_expenses/{id}/delete', '\App\Http\Controllers\UtilityExpenseController@destroy')->name('utility_expenses.destroy');
    Route::get('utility_expenses/{id}/deleteMsg', '\App\Http\Controllers\UtilityExpenseController@DeleteMsg');
    Route::get('utility_expenses/api/data/{id}', 'UtilityExpenseController@data')->name('utility_expenses_data');
});
//Employee Salary  of offices of TMS
Route::group(['middleware' => 'web'], function () {
   	Route::resource('employes-salary', '\App\Http\Controllers\EmployesSalaryController');
	Route::get('employes-salary/create/{id}', '\App\Http\Controllers\EmployesSalaryController@create')->name('employes-salary.create');
   	Route::post('employes-salary/{id}/update', '\App\Http\Controllers\EmployesSalaryController@update')->name('employes-salary.update');
    Route::get('employes-salary/{id}/delete', '\App\Http\Controllers\EmployesSalaryController@destroy')->name('employes-salary.destroy');
    Route::get('employes-salary/{id}/deleteMsg', '\App\Http\Controllers\EmployesSalaryController@DeleteMsg');
    Route::get('employes-salary/api/data/{id}', 'EmployesSalaryController@data')->name('employes-salary_data');
});
//Total Earning of offices TMS
Route::group(['middleware' => 'web'], function () {
   	Route::resource('office_earning', '\App\Http\Controllers\OfficeEarningController');
	Route::get('office_earning/create/{id}', '\App\Http\Controllers\OfficeEarningController@create')->name('office_earning.create');
   	Route::post('office_earning/{id}/update', '\App\Http\Controllers\OfficeEarningController@update')->name('office_earning.update');
    Route::get('office_earning/{id}/delete', '\App\Http\Controllers\OfficeEarningController@destroy')->name('office_earning.destroy');
    Route::get('office_earning/{id}/deleteMsg', '\App\Http\Controllers\OfficeEarningController@DeleteMsg');
    Route::get('office_earning/api/data/{id}', 'OfficeEarningController@data')->name('office_earning_data');
});
//Balance Amount of offices TMS
Route::group(['middleware' => 'web'], function () {
   	Route::resource('office_balance', '\App\Http\Controllers\BalanceAmountController');
	Route::get('office_balance/create/{id}', '\App\Http\Controllers\BalanceAmountController@create')->name('office_balance.create');
   	Route::post('office_balance/{id}/update', '\App\Http\Controllers\BalanceAmountController@update')->name('office_balance.update');
    Route::get('office_balance/{id}/delete', '\App\Http\Controllers\BalanceAmountController@destroy')->name('office_balance.destroy');
    Route::get('office_balance/{id}/deleteMsg', '\App\Http\Controllers\BalanceAmountController@DeleteMsg');
    Route::get('office_balance/api/data/{id}', 'BalanceAmountController@data')->name('office_balance_data');
});
// Google Calendar API
Route::group(['middleware' => 'web'], function () {
    Route::get('oauth', '\App\Http\Controllers\gCalendarController@oauth')->name('cal.oauth');
    Route::get('calendar/list', '\App\Http\Controllers\gCalendarController@getCalendarListJson')->name('cal.list');
});

//transfer Routes
    Route::group(['middleware' => 'web'], function () {
        Route::resource('transfer', '\App\Http\Controllers\TransferController');
        Route::post('transfer/{id}/update', '\App\Http\Controllers\TransferController@update');
        Route::get('transfer/{id}/delete', '\App\Http\Controllers\TransferController@destroy')->name('transfer.destroy');
        Route::get('transfer/{id}/deleteMsg', '\App\Http\Controllers\TransferController@DeleteMsg');
        Route::get('transfer/api/data', '\App\Http\Controllers\TransferController@data')->name('transfer_data');
    });

//tour_package Routes
    Route::group(['middleware' => 'web'], function () {
        Route::resource('tour_package', '\App\Http\Controllers\TourPackageController');
        Route::post('tour_package/{id}/update', '\App\Http\Controllers\TourPackageController@update')->name('tour_package.update');
        Route::get('tour_package/{id}/create', '\App\Http\Controllers\TourPackageController@create')->name('tour_package.create');
        Route::get('tour_package/{id}/delete', '\App\Http\Controllers\TourPackageController@destroy')->name('tour_package.destroy');
        Route::get('tour_package/{id}/deleteMsg', '\App\Http\Controllers\TourPackageController@DeleteMsg');
        Route::post('tour_package/{id}/generateServices', '\App\Http\Controllers\TourPackageController@generateServices')->name('tour_package.generateServices');
        Route::get('/tour_package/{id}/api', '\App\Http\Controllers\TourPackageController@apiData')->name('service_api_data');
        Route::get('/tour_package/{id}/change_tour_day', 'TourPackageController@changeTourDay')->name('change_tour_day');
        Route::get('/tour_package/{id}/change_time', 'TourPackageController@changeTime')->name('change_time');
        Route::post('/description_package', 'TourPackageController@descriptionPackage')->name('description_package');
        Route::get('/reorder_package', 'TourPackageController@reorderService')->name('reorder_package');
        Route::get('/package/api/ajax_update', 'TourPackageController@ajaxUpdate');
        Route::post('/hotel_room_types', 'TourPackageController@viewHotelRoomType');
		Route::post('/hotel_room_types_tours', 'TourPackageController@viewHotelRoomType');
        Route::post('/tour_package/api/update_status', 'TourPackageController@ajaxUpdateStatus');
        Route::get('/tour_package/api/status_list', 'TourPackageController@statusList');
        Route::get('/tour_package/api/status_list_hotel', 'TourPackageController@statusListHotel');
        Route::get('/tour_package/api/status_list_transfer', 'TourPackageController@statusListTransfer');
        Route::get('/get_last_date_hotel/api/{id}', 'TourPackageController@getLastDateForHotel');
        Route::get('/get_date_day/{id}', 'TourPackageController@getDateDayId');
	    Route::get('/tour_package/{id}/main_hotel', 'TourPackageController@mainHotel');
	    Route::get('/get_packages_for_delete/{id}', 'TourPackageController@getPackagesHotelConfirmed');
	    Route::get('/delete_packages_hotel_cancelled/{id}', 'TourPackageController@deletePackagesCancelledHotel');
		Route::get('/fellowconfirmHotel/{id}', 'TourPackageController@fellowconfirmHotel');
	    Route::get('/api/get_status/{id}', 'TourPackageController@apiGetStatus');
	    Route::get('/api/get_tour_package_transfer_dates/{id}', 'TourPackageController@getTourPackageTransferDates');
	    Route::get('/api/tour_package_transfer/{id}/delete', 'TourPackageController@deleteTransferTourPackage');

    });
	Route::group(['middleware' => 'web'], function () {
		Route::get('/current_offers', 'OfferController@current_offers')->name('current_offers.index');
		Route::get('/past_offers', 'OfferController@past_offers')->name('past_offers.index');
		Route::get('/current_bookings', 'OfferController@current_bookings')->name('current_bookings.index');
		Route::get('/cancellation_policies', 'OfferController@cancellation_policies')->name('cancellation_policies.index');
		Route::get('/recent_offers_data', 'OfferController@recent_offers_data')->name('recent_offers_data');
		Route::get('/past_offers_data', 'OfferController@past_offers_data')->name('past_offers_data');
		Route::get('/current_bookings_data', 'OfferController@current_bookings_data')->name('current_bookings_data');
		Route::get('/cancellation_policies_data', 'OfferController@cancellation_policies_data')->name('cancellation_policies_data');
		Route::get('/tour_package/hotel_offers/{id}', 'OfferController@hotel_offers')->name('offers');
		Route::get('/tour_package/hotel_offers/{id}/create', 'OfferController@create')->name('offers.create')
			;
		Route::get('/tour_package/hotel_offers/{id}/emails', 'OfferController@offer_emails')->name('offers.emails');
		Route::get('offer/{id}/deleteMsg','\App\Http\Controllers\OfferController@DeleteMsg')->name('offer.deleteMsg');
    	Route::get('offer/{id}/delete','\App\Http\Controllers\OfferController@destroy')->name('offer.destroy');
		Route::get('offer/{id}/assign_to_tour','\App\Http\Controllers\OfferController@assign_to_tour')->name('offer.assign_tour');
		Route::post('offer/{id}/days_dropdown','\App\Http\Controllers\TourController@days_dropdown')->name('offer.days_dropdown');
		Route::get('/monthly_chart_data', 'TourController@monthly_chart_data')->name('monthly_chart_data');
		Route::get('update-status/{id}', '\App\Http\Controllers\TourController@updateStatus');
		Route::get('/cancelled_chart_data', 'TourController@cancelled_chart_data')->name('cancelled_chart_data');
		Route::get('/tour_achieve_data', 'TourController@tour_achieve_data')->name('tour_achieve_data');
	});
//tour Routes
    Route::group(['middleware' => 'web'], function () {
        Route::resource('tour', '\App\Http\Controllers\TourController');
        Route::post('tour/{id}/update', '\App\Http\Controllers\TourController@update');
        Route::get('tour/{id}/delete', '\App\Http\Controllers\TourController@destroy')->name('tour.destroy');
        Route::get('tour/{id}/deleteMsg', '\App\Http\Controllers\TourController@DeleteMsg')->name('tour.deleteMsg');
        Route::get('tour/{id}/delete/{tab}', '\App\Http\Controllers\TourController@destroy')->name('tour_tab.destroy');
        Route::get('tour/{id}/deleteMsg/{tab}', '\App\Http\Controllers\TourController@DeleteMsg');
        Route::post('tour/{id}/generatePackages', '\App\Http\Controllers\TourController@generatePackages')->name('generate_packages');
        Route::get('/tour/{id}/api/{export}/{type?}', '\App\Http\Controllers\TourController@export')->name('tour_export');
        Route::get('/tour/{id}/pdf_data', '\App\Http\Controllers\TourController@pdfData')->name('pdf_data');
        Route::get('tour/api/tasks_data', '\App\Http\Controllers\TourController@tasksData')->name('tasks_data_tour');
        Route::get('tour/{id}/clone', 'TourController@cloneTour')->name('tour_clone');
        Route::get('/tour/{id}/export/html', 'TourController@htmlExport')->name('tour_html_export');
        Route::get('/tour/{id}/doc/{doc_type}', 'TourController@docExport')->name('tour_doc_export');
        Route::get('/tour/{id}/export/{pdf_type}', 'TourController@pdfExport')->name('tour_pdf_export');
        Route::get('/tour/api/country_list', 'TourController@countryList');
        Route::get('/tour/api/status_list', 'TourController@statusList');
        Route::post('tour/{id}/updateCalendar', '\App\Http\Controllers\TourController@updateCalendarTour');
        Route::post('/change_tour_price', '\App\Http\Controllers\TourController@changeTourPrice');
        Route::post('/get_tour_days', '\App\Http\Controllers\TourController@getTourDays');
        Route::post('/get_tour_days_id', '\App\Http\Controllers\TourController@getTourDaysId');
        Route::get('/checkTourDayConfirmedHotel', '\App\Http\Controllers\TourController@checkTourDayConfirmedHotel');
        Route::post('/get_tour_days_id_hotel_service', '\App\Http\Controllers\TourController@getTourDaysIdHotelService');
	    Route::get('tour/{id}/convert_to_tour', 'TourController@convertToTour')->name('tour.convert_to_tour');
		Route::get('tour/{id}/convert_to_quotation', 'TourController@convertToQuotation')->name('tour.convertToQuotation');
		Route::post('/update_voucherid', '\App\Http\Controllers\TourController@update_voucherid');
        Route::post('/update_itnid', '\App\Http\Controllers\TourController@update_itnid');
		
    });


//task Routes
Route::group(['middleware'=> 'web'],function(){
	Route::get('/task/getTasksBlock', '\App\Http\Controllers\ScaffoldInterface\AppController@getTasksBlock');
	Route::resource('task','\App\Http\Controllers\TaskController');
	Route::post('task/{id}/update','\App\Http\Controllers\TaskController@update');
	Route::get('task/{id}/delete','\App\Http\Controllers\TaskController@destroy')->name('task.destroy');
    Route::get('task/{id}/deleteMsg','\App\Http\Controllers\TaskController@DeleteMsg')->name('task.deleteMsg');
    Route::get('task/{id}/delete/{tab}','\App\Http\Controllers\TaskController@destroy')->name('task_tab.destroy');
    Route::get('task/{id}/deleteMsg/{tab}','\App\Http\Controllers\TaskController@DeleteMsg');
	Route::post('task/{id}/updateCalendar', '\App\Http\Controllers\TaskController@updateCalendarTask');
	Route::get('/task/statuses/list', 'TaskController@statusesList');
	Route::get('/getallhollydaycalendars', '\App\Http\Controllers\ScaffoldInterface\AppController@getAllHollydayCalendars');
	Route::post('/checkHollydayCalendarById/{id}', '\App\Http\Controllers\ScaffoldInterface\AppController@checkHollydayCalendarById');
});


//status Routes
    Route::group(['middleware'=> 'web'],function(){
        Route::resource('status','\App\Http\Controllers\StatusController');
        Route::post('status/{id}/update','\App\Http\Controllers\StatusController@update');
        Route::get('status/{id}/delete','\App\Http\Controllers\StatusController@destroy')->name('status.destroy');
        Route::get('status/{id}/deleteMsg','\App\Http\Controllers\StatusController@DeleteMsg');
        Route::get('status/api/data', '\App\Http\Controllers\StatusController@data')->name('status_data');
    });
    
//holiday Routes
    Route::group(['middleware'=> 'web'],function(){
        Route::resource('holiday','\App\Http\Controllers\HolidayController');
        Route::post('holiday/{id}/update','\App\Http\Controllers\HolidayController@update');
        Route::get('holiday/{id}/delete','\App\Http\Controllers\HolidayController@destroy')->name('holiday.destroy');
        Route::get('holiday/{id}/deleteMsg','\App\Http\Controllers\HolidayController@DeleteMsg');
        Route::get('holiday/api/data', '\App\Http\Controllers\HolidayController@data')->name('holidaycalendar_data');
    });

//Room Types Routes
Route::group(['middleware'=> 'web'],function(){
    Route::resource('room_types','\App\Http\Controllers\RoomTypesController');
    Route::post('room_types/{id}/update','\App\Http\Controllers\RoomTypesController@update');
    Route::get('room_types/{id}/delete','\App\Http\Controllers\RoomTypesController@destroy')->name('room_types.destroy');
    Route::get('room_types/{id}/deleteMsg','\App\Http\Controllers\RoomTypesController@DeleteMsg');
    Route::get('room_types/api/data', '\App\Http\Controllers\RoomTypesController@data')->name('room_types_data');
});

    //Email Temlates
    Route::group(['middleware'=> 'web'],function(){
        Route::resource('templates','\App\Http\Controllers\TemplatesController');
        Route::post('templates/{id}/update','\App\Http\Controllers\TemplatesController@update');
        Route::post('templates/{id}/delete','\App\Http\Controllers\TemplatesController@destroy')->name('templates.destroy');
        Route::get('templates/api/data', '\App\Http\Controllers\TemplatesController@data')->name('templates_data');
        Route::get('templates/api/load', '\App\Http\Controllers\TemplatesController@loadTemplate');
		Route::get('desctemplates/api/load', '\App\Http\Controllers\TemplatesController@loadDescTemplate');
        Route::get('templates/api/loadServiceTemplates', '\App\Http\Controllers\TemplatesController@loadServiceTemplates');
        Route::post('templates/api/send', '\App\Http\Controllers\TemplatesController@sendTemplate');
		
		Route::any('templates/{userId}/emails/reply', '\App\Http\Controllers\TemplatesController@replyEmail');
    });

    //Agreements Routes
    Route::group(['middleware'=> 'web'],function(){
        Route::get('agreements/{id}/create','\App\Http\Controllers\AgreementsController@create')->name('create_agreements');
        Route::get('agreements/{hotel_id}/edit/{id}','\App\Http\Controllers\AgreementsController@edit')->name('edit_agreements');
        Route::get('agreements/{hotel_id}/delete/{id}','\App\Http\Controllers\AgreementsController@destroy')->name('delete_agreements');
        Route::post('agreements/store','\App\Http\Controllers\AgreementsController@store')->name('store_agreements');
        Route::post('agreements/update','\App\Http\Controllers\AgreementsController@update')->name('update_agreements');
        Route::get('agreements/kontingent','\App\Http\Controllers\AgreementsController@kontingent')->name('kontingent_agreements');
        Route::get('agreements/kontingent_save','\App\Http\Controllers\AgreementsController@kontingent_save')->name('kontingent_save');
        Route::get('agreements/kontingent_delete','\App\Http\Controllers\AgreementsController@kontingent_delete')->name('kontingent_delete');
        Route::post('/agreement_hotel_room_types','\App\Http\Controllers\AgreementsController@viewAgreementHotelRoomType');
    });

    //Season Routes
    Route::group(['middleware'=> 'web'],function(){
        Route::get('season/{id}/create','\App\Http\Controllers\SeasonsController@create')->name('create_season');
        Route::get('season/{hotel_id}/edit/{id}','\App\Http\Controllers\SeasonsController@edit')->name('edit_season');
        Route::get('season/{hotel_id}/delete/{id}','\App\Http\Controllers\SeasonsController@destroy')->name('delete_season');
        Route::post('season/store','\App\Http\Controllers\SeasonsController@store')->name('store_season');
        Route::post('season/update','\App\Http\Controllers\SeasonsController@update')->name('update_season');
        Route::post('/season_hotel_room_types','\App\Http\Controllers\SeasonsController@viewSeasonHotelRoomType');
    });


// Buses Routes
    Route::group(['middleware'=> 'web'],function(){
        Route::get('bus/calendar','\App\Http\Controllers\BusController@calendar')->name('bus_calendar');
        Route::resource('bus','\App\Http\Controllers\BusController');
        Route::post('bus/{id}/update','\App\Http\Controllers\BusController@update');
        Route::get('bus/{id}/delete','\App\Http\Controllers\BusController@destroy')->name('bus.destroy');
        Route::get('bus/{id}/deleteMsg','\App\Http\Controllers\BusController@DeleteMsg');
        Route::get('bus/api/data', '\App\Http\Controllers\BusController@data')->name('bus_data');
        Route::get('/driver_bus_transfer/api/{id}', '\App\Http\Controllers\BusController@getDriverAndBusTransfer');
        Route::get('/driver_bus_transfer/table/api/{id}', '\App\Http\Controllers\BusController@getDriverAndBusTransferForTable');
        Route::post('/bus_day/update', '\App\Http\Controllers\BusController@updateBusDay');
        Route::post('/bus_day/update/tour', '\App\Http\Controllers\BusController@updateBusDayTour');
        Route::post('/bus_day/delete', '\App\Http\Controllers\BusController@deleteBusDay');
    });

    // Bus Day Api
    Route::group(['middleware'=> 'web'],function(){
        Route::get('api/bus_days','\App\Http\Controllers\BusController@getApiBusDays');
        Route::post('api/add_day','\App\Http\Controllers\BusController@addApiBusDays');
        Route::get('api/get_buses_transfer/{id}','\App\Http\Controllers\BusController@getBusesTransfer');
        Route::get('api/get_drivers_transfer/{id}','\App\Http\Controllers\BusController@getDriversTransfer');
        Route::get('api/generate_form_trip','\App\Http\Controllers\BusController@generateFormTrip');
        Route::get('api/generate_form_tour','\App\Http\Controllers\BusController@generateFormTour');
    });




//Rates Routes
    Route::group(['middleware'=> 'web'],function(){
        Route::resource('rate','\App\Http\Controllers\RateController');
        Route::post('rate/{id}/update','\App\Http\Controllers\RateController@update');
        Route::get('rate/{id}/delete','\App\Http\Controllers\RateController@destroy')->name('rate.destroy');
        Route::get('rate/{id}/deleteMsg','\App\Http\Controllers\RateController@DeleteMsg');
        Route::get('rate/api/data', '\App\Http\Controllers\RateController@data')->name('rate_data');
    });


//Currency Rates Routes
Route::group(['middleware'=> 'web'],function(){
    Route::resource('currency_rate','\App\Http\Controllers\CurrencyRateController');
    Route::post('currency_rate/{id}/update','\App\Http\Controllers\CurrencyRateController@update');
    Route::get('currency_rate/{id}/delete','\App\Http\Controllers\CurrencyRateController@destroy')->name('currency_rate.destroy');
    Route::get('currency_rate/{id}/deleteMsg','\App\Http\Controllers\CurrencyRateController@DeleteMsg');
    Route::get('currency_rate/api/data', '\App\Http\Controllers\CurrencyRateController@data')->name('currency_rate_data');
});

// Notifications
    Route::get('/notifications/{id}/delete', '\App\Http\Controllers\NotificationsController@destroy')->name('notifications.destroy');
    Route::get('/notifications/{id}/deleteMsg', '\App\Http\Controllers\NotificationsController@deleteMsg');
    Route::post('/delete_notifications', '\App\Http\Controllers\NotificationsController@deleteNotification');
    Route::get('/read_all_notifications', '\App\Http\Controllers\NotificationsController@readAllNotifications');
    Route::get('/delete_all_notifications', '\App\Http\Controllers\NotificationsController@deleteAllNotifications');
    Route::get('/getNotifications', '\App\Http\Controllers\NotificationsController@getNotifications');

//Profile User
Route::group(['middleware'=> 'web'],function(){
    Route::get('profile','\App\Http\Controllers\ProfileController@show')->name('profile');
    Route::get('profile/edit','\App\Http\Controllers\ProfileController@edit');
    Route::get('profile/data/tasks','\App\Http\Controllers\ProfileController@dataTasks')->name('profile_tasks');
    Route::get('profile/data/tours','\App\Http\Controllers\ProfileController@dataTours')->name('profile_tours');
    Route::get('profile/data/notifications','\App\Http\Controllers\ProfileController@dataNotifications')->name('profile_notifications');
	Route::get('notification/show','\App\Http\Controllers\ProfileController@notification_show');
});


//Currencies Routes
    Route::group(['middleware'=> 'web'],function(){
        Route::resource('currencies','\App\Http\Controllers\CurrenciesController');
        Route::post('currencies/{id}/update','\App\Http\Controllers\CurrenciesController@update');
        Route::get('currencies/{id}/delete','\App\Http\Controllers\CurrenciesController@destroy')->name('currencies.destroy');
        Route::get('currencies/{id}/deleteMsg','\App\Http\Controllers\CurrenciesController@DeleteMsg');
        Route::get('currencies/api/data', '\App\Http\Controllers\CurrenciesController@data')->name('currencies_data');
    });

//Criteria Routes
Route::group(['middleware'=> 'web'],function(){
    Route::resource('criteria','\App\Http\Controllers\CriteriaController');
    Route::post('criteria/{id}/update','\App\Http\Controllers\CriteriaController@update');
    Route::get('criteria/{id}/delete','\App\Http\Controllers\CriteriaController@destroy')->name('criteria.destroy');
    Route::get('criteria/{id}/deleteMsg','\App\Http\Controllers\CriteriaController@DeleteMsg');
    Route::get('criteria/api/data', '\App\Http\Controllers\CriteriaController@data')->name('criteria_data');
});

//Comments Routes
    Route::group(['middleware' => 'web'], function () {
        Route::resource('comment', 'CommentController');
        Route::get('/comment/{id}/delete', 'CommentController@destroy')->name('comment.destroy');
        Route::get('/comment/{id}/delete_msg', 'CommentController@deleteMsg');
        Route::get('/comment/{id}/reply', 'CommentController@reply')->name('comment_reply');
        Route::post('/comment/generate-comments', 'CommentController@getComments');
    });

    Route::group(['middleware' => 'web', 'prefix' => 'users'], function () {
        Route::get('/{id}/deleteMsg', 'ScaffoldInterface\UserController@deleteMsg');
        Route::get('/{id}/delete', 'ScaffoldInterface\UserController@destroy')->name('user.destroy');
        Route::post('/removeRole', '\App\Http\Controllers\ScaffoldInterface\UserController@revokeRole')->name('user.remove_role');
        Route::post('/addRole', '\App\Http\Controllers\ScaffoldInterface\UserController@addRole');
        Route::post('/addPermission', '\App\Http\Controllers\ScaffoldInterface\UserController@addPermission');
        Route::get('/removePermission/{user_id}/{key}', '\App\Http\Controllers\ScaffoldInterface\UserController@revokePermission');

    });

    Route::group(['middleware' => 'web'], function () {
        Route::resource('cruises', 'CruisesController');
        Route::get('/cruises/{id}/delete', 'CruisesController@destroy')->name('cruise.destroy');
        Route::get('/cruises/{id}/delete_msg', 'CruisesController@deleteMsg');
        Route::get('/cruises/api/data', 'CruisesController@data')->name('cruises_data');
        Route::post('/cruises/{id}/api/attach', 'CruisesController@getAttach')->name('cruises_attach');
    });
    
    Route::group(['middleware' => 'web'], function () {
        Route::get('/images/index', 'AttachmentController@index')->name('images.index');
        Route::post('/images/savefile', 'AttachmentController@saveFile')->name('images.savefile');
//        Route::get('/cruises/{id}/delete_msg', 'CruisesController@deleteMsg');
    });

    Route::group(['middleware' => 'web'], function () {
        Route::get('/roles/{id}/deleteMsg', 'ScaffoldInterface\RoleController@deleteMsg');
        Route::get('/roles/{id}/delete', 'ScaffoldInterface\RoleController@destroy')->name('role.destroy');
    });
    Route::group(['middleware' => 'web'], function () {
        Route::get('/permissions/{id}/deleteMsg', 'ScaffoldInterface\PermissionController@deleteMsg');
        Route::get('/permissions/{id}/delete', 'ScaffoldInterface\PermissionController@destroy')->name('permission.destroy');
    });
    Route::post('roles/addPermission', '\App\Http\Controllers\ScaffoldInterface\RoleController@addPermission');
    Route::get('roles/removePermission/{permission}/{role_id}', '\App\Http\Controllers\ScaffoldInterface\RoleController@revokePermission');

    Route::group(['middleware' => 'web'], function () {
        Route::resource('flights', 'FlightController');
        Route::get('flights/{id}/delete', 'FlightController@destroy')->name('flight.destroy');
        Route::get('flights/{id}/delete_msg', 'FlightController@deleteMsg')->name('flights_delete_msg');
        Route::get('flights/api/data', 'FlightController@data')->name('flights.data');
        Route::post('/flights/{id}/api/attach', 'FlightController@getAttach')->name('flights_attach');
    });

    Route::group(['middleware' => 'web'], function(){
        Route::resource('settings', 'SettingController');
        Route::get('/settings/api/data', 'SettingController@data');
    });

    Route::get('/supplier_search', 'SupplierSearchController@index')->name('supplier_search');
    Route::get('/supplier_show', 'SupplierSearchController@show')->name('supplier_show');
    Route::get('/table_service_list', 'SupplierSearchController@generateTableServiceList');
    Route::get('/supplier_criteria', 'SupplierSearchController@getCriterias')->name('criterias_for_search');
    Route::group(['middleware' => 'web', 'prefix' => 'activities'], function(){
        Route::get('/', 'ActivitiesController@index')->name('activities_index');
        Route::get('/api/data', 'ActivitiesController@data')->name('activities_data');
    });
    Route::post('/file/{id}/delete', 'FileController@delete')->name('file_delete');
    Route::post('/import', 'ImportController@getFile')->name('file_import');
    Route::post('/import_seasons', 'ImportController@getFileSeasons')->name('file_import_seasons');
    Route::get('/import/check', 'ImportController@check')->name('file_import_check');
    Route::get('/import/modal', 'ImportController@getModal');
    Route::get('/import/checkServicesCity', 'ImportController@checkServicesCitiesCountries');
    Route::get('/export', 'ExportController@export')->name('export_data');
    Route::get('/export_seasons', 'ExportController@exportSeasons');
    Route::get('/services_datatables', 'ServicesController@datatable')->name('generate_datatable');

    Route::get('/services/{id}/history', 'ServicesController@showTourPackageHistory')->name('services_history');
});
Auth::routes();

Route::group(['middleware' => ['auth']], function () {
Route::get('/home', ['uses'        => '\App\Http\Controllers\ScaffoldInterface\AppController@dashboard',
                     'middleware'  => ['auth', 'permissions.required'],
                     'permissions' => 'dashboard.index',
                     'as'          => 'dashboard_main'
]);
});
Route::get('/home/getToursForCalendar', ['uses'        => '\App\Http\Controllers\ScaffoldInterface\AppController@getToursForCalendar',
                                         'middleware'  => ['auth', 'permissions.required'],
                                         'permissions' => 'dashboard.index',
                                         'as'          => 'dashboard.getToursForCalendar'
]);

Route::get('/home/getToursForCalendarByUser/{id}', ['uses'        => '\App\Http\Controllers\ScaffoldInterface\AppController@getToursForCalendar',
    'middleware'  => ['auth', 'permissions.required'],
    'permissions' => 'dashboard.index',
    'as'          => 'dashboard.getToursForCalendar'
]);

Route::get('/home/getToursTasksForCalendar', ['uses'        => '\App\Http\Controllers\ScaffoldInterface\AppController@getToursTasksForCalendar',
                                              'middleware'  => ['auth', 'permissions.required'],
                                              'permissions' => 'dashboard.index',
                                              'as'          => 'dashboard.getToursTasksForCalendar'
]);

Route::post('/calendar/quick-create', ['uses'        => '\App\Http\Controllers\ScaffoldInterface\AppController@quickCreateCalendarEvent',
                                       'middleware'  => ['auth', 'permissions.required'],
                                       'permissions' => 'dashboard.index',
                                       'as'          => 'calendar.quick-create'
]);
/*
Route::get('/home/getToursTasksForCalendar', ['uses'        => '\App\Http\Controllers\ScaffoldInterface\AppController@getToursByCountries',
    'middleware'  => ['auth', 'permissions.required'],
    'permissions' => 'dashboard.index',
    'as'          => 'dashboard.getToursByCountries'
]);*/

//Route::get('chat', ['uses'        => '\App\Http\Controllers\ChatController@index',
//                     'middleware'  => ['auth', 'permissions.required'],
//                     'permissions' => 'chat.index',
//                     'as'          => 'chat.index'
//]);

Route::post('chat/message', ['uses'        => '\App\Http\Controllers\ChatController@postMessage',
                    'middleware'  => ['auth', 'permissions.required'],
                    'permissions' => 'chat.post',
                    'as'          => 'chat.post'
]);

Route::group(['middleware' => 'web'], function () {
    Route::resource('announcements', 'AnnouncementController');
    Route::get('/announcement/{id}/delete', 'AnnouncementController@destroy')->name('announcement.destroy');
    Route::get('/announcement/{id}/delete_msg', 'AnnouncementController@deleteMsg')->name('announcement.deleteMsg');
    Route::get('/announcement/api/data', 'AnnouncementController@data')->name('announcements_data');
    Route::post('/announcement/{id}/reply', 'AnnouncementController@reply')->name('announcement_reply');
    Route::get('/announcement/{id}/generate-announcements', 'AnnouncementController@generateAnnouncements')->name('announcements_generate');
});

Route::group(['middleware' => 'web'], function () {
    Route::get('/chat/{id}/renderChat', 'ChatController@renderChat')->name('chat.renderChat');
    Route::get('/chat/main', 'ChatController@main')->name('chat.main');
	Route::get('/chat/{id}/deleteMsg', 'ChatController@delete')->name('chat.deleteMsg');
	Route::get('/chat/{id}/destroy_chat', 'ChatController@destroy')->name('chat.destroy_chat');
    Route::get('/chat/renderUsersForChat', 'ChatController@renderUsersForChat')->name('chat.renderUsersForChat');
    Route::get('/chat/{id}/getMessage', 'ChatController@getMessage')->name('chat.getMessage');
    Route::get('/chat/{id}/getNewChat', 'ChatController@getNewChat')->name('chat.getNewChat');
    Route::get('/chat/getOrCreateChat', 'ChatController@getOrCreateChat')->name('chat.getOrCreateChat');
    Route::get('/chat/renderCustomChatCreateFrom', 'ChatController@renderCustomChatCreateFrom')->name('chat.renderCustomChatCreateFrom');
    Route::get('/chat/renderCustomChatDeleteFrom', 'ChatController@renderCustomChatDeleteFrom')->name('chat.renderCustomChatDeleteFrom');
    Route::get('/chat/deleteChat', 'ChatController@deleteChat')->name('chat.deleteChat');
    Route::get('/chat/createCustomChat', 'ChatController@createCustomChat')->name('chat.createCustomChat');
    Route::get('/chat/{id}/renderUsersForCustomChat', 'ChatController@renderUsersForCustomChat')->name('chat.renderUsersForCustomChat');
    Route::get('/chat/{id}/renderDirectChat', 'ChatController@renderDirectChat')->name('chat.renderDirectChat');
    Route::get('/chat/addUserToCustomChat', 'ChatController@addUserToCustomChat')->name('chat.addUserToCustomChat');
    Route::get('/chat/removeUserFromChat', 'ChatController@removeUserFromChat')->name('chat.removeUserFromChat');
    Route::get('/chat/getChatNotifications', 'ChatController@getChatNotifications')->name('chat.getChatNotifications');
    Route::resource('chat', 'ChatController');
});

Route::group(['middleware' => 'web'], function () {
	Route::resource( 'users', '\App\Http\Controllers\ScaffoldInterface\UserController' );
	Route::post( '/users/{user_id}', '\App\Http\Controllers\ScaffoldInterface\UserController@update' );
});
Route::group(['middleware' => 'web'], function () {
	Route::resource( 'roles', '\App\Http\Controllers\ScaffoldInterface\RoleController' );
    Route::post('/roles/store', 'ScaffoldInterface\RoleController@store');
	Route::post('/roles/update', 'ScaffoldInterface\RoleController@update');
});
Route::group(['middleware' => 'web'], function () {
	Route::resource( 'permissions', '\App\Http\Controllers\ScaffoldInterface\PermissionController' );
    Route::post('/permissions/store', 'ScaffoldInterface\PermissionController@store');
    Route::post('/permissions/update', 'ScaffoldInterface\PermissionController@update');
});

//Emails Routes
Route::group(['middleware' => 'web'], function () {
	Route::get('email/parseEmails', 'EmailController@parseEmails')->name('email.parseEmails');
	Route::get('email/ajax/mail', 'EmailController@parseEmails')->name('email.ajaxMail');
	Route::get('email/another', 'EmailController@another')->name('email.another');
    Route::get('email/readAll', 'EmailController@readAll')->name('email.readAll');
	Route::get('email/search', 'EmailController@emailSearch')->name('email.search_result');
	Route::post('email/search', 'EmailController@emailSearch')->name('email.search');
	Route::post('email/send', 'EmailController@send')->name('email.send');
	Route::any('users/{userId}/emails/sending', 'EmailController@sendingEmail');
	Route::any('users/{userId}/emails/reply', 'EmailController@replyEmail');
	Route::any('users/{id}/emails/delete', 'EmailController@deleteEmail');
	Route::get('email/getComposeForm', 'EmailController@getComposeForm')->name('email.getComposeForm');
	Route::get('email/getMoveToForm', 'EmailController@getMoveToForm')->name('email.getMoveToForm');
	Route::post('email/moveEmail', 'EmailController@moveEmail')->name('email.moveEmail');
	Route::post('email/addFolder', 'EmailController@addFolder')->name('email.addFolder');
	Route::get('email/template', 'EmailController@composeEmailTemplate')->name('email.template');
	Route::get('email/addFolderForm', 'EmailController@addFolderForm')->name('email.addFolderForm');
	Route::get('email/{page?}', 'EmailController@index')->name('email.index');
	Route::get('email/folder/{name}/{page?}', 'EmailController@folder')->name('email.folder')
        ->where('name', '.*');
	Route::get('email/mail/{id}/{currentFolder?}', 'EmailController@mail')->name('email.mail')
        ->where('currentFolder', '.*');


    Route::get('email/ajax/mail/{id}/{currentFolder?}', 'EmailController@ajaxMail')->name('email.ajaxMail')
        ->where('currentFolder', '.*');


	Route::get('email/deleteMsg/{id}/{currentFolder?}', 'EmailController@emailDeleteMsg')->name('email.deleteMsg')
        ->where('currentFolder', '.*');
	Route::get('email/remove/{id}/{currentFolder?}', 'EmailController@removeEmail')->name('email.remove')
        ->where('currentFolder', '.*');
	Route::get('email/attachment/{folderName}/{id}/{attachmentName}', 'EmailController@attachment')->name('email.attachment')
        ->where('folderName', '.*');
	Route::get('email/attachmentList/{folderName}/{id}', 'EmailController@attachmentList')->name('email.attachmentList')
        ->where('folderName', '.*');
});



//Driver Routes
Route::group(['middleware' => 'web'], function () {
	Route::resource('driver', '\App\Http\Controllers\DriverController');
    Route::get('/driver/{id}/delete', 'DriverController@destroy')->name('driver.destroy');
    Route::get('/driver/{id}/delete_msg', 'DriverController@deleteMsg');
	Route::get('/driver/api/data', 'DriverController@data')->name('driver.data');
});

Route::group(['middleware' => 'web'], function () {
    Route::post('roomlist/update', 'RoomListController@update')->name('roomlist.update');
    Route::get('roomlist/{id}/create', 'RoomListController@create')->name('roomlist.add');
    Route::get('roomlist/{id}/show', 'RoomListController@show')->name('roomlist.show');
    Route::get('roomlist/{id}/send', 'RoomListController@send')->name('roomlist.send');
});

Route::group(['middleware' => 'web'], function () {
	Route::get('quotation/{id}/create', 'QuotationController@create')->name('quotation.add');
	Route::get('quotation/add_column_message', 'QuotationController@addColumnMessage')->name('quotation.add_column_message');
	Route::get('quotation/{id}/pdf', 'QuotationController@pdf')->name('quotation.pdf');
	Route::get('quotation/{id}/excel', 'QuotationController@excel')->name('quotation.excel');
	Route::post('quotation/{id}/save', 'QuotationController@save')->name('quotation.save');
	Route::post('quotation/{id}/updateQuotation', 'QuotationController@update')->name('quotation.updateQuotation');
	Route::post('quotation/{id}/confirm', 'QuotationController@confirm')->name('quotation.confirm');
    Route::post('quotation/{id}/confirm_cancel', 'QuotationController@confirm_cancel')->name('quotation.confirm_cancel');
	Route::resource('quotation', 'QuotationController');
});

Route::group(['middleware' => 'web'], function () {
	Route::get('guestlist/{id}/create', 'GuestListController@create')->name('guestList.add');
	Route::get('guestlist/{id}/showbyid', 'GuestListController@showById')->name('guestList.showbyid');
	Route::get('guestlist/{id}/showhotelemailsbyid', 'GuestListController@showHotelEmailsById')->name('guestList.showhotelemailsbyid');
	Route::get('guestlist/{id}/send/{guestlistid?}', 'GuestListController@send')->name('guestlist.send');
	Route::get('guestlist/{id}/delete/{guestlistid?}', 'GuestListController@delete')->name('guestlist.delete');
	Route::resource('guestlist', 'GuestListController');
});

Route::group(['middleware' => 'web'], function () {
	Route::resource('comparison', 'ComparisonController');
	Route::get('comparison/{id}/comments', 'ComparisonController@comments')->name('comparison.comments');
});

Route::group(['middleware' => 'web'], function () {
    Route::get('places', 'ImportController@addPlaces')->name('config_places');
	Route::get('/places/{name}', 'ImportController@addPlaces')->name('add_places');
	Route::get('/menu/destroy/{id}', 'MenuController@destroy')->name('menu.destroy_menu');
	Route::get('/menu/delete/{id}', 'MenuController@delete')->name('menu.delete');
	Route::resource('menu', 'MenuController');	
});

Route::get('/tour/{id}/landingpage', 'TourController@landingPage')->name('landing_page');


Route::get('getemailbyId/{id}/{mailtype}', 'EmailController@getEmailById')->name('getemailbyId');
Route::get('tp/getaddEmails', '\App\Http\Controllers\BookingRequestController@getaddEmails')->name('tour_package.getAdditionEmails');
Route::get('booking/{generatedlink}/{id}', '\App\Http\Controllers\BookingRequestController@generated_link');
Route::get('offer/{id}/show', 'OfferController@show')->name('show');
Route::get('offer/{id}/supplier_delete', 'OfferController@supplier_delete')->name('supplier_delete');
Route::get('/offer/api/status_list', 'OfferController@statusList');
Route::put('offer//updatestatus/{id}', 'OfferController@updatestatus')->name('offerupdatestatus');
Route::post('tour_package/{id}/offer_update', '\App\Http\Controllers\BookingRequestController@offerUpdate')->name('tour_package.offer_update');
Route::post('tour_package/{id}/addemails', '\App\Http\Controllers\BookingRequestController@addemails')->name('tour_package.addemails');


Route::get('TMS-Client/login', '\App\Http\Controllers\TMSClient\LoginController@index')->name('TMS-Client.login');
Route::post('TMS-Client/login', '\App\Http\Controllers\TMSClient\LoginController@Clientauth')->name('client.login');

Route::group(['middleware' => 'clientauth'], function () {
Route::get('TMS-Client/logout', '\App\Http\Controllers\TMSClient\LoginController@signout')->name('client.logout');
Route::get('TMS-Client/home', '\App\Http\Controllers\TMSClient\LoginController@home')->name('TMS-Client.home');
Route::get('TMS-Client/quotation_requests', '\App\Http\Controllers\TMSClient\LoginController@quotation_requests')->name('TMS-Client.quotation_requests');
 Route::resource('client_tour_package', '\App\Http\Controllers\TMSClient\TourPackageController');
Route::resource('TMS-Client-tours', '\App\Http\Controllers\TMSClient\TourController');
	Route::get('TMS-Client-simpletours/create', '\App\Http\Controllers\TMSClient\TourController@simple_create');
Route::get('client_tour_data/api/data', '\App\Http\Controllers\TMSClient\TourController@tour_data')->name('client_tour_data');
//Route::post('/customLogin', '\App\Http\Controllers\TMSClient\LoginController@customLogin')->name('custom.login');
Route::any('/sevice_modal/show/{id}', '\App\Http\Controllers\TMSClient\ModalController@show')->name('service_modal.show');
 Route::post('/searchPackageData', '\App\Http\Controllers\TMSClient\ModalController@getData');
 Route::post('/dropdownPackageData', '\App\Http\Controllers\TMSClient\ModalController@getdropdownData');
	Route::post('/selectionPackageData', '\App\Http\Controllers\TMSClient\ModalController@selectionPackageData');
Route::post('TMS-Client/file_viewer', '\App\Http\Controllers\TMSClient\UploadController@file_viewer');
	Route::post('TMS-Client/uploadTourFile', '\App\Http\Controllers\TMSClient\UploadController@uploadTourFile');
	Route::get('TMS-Client/downloadSampleExcel', '\App\Http\Controllers\TMSClient\UploadController@downloadSampleExcel');
});


Route::get('TMS-Supplier/login', '\App\Http\Controllers\TMSSupplier\LoginController@index')->name('TMS-Supplier.login');
Route::post('TMS-Supplier/login', '\App\Http\Controllers\TMSSupplier\LoginController@Supplierauth')->name('supplier.login');
Route::get('TMS-Supplier/logout', '\App\Http\Controllers\TMSSupplier\LoginController@signout')->name('supplier.logout');
Route::group(['middleware' => 'supplierauth'], function () {
Route::get('TMS-Supplier/home', '\App\Http\Controllers\TMSSupplier\LoginController@home')->name('TMS-Supplier.home');
//Route::get('offer_data/api/data', '\App\Http\Controllers\TMSSupplier\OfferController@data_request')->name('offer_data');
	
});
Route::post('TMS-Supplier/add_comment/{id}', '\App\Http\Controllers\TMSSupplier\OfferController@addComment')->name('add_comment');
Route::get('city_list/{country_code}', 'TourController@get_cities');

Route::get('check', function(){
   
    return view( 'email.email_index', [
       
        'imapConnected' => true//$this->imapConnected
    ] );
	
	

});
	
Auth::routes();


