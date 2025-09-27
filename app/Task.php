<?php

namespace App;

use App\Helper\Trackable;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Task extends Model
{

    //use Trackable;

    protected $guarded = [];

    public static $taskTypes = [
        '1' => 'Personal',
        '2' => 'In general'
    ];

    public static $statusColors = [
        '1' => '#f39c12',
        '2' => '#45a163',
        '3' => '#ff9300',
        '4' => '#b90000'
    ];

    protected $table = 'tasks';

	public function files()
	{
		return $this->hasMany('App\File');
	}

    public function assigned_users()
    {
        return $this->belongsToMany('App\User');
    }

    public function assignedTo()
    {
        return $this->belongsTo('App\User', 'assign');
    }

    public function status(){
        return $this->belongsTo('App\Status', 'status');
    }

    public function getStatusName()
    {
        $status = Status::find($this->status);
        return $status ? $status->name : 'Unknown';
    }

    public function getStatusColor()
    {
        $status = Status::find($this->status);
        return $status ? $status->color : '#cccccc';
    }

    public function tour()
    {
        return $this->belongsTo('App\Tour', 'tour');
    }

    public function tourModel()
    {
        return $this->belongsTo('App\Tour', 'tour');
    }

    public function tourName()
    {

        if ($this->tour){
            $tour = Tour::find($this->tour);
            if ($tour) return $tour->name;
        }else{
            return null;
        }
    }

    public function tourNameNotification()
    {

        if ($this->tour){
            $tour = Tour::query()->where('id', $this->tour)->first();
            if ($tour){
                return "Task {$this->content} for tour " . $tour->name;
            }else{
                return "For this task {$this->content} tour deleted";
            }
        }else{
            return "Task {$this->content} without tour";
        }
    }

    public function tourLinkShow()
    {
        return $this->tour != null ? route('tour.show', ['tour' => $this->tour]) : '';
    }

    public function getDeadLineAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d H:i');
    }

    public function getAssignAttribute($value)
    {
        if (!is_numeric($value)) return $value;
        else {
            return User::find($value)->name;
        }
    }

    /*public function getTourAttribute($value)
    {
        if ($value){
            $tour = Tour::find($value);
            if ($tour) return $tour->name;
        }   
    }*/ // todo What to do this function??

    public function showAssignedUsers()
    {
        $list = '';
        foreach ($this->assigned_users as $user) {
            $list .= $user->name . ' ';
        }
        return $list;
    }
}
