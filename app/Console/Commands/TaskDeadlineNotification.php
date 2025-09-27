<?php

namespace App\Console\Commands;

use App\Helper\SettingsHelper;
use App\Notification;
use App\Repository\Contracts\TaskRepository;
use App\Setting;
use App\Status;
use App\Task;
use App\User;
use Carbon\Carbon;
use function GuzzleHttp\Psr7\uri_for;
use Illuminate\Console\Command;

class TaskDeadlineNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:deadline';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add notification if task has deadline by some time';
    /**
     * @var TaskRepository
     */
    private $taskRepository;

    /**
     * Create a new command instance.
     *
     * @param TaskRepository $taskRepository
     */
    public function __construct(TaskRepository $taskRepository)
    {
        parent::__construct();
        $this->taskRepository = $taskRepository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $users = User::query()->get();
        $setting_task = Setting::query()
            ->where('name', SettingsHelper::TASK_DEADLINE)
            ->first();
        $days_to_deadline = $setting_task ? $setting_task->value : SettingsHelper::TASK_DEADLINE_DEFAULT_TIME;
        $currentDay = Carbon::now();
        $interval_date = Carbon::now()->addDays($days_to_deadline);
        $tasks = collect($this->taskRepository->tasksPeriodDays($interval_date, $currentDay));
        $completedStatus = Status::query()
            ->where('type', 'task')
            ->where('name', 'Completed')->first();

        $users_id = collect();


        if($users->isNotEmpty()){
            foreach ($users as $user){
                $users_id->push($user->id);
            }
        }else{
            return false;
        }

        if($tasks->isNotEmpty()){
            foreach ($tasks as $task){
                if($completedStatus ? $completedStatus->id != $task->status : true){
                    foreach ($users_id as $user_id){
                        foreach ($task->assigned_users as $assigned_user){
                            if($assigned_user->id == $user_id){
                                $url = route('task.show', ['task' => $task->id]);
                                $parsingURL = parse_url($url);
                                $uri = $parsingURL['path'];

                                $user = $users->filter(function ($item) use ($user_id){
                                    return $item->id == $user_id;
                                })->first();

                                if($user){
                                    $now = Carbon::now();
                                    $end = Carbon::parse($task->dead_line);
                                    $date_diff = $end->diffInDays($now);

                                    $notification = Notification::query()
                                        ->create([
                                            'content' => "Task {$task->content} deadline end through {$date_diff} days",
                                            'link' => $uri
                                        ]);

                                    $user->notifications()->attach($notification);
                                }
                            }
                        }
                    }
                }
            }
        }

        return false;
    }
}
