<?php

namespace App\Http\Controllers;


use App\Helper\PermissionHelper;
use App\Notification;
use Auth;

class DatatablesHelperController extends Controller
{
    public static function getActionButton(array $url, $isQuotation = false, $obj)
    {
        $permission_destroy = PermissionHelper::$relationsPermissionDestroy[get_class($obj)];
        $permission_edit = PermissionHelper::$relationsPermissionEdit[get_class($obj)];
        $permission_show = PermissionHelper::$relationsPermissionShow[get_class($obj)];

//        $action = "<div style='width:150px; text-align: center;'>";
        $btnCnt = 0;
        $action = '';
        // show button
        if(Auth::user()->can($permission_show)){
            $action .= "<a class='btn btn-warning btn-sm show-button' href=\"{$url['show']}\" data-link=\"{$url['show']}\"><i class='fa fa-info-circle'></i></a>";
            $btnCnt++;
        }

        // edit button
        if(Auth::user()->can($permission_edit)){
            $action .= "<a class='btn btn-primary btn-sm' href=\"{$url['edit']}\" data-link=\"{$url['edit']}\"><i class='fa fa-pencil-square-o'></i></a>";
            $btnCnt++;
        }

        // destroy button
        if (Auth::user()->can($permission_destroy)){
            $action .= "<a class='delete btn btn-danger btn-sm' style='margin-right: 5px' data-toggle=\"modal\" data-target=\"#myModal\" data-link=\"{$url['delete_msg']}\"><i class='fa fa-trash-o'></i></a>";
            $btnCnt++;
        }

        // clone button for tour
        if (Auth::user()->can('tour.create') && isset($url['id']) && !$isQuotation){
            $action .= "<button class='btn btn-success btn-sm clone-tour-button' data-toggle='modal' data-id='{$url['id']}' data-target='#tour-clone-modal' style='display: none'>" .
            "<i class='fa fa-plus'></i></button>";
            $btnCnt++;
        };
        IF($btnCnt > 1){
            $action = "<div style='width:150px; text-align: center;'>" . $action;
        } else{
            $action = "<div style='width:50px; text-align: center;'>" . $action;
        }
        
        $action .= "</div>";
        return $action;
    }
    
    public static function getActionButtonTours(array $url, $isQuotation = false, array $perm)
    {
//        $permission_destroy = PermissionHelper::$relationsPermissionDestroy[get_class($obj)];
//        $permission_edit = PermissionHelper::$relationsPermissionEdit[get_class($obj)];
//        $permission_show = PermissionHelper::$relationsPermissionShow[get_class($obj)];

        $btnCnt = 0;
        $action = '';
        // show button
        if($perm['show']){
            $action .= "<a class='btn btn-warning btn-sm show-button' href=\"{$url['show']}\" data-link=\"{$url['show']}\"><i class='fa fa-info-circle'></i></a>";
            $btnCnt++;
        }

        // edit button
        if($perm['edit']){
            $action .= "<a class='btn btn-primary btn-sm' href=\"{$url['edit']}\" data-link=\"{$url['edit']}\"><i class='fa fa-pencil-square-o'></i></a>";
            $btnCnt++;
        }

        // destroy button
        if ($perm['destroy']){
            $action .= "<a class='delete btn btn-danger btn-sm' style='margin-right: 5px' data-toggle=\"modal\" data-target=\"#myModal\" data-link=\"{$url['delete_msg']}\"><i class='fa fa-trash-o'></i></a>";
            $btnCnt++;
        }

        // clone button for tour
//        if (Auth::user()->can('tour.create') && isset($url['id']) && !$isQuotation){
        if ($perm['clone']  && isset($url['id'])){
            $action .= "<button class='btn btn-success btn-sm clone-tour-button' data-toggle='modal' data-id='{$url['id']}' data-target='#tour-clone-modal' style='display: none'>" .
            "<i class='fa fa-plus'></i></button>";
            $btnCnt++;
        };
        IF($btnCnt > 1){
            $action = "<div style='width:150px; text-align: center;'>" . $action;
        } else{
            $action = "<div style='width:50px; text-align: center;'>" . $action;
        }
        
        $action .= "</div>";
        return $action;
    }

    public static function getSelectButton(int $id, $name = null)
    {
    	return $select = "<button class='btn btn-default tour_package_select_button' data-id=\"{$id}\" data-name=\"{$name}\">Select</button>";
    }

    public static function getQuotationListButtons(array $url, $quotation){

        $permission_edit = PermissionHelper::$relationsPermissionEdit[get_class($quotation)];

        $action = "";
            if(Auth::user()->can($permission_edit)){
                $action .= "<a class='btn btn-primary btn-sm' href=\"{$url['edit']}\" data-link=\"{$url['edit']}\"><i class='fa fa-pencil-square-o'></i></a>";
            }

            $action .= "<a class='btn btn-primary btn-sm' target=\"_blank\" href=\"{$url['print']}\" data-link=\"{$url['print']}\"><i class=\"fa fa-print\" aria-hidden=\"true\"></i></a>";
        $action .= "</div>";
        return $action;
    }

    public static function getEditButton(array $url)
    {
        $action =
            "<a class='btn btn-primary btn-sm' href=\"{$url['edit']}\" data-link=\"{$url['edit']}\"><i class='fa fa-pencil-square-o'></i></a>";
        $action .= "</div>";
        return $action;
    }

    public static function getShowButton(array $url)
    {

        $action =
            "<a class='btn btn-warning btn-sm show-button' href=\"{$url['show']}\" data-link=\"{$url['show']}\"><i class='fa fa-info-circle'></i></a>";
        $action .= "</div>";
        return $action;
    }

    public static function getActionButtonForProfileNotification(array $url, $obj)
    {
        $permission_destroy = PermissionHelper::$relationsPermissionDestroy[get_class($obj)];
//        $permission_edit = PermissionHelper::$relationsPermissionEdit[get_class($obj)];
//        $permission_show = PermissionHelper::$relationsPermissionShow[get_class($obj)];

        $action = "<div style='text-align: right;'>";
            $action .= "<a class='btn btn-warning btn-sm show-button' href=\"{$url['show']}\" data-link=\"{$url['show']}\"><i class='fa fa-info-circle'></i></a>";
        
        if(Auth::user()->can($permission_destroy)){
            $action .= "<a class='delete btn btn-danger btn-sm' style='margin-right: 5px' data-toggle=\"modal\" data-target=\"#myModal\" data-link=\"{$url['delete_msg']}\"><i class='fa fa-trash-o'></i></a>";
        }
        $action .= "</div>";
        return $action;
    }
}
