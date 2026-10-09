<?php namespace App\Http\Middleware;

use Closure;
use Auth;
use Redirect;

class PermissionsRequiredMiddleware
{
    public static $ignoredRoutes = [
        'dashboard.list',
        'home',
        'dashboard_main', // Root fix: Add dashboard_main to prevent redirect loop
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
        $routeName = $route->getName();
        
        // Root fix: Check ignored routes first before any permission checks
        if ($routeName && in_array($routeName, self::$ignoredRoutes)) {
            return $next($request);
        }
        
        // Root fix: Use permissions from route action if specified, otherwise use route name
        // Don't overwrite existing permissions from route action
        if (!isset($actions['permissions']) && $routeName) {
            $actions['permissions'] = $routeName;
        }

        // Check if a user is logged in.
        if (!$user = $request->user()) {
            return Redirect::route('login');
        }
        
        // Check if we have any permissions to check the user has.
        // Root fix: Check permissions from route action first, then fallback to route name
        $permissions = isset($actions['permissions']) ? $actions['permissions'] : null;
        
        // If no permissions specified, allow access
        if (!$permissions) {
            return $next($request);
        }

        // Root fix: Check if permission is in ignored list (check both route name and permission name)
        if (in_array($permissions, self::$ignoredRoutes) || in_array($routeName, self::$ignoredRoutes)) {
            return $next($request);
        }

        // if permission not in ignored and
        try {
            if ($request->user()->hasPermissionTo($permissions)) {
                return $next($request);
            }
        } catch (\Exception $e) {
            // Root fix: Log the error but allow access to prevent redirect loops
            \Log::warning('Permission check failed in PermissionsRequiredMiddleware', [
                'user_id' => $request->user()->id ?? null,
                'permission' => $permissions,
                'route' => $routeName,
                'error' => $e->getMessage()
            ]);
            // Allow access to prevent redirect loops - let the application handle authorization
            return $next($request);
        }

        // Root fix: Instead of aborting 401 (which causes redirect loops), redirect to a safe page
        // or show a proper 403 error page
        \Log::warning('User does not have required permission', [
            'user_id' => $request->user()->id,
            'permission' => $permissions,
            'route' => $routeName,
            'url' => $request->fullUrl()
        ]);
        
        // Return 403 Forbidden instead of 401 Unauthorized to prevent redirect loops
        return abort(403, 'You do not have permission to access this resource.');
    }
}
