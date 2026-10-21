<?php

namespace App\Http\Middleware;

use Closure;

class ApiPermissions
{
    public function handle($request, Closure $next)
    {
        $user = $request->user();
        abort_unless($user, 401);
        abort_if($user->is_active === false, 403);
        // Legacy widgets must use the session identity, never a supplied user ID.
        $target = $request->route('userId');
        abort_if($target !== null && (string) $target !== (string) $user->getAuthIdentifier(), 403);
        $request->merge(['userId' => $user->getAuthIdentifier()]);

        $controller = class_basename(explode('@', $request->route()->getActionName())[0]);
        $method = $request->route()->getActionMethod();
        $modules = ['TourApiController' => 'tour', 'ClientApiController' => 'clients',
            'TaskApiController' => 'task', 'UserApiController' => 'users',
            'DashboardApiController' => 'dashboard', 'SettingsApiController' => 'settings', 'DashboardController' => 'dashboard',
            'EmailsController' => 'email'];
        $module = $modules[$controller] ?? null;
        abort_unless($module, 403);
        $action = ['store' => 'create', 'clone' => 'create', 'update' => 'edit',
            'updateStatus' => 'edit', 'destroy' => 'destroy', 'show' => 'show'][$method] ?? 'index';
        $permission = $module . '.' . $action;
        if ($module === 'email') $permission = in_array($method, ['getTours', 'getArchiveTours']) ? 'tour.index' : 'email.main';
        if ($module === 'settings' || $method === 'clearCache') $permission = 'settings.index';
        if ($method === 'getTaskCreatePopup') $permission = 'task.create';
        if ($method === 'getModalAddTour') $permission = 'tour.create';
        if ($module === 'users' && in_array($method, ['tasks', 'tours'])) $permission = $method === 'tasks' ? 'task.index' : 'tour.index';
        if ($module === 'clients' && $method === 'tours') $permission = 'tour.index';
        abort_unless($user->can($permission), 403);

        if ($method === 'bulkAction') {
            $bulkPermission = ['delete' => 'tour.destroy', 'update_status' => 'tour.edit', 'assign_user' => 'tour.edit'][$request->input('action')] ?? null;
            abort_unless($bulkPermission && $user->can($bulkPermission), 403);
        }
        return $next($request);
    }
}
