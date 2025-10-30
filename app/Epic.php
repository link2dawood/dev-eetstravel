<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Epic extends Model
{
    protected $guarded = [];
    protected $table = 'epics';

    protected $fillable = [
        'name',
        'description',
        'color',
        'sort_order'
    ];

    public function tasks()
    {
        return $this->hasMany('App\Task', 'epic_id');
    }

    public function getTaskCountAttribute()
    {
        return $this->tasks()->count();
    }

    public function getCompletedTasksAttribute()
    {
        return $this->tasks()->whereHas('status', function ($query) {
            $query->where('is_completed', true);
        })->count();
    }

    public function getProgressPercentageAttribute()
    {
        $total = $this->task_count;
        if ($total == 0) return 0;

        return round(($this->completed_tasks / $total) * 100);
    }
}
