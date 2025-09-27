<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 29.03.18
 * Time: 10:51
 */

namespace App\Helper;


use App\Announcement;
use App\Bus;
use App\Chat;
use App\Client;
use App\Comment;
use App\Criteria;
use App\Cruises;
use App\Currencies;
use App\CurrencyRate;
use App\Driver;
use App\Email;
use App\Event;
use App\Flight;
use App\GuestList;
use App\Guide;
use App\Holidaycalendarday;
use App\Hotel;
use App\HotelAgreements;
use App\Http\Controllers\Api\DashboardController;
use App\Menu;
use App\Notification;
use App\Quotation;
use App\Rate;
use App\Restaurant;
use App\RoomTypes;
use App\Seasons;
use App\Setting;
use App\Status;
use App\Task;
use App\Tour;
use App\TourPackage;
use App\Transfer;
use App\User;
use App\ClientInvoices;
use App\Invoices;
use App\Offices;
use Auth;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

trait PermissionHelper
{
    public static $relationsPermissionDestroy = [
        Activity::class => 'activities.destroy',
        Tour::class => 'tour.destroy',
        User::class => 'users.destroy',
        Role::class => 'roles.destroy',
        Permission::class => 'permissions.destroy',
        Task::class => 'task.destroy',
        Event::class => 'event.destroy',
        Restaurant::class => 'restaurant.destroy',
        Hotel::class => 'hotel.destroy',
        Guide::class => 'guide.destroy',
        Flight::class => 'flight.destroy',
        Cruises::class => 'cruise.destroy',
        Transfer::class => 'transfer.destroy',
        Bus::class => 'bus.destroy',
        Driver::class => 'driver.destroy',
        Status::class => 'status.destroy',
        RoomTypes::class => 'room_types.destroy',
        Rate::class => 'rate.destroy',
        CurrencyRate::class => 'currency_rate.destroy',
        Currencies::class => 'currencies.destroy',
        Criteria::class => 'criteria.destroy',
        Chat::class => 'chat.destroy_user',
        Email::class => 'email.destroy',
        Comment::class => 'comment.destroy',
        Client::class => 'client.destroy',
        TourPackage::class => 'tour_package.destroy',
        GuestList::class => 'guest_list.destroy',
        HotelAgreements::class => 'agreements.destroy',
        Menu::class => 'menu.destroy',
        Seasons::class => 'season_price.destroy',
        Announcement::class => 'announcement.destroy',
        Holidaycalendarday::class => 'holiday.destroy',
        Notification::class => 'notification.destroy',
		ClientInvoices::class => 'transactions.destroy',
		Invoices::class => 'invoices.destroy',
		Offices::class => 'office.destroy',
    ];

    public static $relationsPermissionEdit = [
        Activity::class => 'activities.edit',
        Tour::class => 'tour.edit',
        Task::class => 'task.edit',
        Event::class => 'event.edit',
        Restaurant::class => 'restaurant.edit',
        Hotel::class => 'hotel.edit',
        Guide::class => 'guide.edit',
        Flight::class => 'flights.edit',
        Cruises::class => 'cruises.edit',
        Transfer::class => 'transfer.edit',
        Bus::class => 'bus.edit',
        Driver::class => 'driver.edit',
        Status::class => 'status.edit',
        RoomTypes::class => 'room_types.edit',
        Rate::class => 'rate.edit',
        CurrencyRate::class => 'currency_rate.edit',
        Currencies::class => 'currencies.edit',
        Criteria::class => 'criteria.edit',
        Chat::class => 'chat.edit',
        Comment::class => 'comment.edit',
        Client::class => 'clients.edit',
        TourPackage::class => 'tour_package.edit',
        HotelAgreements::class => 'edit_agreements',
        Menu::class => 'menu.edit',
        Seasons::class => 'edit_season',
        Announcement::class => 'announcements.edit',
        Quotation::class => 'quotation.edit',
        Holidaycalendarday::class => 'holiday.edit',
		ClientInvoices::class => 'transactions.edit',
		Invoices::class => 'invoices.edit',
		Offices::class => 'office.edit',
		
    ];


    public static $relationsPermissionShow = [
        Activity::class => 'activities.show',
        Tour::class => 'tour.show',
        Task::class => 'task.show',
        Event::class => 'event.show',
        Restaurant::class => 'restaurant.show',
        Hotel::class => 'hotel.show',
        Guide::class => 'guide.show',
        Flight::class => 'flights.show',
        Cruises::class => 'cruises.show',
        Transfer::class => 'transfer.show',
        Bus::class => 'bus.show',
        Driver::class => 'driver.show',
        Status::class => 'status.show',
        RoomTypes::class => 'room_types.show',
        Rate::class => 'rate.show',
        CurrencyRate::class => 'currency_rate.show',
        Currencies::class => 'currencies.show',
        Criteria::class => 'criteria.show',
        Chat::class => 'chat.show',
        Comment::class => 'comment.show',
        Client::class => 'clients.show',
        TourPackage::class => 'tour_package.show',
        Menu::class => 'menu.show',
        Announcement::class => 'announcements.show',
        Holidaycalendarday::class => 'holiday.show',
		ClientInvoices::class => 'transactions.show',
		Invoices::class => 'invoices.show',
		Offices::class => 'office.show',
    ];

