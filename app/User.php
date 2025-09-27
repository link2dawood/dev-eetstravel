<?php

namespace App;

use Auth;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\CausesActivity;

/**
 * App\User
 *
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Permission[]                           $permissions
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Role[]                                 $roles
 * @method static \Illuminate\Database\Query\Builder|\App\User role( $roles )
 * @mixin \Eloquent
 * @property int                                                                                                            $id
 * @property string                                                                                                         $name
 * @property string                                                                                                         $email
 * @property string                                                                                                         $password
 * @property string                                                                                                         $remember_token
 * @property \Carbon\Carbon                                                                                                 $created_at
 * @property \Carbon\Carbon                                                                                                 $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\User whereCreatedAt( $value )
 * @method static \Illuminate\Database\Query\Builder|\App\User whereEmail( $value )
 * @method static \Illuminate\Database\Query\Builder|\App\User whereId( $value )
 * @method static \Illuminate\Database\Query\Builder|\App\User whereName( $value )
 * @method static \Illuminate\Database\Query\Builder|\App\User wherePassword( $value )
 * @method static \Illuminate\Database\Query\Builder|\App\User whereRememberToken( $value )
 * @method static \Illuminate\Database\Query\Builder|\App\User whereUpdatedAt( $value )
 * @property string|null $avatar_path
 * @property string|null $education
 * @property string|null $location
 * @property string|null $note
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Activitylog\Models\Activity[] $activity
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Announcement[] $announcements
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Chat[] $chats
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Comment[] $comments
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Task[] $tasks
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Tour[] $tours
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereAvatarPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereEducation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereNote($value)
 */
class User extends Authenticatable
{
    use Notifiable;
    use HasRoles;
    use CausesActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar_path',
        'lastUID',
        'education',
        'location',
        'note'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the avatar URL attribute.
     * Replaces the old Stapler avatar functionality.
     */
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar_path) {
            return asset('storage/' . $this->avatar_path);
        }
        
        // Default avatar or placeholder
        return asset('images/default-avatar.png');
    }

    /**
     * Get avatar in different sizes (replaces Stapler styles)
     */
    public function getAvatarUrl($size = 'medium')
    {
        if ($this->avatar_path) {
            $path = 'storage/' . $this->avatar_path;
            
            // You can implement image resizing here if needed
            // For now, return the original image
            return asset($path);
        }
        
        return asset('images/default-avatar.png');
    }

    public function comments()
    {
        return $this->hasMany('App\Comment');
    }

    public function announcements()
    {
        return $this->hasMany('App\Announcement', 'id', 'author');
    }

    public function chats()
    {
        return $this->belongsToMany('App\Chat', 'chat_user', 'id');
    }

    public function tours()
    {
        return $this->belongsToMany('App\Tour');
    }

    public function tasks()
    {
        return $this->belongsToMany('App\Task');
    }

    public function notifications()
    {
        return $this->belongsToMany('App\Notification');
    }

    public function getTasksAttachedToUser()
    {
        $tasks = collect();
        foreach ($this->tasks as $task) {
            $tasks->put($task->id, $task);
        }

        $tasksAssigned = Task::query()->orderBy('priority', 'DESC')
            ->orderBy('dead_line', 'ASC')->where('assign', $this->id)->get();

        foreach ($tasksAssigned as $task) {
            $tasks->put($task->id, $task);
        }
        return array_values($tasks->all());
    }

    public function getToursAttachedToUser()
    {
        $tours = collect();
        foreach ($this->tours as $tour) {
            $tours->put($tour->id, $tour);
        }

        $toursAssigned = Tour::query()->where('author', $this->id)->get();
        foreach ($toursAssigned as $tour) {
            $tours->put($tour->id, $tour);
        }
        return array_values($tours->all());
    }

    public function getTasksAttachedToUserForCalendar($startDate, $endDate)
    {
        $tasks = collect();
        foreach ($this->tasks as $task) {
            if ($task->status != 7) {
                $tasks->put($task->id, $task);
            }
        }

        $tasksAssigned = Task::query()
            ->where('assign', $this->id)
            ->where('dead_line', '>=', $startDate ?? "")
            ->where('dead_line', '<=', $endDate ?? "")
            ->where('status', '!=', '7')
            ->addSelect('tour as tour_id', 'tasks.*')
            ->get();

        foreach ($tasksAssigned as $task) {
            $tasks->put($task->id, $task);
        }
        return array_values($tasks->all());
    }

    /**
     * check if user have new notification
     */
    public function checkNotification()
    {
        foreach ($this->notifications as $notification) {
            if (!$notification->click) {
                return true;
            }
        }
    }

    /**
     * count how many new notification user has
     * return mixed
     */
    public function countNotification()
    {
        $count = null;
        foreach ($this->notifications as $notification) {
            if (!$notification->click) $count++;
        }
        return $count;
    }

    public function getToursAttachedToUserForCalendar($startDate, $endDate, $id)
    {
        $tours = collect();

        foreach ($this->tours as $tour) {
            $tours->put($tour->id, $tour);
        }

        $toursAssigned = Tour::query()
            ->where('departure_date', '>=', $startDate)
            ->where('departure_date', '<=', $endDate)
            ->where('author', $id)
            ->orWhere('assigned_user', $id)
            ->whereColumn('departure_date', '<=', 'retirement_date')
            ->get();

        foreach ($toursAssigned as $tour) {
            $tours->put($tour->id, $tour);
        }
        return array_values($tours->all());
    }
}