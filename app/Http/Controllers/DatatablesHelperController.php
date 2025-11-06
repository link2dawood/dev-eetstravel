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

        $btnCnt = 0;
        $action = '';
        
        // show button
        if(Auth::user()->can($permission_show)){
            $action .= "<a class='btn btn-warning btn-sm show-button' href=\"{$url['show']}\" data-link=\"{$url['show']}\"><i class='ti ti-eye'></i></a>";
            $btnCnt++;
        }

        // edit button
        if(Auth::user()->can($permission_edit)){
            $action .= "<a class='btn btn-primary btn-sm' href=\"{$url['edit']}\" data-link=\"{$url['edit']}\"><i class='ti ti-edit'></i></a>";
            $btnCnt++;
        }

        // destroy button
        if (Auth::user()->can($permission_destroy)){
            $action .= "<a class='delete btn btn-danger btn-sm' data-bs-toggle=\"modal\" data-bs-target=\"#myModal\" data-link=\"{$url['delete_msg']}\"><i class='ti ti-trash'></i></a>";
            $btnCnt++;
        }

        // clone button for tour
        if (Auth::user()->can('tour.create') && isset($url['id']) && !$isQuotation){
            $action .= "<button class='btn btn-success btn-sm clone-tour-button' data-bs-toggle='modal' data-bs-target='#tour-clone-modal' data-id='{$url['id']}'>" .
            "<i class='ti ti-copy'></i></button>";
            $btnCnt++;
        }
        
        // Calculate width based on button count
        $width = $btnCnt > 1 ? '150px' : '50px';
        $action = "<div style='width:{$width}; text-align: center;'>" . $action . "</div>";
        
        return $action;
    }
    
    public static function getActionButtonTours(array $url, $isQuotation = false, array $perm)
    {
        $btnCnt = 0;
        $action = '';
        
        // show button
        if($perm['show']){
            $action .= "<a class='btn btn-warning btn-sm show-button' href=\"{$url['show']}\" data-link=\"{$url['show']}\"><i class='ti ti-eye'></i></a>";
            $btnCnt++;
        }

        // edit button
        if($perm['edit']){
            $action .= "<a class='btn btn-primary btn-sm' href=\"{$url['edit']}\" data-link=\"{$url['edit']}\"><i class='ti ti-edit'></i></a>";
            $btnCnt++;
        }

        // destroy button
        if ($perm['destroy']){
            $action .= "<a class='delete btn btn-danger btn-sm' data-bs-toggle=\"modal\" data-bs-target=\"#myModal\" data-link=\"{$url['delete_msg']}\"><i class='ti ti-trash'></i></a>";
            $btnCnt++;
        }

        // clone button for tour
        if ($perm['clone'] && isset($url['id'])){
            $action .= "<button class='btn btn-success btn-sm clone-tour-button' data-bs-toggle='modal' data-bs-target='#tour-clone-modal' data-id='{$url['id']}'>" .
            "<i class='ti ti-copy'></i></button>";
            $btnCnt++;
        }
        
        // Calculate width based on button count
        $width = $btnCnt > 1 ? '150px' : '50px';
        $action = "<div style='width:{$width}; text-align: center;'>" . $action . "</div>";
        
        return $action;
    }

    public static function getSelectButton(int $id, $name = null)
    {
    	return $select = "<button class='btn btn-default tour_package_select_button' data-id=\"{$id}\" data-name=\"{$name}\">Select</button>";
    }

    public static function getQuotationListButtons(array $url, $quotation){
        $permission_edit = PermissionHelper::$relationsPermissionEdit[get_class($quotation)];

        $action = "<div style='width:100px; text-align: center;'>";
        if(Auth::user()->can($permission_edit)){
            $action .= "<a class='btn btn-primary btn-sm' href=\"{$url['edit']}\" data-link=\"{$url['edit']}\"><i class='ti ti-edit'></i></a>";
        }

        $action .= "<a class='btn btn-primary btn-sm' target=\"_blank\" href=\"{$url['print']}\" data-link=\"{$url['print']}\"><i class='ti ti-printer'></i></a>";
        $action .= "</div>";
        return $action;
    }

    public static function getEditButton(array $url)
    {
        $action = "<div style='width:50px; text-align: center;'>";
        $action .= "<a class='btn btn-primary btn-sm' href=\"{$url['edit']}\" data-link=\"{$url['edit']}\"><i class='ti ti-edit'></i></a>";
        $action .= "</div>";
        return $action;
    }

    public static function getShowButton(array $url)
    {
        $action = "<div style='width:50px; text-align: center;'>";
        $action .= "<a class='btn btn-warning btn-sm show-button' href=\"{$url['show']}\" data-link=\"{$url['show']}\"><i class='ti ti-eye'></i></a>";
        $action .= "</div>";
        return $action;
    }

    public static function getActionButtonForProfileNotification(array $url, $obj)
    {
        $permission_destroy = PermissionHelper::$relationsPermissionDestroy[get_class($obj)];

        $action = "<div style='text-align: right;'>";
        $action .= "<a class='btn btn-warning btn-sm show-button' href=\"{$url['show']}\" data-link=\"{$url['show']}\"><i class='ti ti-eye'></i></a>";

        if(Auth::user()->can($permission_destroy)){
            $action .= "<a class='delete btn btn-danger btn-sm' data-bs-toggle=\"modal\" data-bs-target=\"#myModal\" data-link=\"{$url['delete_msg']}\"><i class='ti ti-trash'></i></a>";
        }
        $action .= "</div>";
        return $action;
    }
}
