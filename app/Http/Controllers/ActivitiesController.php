<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use Yajra\Datatables\Datatables;

class ActivitiesController extends Controller
{

    public function data(){
        return Datatables::of(Activity::distinct()
        ->leftJoin('users', 'users.id', '=', 'activity_log.causer_id')
        ->select('activity_log.description', 'activity_log.properties', 'activity_log.created_at', 'users.name as causer')->orderBy('created_at', 'desc'))
            ->addColumn('action', function($activity){
        	return $activity->getExtraProperty('action');
        })->addColumn('description', function($activity){
        	// $link = $activity->getExtraProperty('link');
        	// if ($link) {
        	// 	return $link = "$activity->description";
        	// }
        	return $activity->description;
        })->addColumn('link', function($activity){
            $link = $activity->getExtraProperty('link');
            if ($link) {
                return $link = "<a class='btn btn-warning btn-sm pull-right' href='$link'><i class='fa fa-info-circle'></i></a>";
            }
        })->rawColumns(['description', 'link'])->make(true);
    }

    public function index(){
    	$logs = Activity::all();

        // Get all activities data (same as the AJAX data method)
        $activitiesData = Activity::distinct()
            ->leftJoin('users', 'users.id', '=', 'activity_log.causer_id')
            ->select('activity_log.description', 'activity_log.properties', 'activity_log.created_at', 'users.name as causer')
            ->orderBy('created_at', 'desc')
            ->get();

        // Add action and link columns to each activity
        $activitiesData->each(function ($activity) {
            $activity->action = $activity->getExtraProperty('action');
            $activity->formatted_description = $activity->description;

            $link = $activity->getExtraProperty('link');
            if ($link) {
                $activity->link_button = "<a class='btn btn-warning btn-sm pull-right' href='$link'><i class='fa fa-info-circle'></i></a>";
            } else {
                $activity->link_button = '';
            }
        });

    	return view('activities.index', ['logs' => $logs, 'activitiesData' => $activitiesData]);
    }
}
