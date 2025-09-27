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
        $tasks = $user->getTasksAttachedToUser();
        $dt = new \DateTime();
        $dead = $dt->format('Y-m-d H:i:s');
        foreach ($tasks as $i => $task){
            if($task->dead_line < $dead || $task->status == 7 || $task->status == 8){
                unset($tasks[$i]);
            }
        }

        return $tasks;
    }


    public static function getCountUnreadMailMessage(){
        $user = Auth::user();
        $user_login = $user->email_login;
        $email_server = $user->email_server;

        $messages = Email::query()
            ->where('user_login', $user_login)
            ->where('email_server', $email_server)
            ->where('is_seen', false)
            ->get();

        return $messages->isNotEmpty() ? $messages->count() : 0;
    }

    public static function isTabActive($route)
    {
        return \Route::currentRouteName() == $route ? 'active': '';
    }

    public static function isMenuActive($route, $currentRoute)
    {

        return $currentRoute == $route ? 'active': '';
    }


    public static function isTabTreeViewActive($routes)
    {

        return in_array(\Route::currentRouteName(), $routes);
    }

    public static function isMenuTreeViewActive($routes, $currentRoute)
    {

        return in_array($currentRoute, $routes);
    }
}