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

    const TYPE_PERSONAL = 1;

    /**
     * Personal tasks are private to their creator and assignees; admins see every task.
     */
    public function scopeVisibleTo($query, $user)
    {
        if ($user->hasRole('admin')) {
            return $query;
        }

        return $query->where(function ($q) use ($user) {
            $q->where('tasks.task_type', '<>', self::TYPE_PERSONAL)
                ->orWhere('tasks.assign', $user->id)
                ->orWhereHas('assigned_users', function ($u) use ($user) {
                    $u->where('users.id', $user->id);
                });
        });
    }

    public function isVisibleTo($user)
    {
        return (int) $this->task_type !== self::TYPE_PERSONAL
            || $user->hasRole('admin')
            || (int) $this->assign === (int) $user->id
            || $this->assigned_users()->where('users.id', $user->id)->exists();
    }

    /**
     * Tasks on a user's own to-do list: assigned to them, or created by them.
     */
    public function scopeForUser($query, $user)
    {
        return $query->where(function ($q) use ($user) {
            $q->where('tasks.assign', $user->id)
                ->orWhereHas('assigned_users', function ($u) use ($user) {
                    $u->where('users.id', $user->id);
                });
        });
    }

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

    public function isOverdue()
    {
        if (!$this->dead_line) {
            return false;
        }
        return Carbon::parse($this->dead_line)->isPast();
    }

    public function tour()
    {
        return $this->belongsTo('App\Tour', 'tour');
    }

    public function tourModel()
    {
        return $this->belongsTo('App\Tour', 'tour');
    }

    public function epic()
    {
        return $this->belongsTo('App\Epic', 'epic_id');
    }

    public function tourName()
    {
        $tourId = $this->getAttribute('tour');
        if (!$tourId) {
            return null;
        }

        $tour = $this->relationLoaded('tour')
            ? $this->getRelation('tour')
            : Tour::find($tourId);

        return $tour ? $tour->name : null;
    }

    /** @var array<int, Tour|null> Tours preloaded by preloadNotificationTours(), keyed by id */
    private static $notificationTours = [];

    /**
     * Load the tours of many tasks in one query before tourNameNotification() runs per task.
     */
    public static function preloadNotificationTours($tasks)
    {
        $ids = collect($tasks)->pluck('tour')->filter()->unique()->reject(function ($id) {
            return array_key_exists($id, self::$notificationTours);
        });
        if ($ids->isEmpty()) {
            return;
        }
        $tours = Tour::whereIn('id', $ids)->get()->keyBy('id');
        foreach ($ids as $id) {
            self::$notificationTours[$id] = $tours->get($id); // null = deleted tour
        }
    }

    public function tourNameNotification()
    {
        if ($this->tour){
            // Use eager loaded relationship to avoid N+1 queries
            $tour = $this->relationLoaded('tour') ? $this->tour
                : (array_key_exists($this->tour, self::$notificationTours) ? self::$notificationTours[$this->tour] : Tour::find($this->tour));
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
        return $value ? Carbon::parse($value)->format('Y-m-d H:i') : null;
    }

    // Removed getAssignAttribute accessor to prevent conflicts
    // Use $task->assignedTo relationship instead of $task->assign for displaying names

    public function showAssignedUsers()
    {
        $list = '';
        foreach ($this->assigned_users as $user) {
            $list .= $user->name . ' ';
        }
        return $list;
    }

    /**
     * Check if the task is overdue
     *
     * @return bool
     */
    public function isOverdueOriginal()
    {
        if (!$this->dead_line) {
            return false;
        }

        // Check if deadline has passed and task is not completed
        $deadline = Carbon::parse($this->dead_line);
        $isCompleted = $this->status && isset($this->status->is_completed) && $this->status->is_completed;
        
        return $deadline->isPast() && !$isCompleted;
    }
}