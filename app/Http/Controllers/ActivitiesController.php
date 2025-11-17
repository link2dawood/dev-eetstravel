<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use Yajra\Datatables\Datatables;

class ActivitiesController extends Controller
{

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
                $activity->link_button = "<a class='btn btn-sm btn-warning' href='$link' title='View'><svg xmlns='http://www.w3.org/2000/svg' class='icon' width='24' height='24' viewBox='0 0 24 24' stroke-width='2' stroke='currentColor' fill='none' stroke-linecap='round' stroke-linejoin='round'><path stroke='none' d='M0 0h24v24H0z' fill='none'/><path d='M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0' /><path d='M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6' /></svg></a>";
            } else {
                $activity->link_button = '';
            }
        });

    	return view('activities.index', ['logs' => $logs, 'activitiesData' => $activitiesData]);
    }
}
