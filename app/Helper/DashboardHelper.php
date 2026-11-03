<?php

namespace App\Helper;

use App\Email;
use App\Task;
use Auth;

class DashboardHelper
{

    public static function getTasks()
    {
        $user = Auth::user();
        $now = now()->toDateTimeString();
        $activeTasks = function ($query) use ($now) {
            $query->where('tasks.dead_line', '>=', $now)
                ->where(function ($query) {
                    $query->whereNull('tasks.status')
                        ->orWhereNotIn('tasks.status', [7, 8]);
                });
        };

        $tasks = $user->tasks()
            ->where($activeTasks)
            ->get()
            ->keyBy('id');

        $assignedTasks = Task::query()
            ->where('assign', $user->id)
            ->where($activeTasks)
            ->orderBy('priority', 'DESC')
            ->orderBy('dead_line', 'ASC')
            ->get();

        foreach ($assignedTasks as $task) {
            $tasks->put($task->id, $task);
        }
        $tasks = array_values($tasks->all());

        // The header shows each task's tour name; load those tours in one query
        \App\Task::preloadNotificationTours($tasks);

        return $tasks;
    }


    public static function getCountUnreadMailMessage(){
        $user = Auth::user();
        $user_login = $user->email_login;
        $email_server = $user->email_server;

        return Email::query()
            ->where('user_login', $user_login)
            ->where('email_server', $email_server)
            ->where('is_seen', false)
            ->count();
    }

    public static function isTabActive($route)
    {
        return \Route::currentRouteName() == $route ? 'active': '';
    }

    public static function isMenuActive($route, $currentRoute)
    {

        if (!is_string($route) || !is_string($currentRoute)) return '';
        if ($currentRoute === $route) return 'active';
        if (substr($route, -6) === '.index') {
            $module = substr($route, 0, -6);
            if (strpos($currentRoute, $module.'.') === 0) return 'active';
        }
        return '';
    }


    public static function isTabTreeViewActive($routes)
    {

        return in_array(\Route::currentRouteName(), $routes);
    }

    public static function isMenuTreeViewActive($routes, $currentRoute)
    {

        foreach ($routes as $route) {
            if (self::isMenuActive($route, $currentRoute) === 'active') return true;
        }
        return false;
    }
}
