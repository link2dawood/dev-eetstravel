<?php

namespace App\Repository\TaskRepository;

use App\Repository\Contracts\TaskRepository;
use App\Status;
use App\Task;

class EloquentTaskRepository implements TaskRepository
{
    const STATUS_PENDING = 2;
    private $current_date;

    public function __construct()
    {
        $this->current_date = date('Y-m-d', time());
    }

    public function all($limit = null)
    {

        //return Task::where('dead_line', '<', $this->current_date)
       return Task::orderBy('priority', 'ASC')
           ->orderBy('dead_line', 'ASC')->limit($limit)
            ->get();

    }

    public function allForAssigned(int $user_id, $limit = null)
    {
        return Task::whereHas('assigned_users', function ($query) use ($user_id) {
            $query->where('user_id', $user_id);
        })->orWhere('assign', $user_id)
            //->orderBy('status', 'ASC')
            ->orderBy('priority', 'DESC')
            ->orderBy('dead_line', 'ASC')
            //->where('dead_line', '<', $this->current_date)
            ->limit($limit)
            ->get();
    }

    public function getAllTaskForDashboard(int $user_id, $limit = null){
        $builder = Task::query();

        $statusPending = Status::query()
            ->where('type', 'task')
            ->where('name', 'Pending')->first();

        if($statusPending){
            $builder->where('status', $statusPending->id);
        }
        return $builder
            ->whereHas('assigned_users', function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })
            ->orderBy('priority', 'DESC')
            ->orderBy('dead_line', 'ASC')
            ->limit($limit)
            ->get();
    }
    
    public function allForAssignedToTour(int $user_id, int $tour_id, $limit = null)
    {
//        $statusPending = Status::query()
//            ->where('type', 'task')
//            ->where('name', 'Pending')->first();

        $builder = Task::query();

//        if($statusPending){
//            $builder->where('status', $statusPending->id);
//        }

        if(\Auth::user()->hasRole('admin')){
                $builder->where('assign', $user_id);
        }

       $builder = $builder
            ->where('tour', $tour_id)
            //->whereHas('assigned_users', function ($query) use ($user_id) {
              //  $query->where('user_id', $user_id);
            //})
            ->orderBy('priority', 'DESC')
            ->orderBy('dead_line', 'ASC')
            ->limit($limit)
            ->get();

        if(!\Auth::user()->hasRole('admin')) {
            $array = array();
            foreach ($builder as $task) {

                if (count($task->assigned_users) == 0) {
                    array_push($array, $task);
                } else {
                    foreach ($task->assigned_users as $user) {
                        if ($user->id == $user_id) array_push($array, $task);
                    }
                }
            }

            return $array;
        }

        return $builder;
    }

    public function tourTasks(int $tour_id)
    {
        return Task::where('tour', $tour_id)->get();
    }

    public function findPending(int $limit = NULL)
    {
        return Task::where('status', '=', self::STATUS_PENDING)
          //  ->where('dead_line', '<', $this->current_date)
            ->limit($limit)->orderBy('priority', 'DESC')->orderBy('dead_line', 'ASC')->get();
    }

    public function findAssignedPending(int $user_id, int $limit)
    {
        Task::where('status', '=', self::STATUS_PENDING)
            ->whereHas('assigned_users', function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })->orWhere('assign', $user_id)->limit($limit)
            ->orderBy('priority', 'DESC')->orderBy('dead_line', 'ASC')->get();
            //->where('dead_line', '<', $this->current_date)

    }


    public function tasksPeriodDays($date, $currentDate){
        return Task::query()
            ->where('dead_line', '>', $currentDate)
            ->where('dead_line', '<', $date)
            ->get();
    }
}
