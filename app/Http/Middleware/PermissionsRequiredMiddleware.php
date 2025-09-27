<?php namespace App\Http\Middleware;

use Closure;
use Auth;
use Redirect;

class PermissionsRequiredMiddleware
{
    public static $ignoredRoutes = [
        'dashboard.list',
        'home',
        'landing_page',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Get the current route.
        $route = $request->route();

        // Get the current route actions.
        $actions = $route->getAction();
        if ($routeName = $route->getName()) {
            $actions['permissions'] = $routeName;
        }
        
        if (in_array($routeName, self::$ignoredRoutes)) {
            return $next($request);
        }

        // Check if a user is logged in.
        if (!$user = $request->user()) {
            return Redirect::route('login');
        }
        
        // Check if we have any permissions to check the user has.
        if (!$permissions = isset($actions['permissions']) ? $actions['permissions'] : null) {
            // No permissions to check, allow access.
            return $next($request);
        }

        // if permission not in ignored and
        try {
            if ($request->user()->hasPermissionTo($permissions)) {
                return $next($request);
            }
        } catch (\Exception $e) {
            return $next($request);
        }


        return abort(401);
    }
}
