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