    public static $relationsPermissionCreate = [
        Activity::class => 'activities.create',
        Tour::class => 'tour.create',
        Task::class => 'task.create',
        Event::class => 'event.create',
        Restaurant::class => 'restaurant.create',
        Hotel::class => 'hotel.create',
        Guide::class => 'guide.create',
        Flight::class => 'flights.create',
        Cruises::class => 'cruises.create',
        Transfer::class => 'transfer.create',
        Bus::class => 'bus.create',
        Driver::class => 'driver.create',
        Status::class => 'status.create',
        RoomTypes::class => 'room_types.create',
        Rate::class => 'rate.create',
        CurrencyRate::class => 'currency_rate.create',
        Currencies::class => 'currencies.create',
        Criteria::class => 'criteria.create',
        Chat::class => 'chat.create',
        Comment::class => 'comment.create',
        Client::class => 'clients.create',
        TourPackage::class => 'tour_package.create',
        Menu::class => 'menu.create',
        Announcement::class => 'announcements.create',
        Holidaycalendarday::class => 'holiday.create',
		ClientInvoices::class => 'transactions.create',
		Invoices::class => 'invoices.create',
		Offices::class => 'office.create',
    ];

    public static $relationsPermissionIndex = [
        Activity::class => 'activities.index',
        Tour::class => 'tour.index',
        Task::class => 'task.index',
        Event::class => 'event.index',
        Restaurant::class => 'restaurant.index',
        Hotel::class => 'hotel.index',
        Guide::class => 'guide.index',
        Flight::class => 'flights.index',
        Cruises::class => 'cruises.index',
        Transfer::class => 'transfer.index',
        Bus::class => 'bus.index',
        Driver::class => 'driver.index',
        Status::class => 'status.index',
        RoomTypes::class => 'room_types.index',
        Rate::class => 'rate.index',
        CurrencyRate::class => 'currency_rate.index',
        Currencies::class => 'currencies.index',
        Criteria::class => 'criteria.index',
        Chat::class => 'chat.index',
        Comment::class => 'comment.index',
        Client::class => 'clients.index',
        TourPackage::class => 'tour_package.index',
        Menu::class => 'menu.index',
        Announcement::class => 'announcements.index',
        Holidaycalendarday::class => 'holiday.index',
		ClientInvoices::class => 'transactions.index',
		Invoices::class => 'invoices.index',
		Offices::class => 'office.index',
    ];
    public static $relationsPermissionDashboard = [
        DashboardController::DASHBOARD_ACTIVITIES => 'dashboard.activities',
        DashboardController::DASHBOARD_ANNOUNCEMENTS => 'dashboard.announcements',
        DashboardController::DASHBOARD_CHAT_GROUPS => 'dashboard.chat_groups',
        DashboardController::DASHBOARD_INBOX => 'dashboard.inbox',
        DashboardController::DASHBOARD_TASKS_CALENDAR => 'dashboard.calendar',
        DashboardController::DASHBOARD_TASKS => 'dashboard.tasks',
        DashboardController::DASHBOARD_TOUR_CALENDAR => 'dashboard.tours_calendar',
        DashboardController::DASHBOARD_LATEST_TOURS => 'dashboard.latest_tours',

    ];


    public static function checkPermission($permission){
        if(Auth::user()->can($permission)){
            return true;
        }else{
            return false;
        }
    }


    public static function getActionsButton($obj, array $url){
        $permission_destroy = PermissionHelper::$relationsPermissionDestroy[get_class($obj)];
        $permission_edit = PermissionHelper::$relationsPermissionEdit[get_class($obj)];
        $permission_show = PermissionHelper::$relationsPermissionShow[get_class($obj)];
        $action = "<div style='width:150px; text-align: center;' class='buttons_margin'>";

        // show button
        if(Auth::user()->can($permission_show)){
            $action .= "<a href=".$url['show']." class='btn btn-warning btn-sm show-button' 
            data-link=".$url['show']."><i class=\"fa fa-info-circle\" aria-hidden=\"true\"></i></a>";
        }

        // edit button
        if(Auth::user()->can($permission_edit)){
            $action .= "<a href=".$url['edit']." class='btn btn-primary btn-sm edit-button' 
            data-link=".$url['edit']."><i class=\"fa fa-pencil-square-o\" aria-hidden=\"true\"></i></a>";
        }

        // destroy button
        if (Auth::user()->can($permission_destroy)){
            $action .= "<a data-toggle=\"modal\" data-target=\"#myModal\" class='btn btn-danger btn-sm delete' 
            data-link=".$url['deleteMsg']."><i class=\"fa fa-trash-o\" aria-hidden=\"true\"></i></a>";
        }

        $action .= "</div>";
        return $action;
    }

    public static function getCreateButton($route, $class){
        $permissionCreate = PermissionHelper::$relationsPermissionCreate[$class];
        $action = "";
        if(Auth::user()->can($permissionCreate)){
            $action = "<a class='btn btn-success' href='$route'>
                <i class=\"fa fa-plus fa-md\" aria-hidden=\"true\"></i> New</a>";
        }

        return $action;
    }


    public static function getActionPermission($class,$userId){
        $permission_index = PermissionHelper::$relationsPermissionIndex[$class];
        $permission_show = PermissionHelper::$relationsPermissionShow[$class];
        $permission_create = PermissionHelper::$relationsPermissionCreate[$class];
        $permission_edit = PermissionHelper::$relationsPermissionEdit[$class];
        $permission_destroy = PermissionHelper::$relationsPermissionDestroy[$class];
        $action = [];
        $user = User::findOrFail($userId);
        $action['index'] = $user->can($permission_index);
        $action['create'] = $user->can($permission_create);
        $action['show'] = $user->can($permission_show);
        $action['edit'] = $user->can($permission_edit);
        $action['destroy'] = $user->can($permission_destroy);

        return $action;
    }
}