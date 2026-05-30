<?php namespace App\Http\Middleware;

use Closure;
use Auth;
use Redirect;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;

class PermissionsRequiredMiddleware
{
    public static $ignoredRoutes = [
        'dashboard.list',
        'home',
        'dashboard_main', // Root fix: Add dashboard_main to prevent redirect loop
        'landing_page',
    ];

    /**
     * Role names that bypass missing-permission failures. When Spatie throws
     * PermissionDoesNotExist (because the seeder for that module was never
     * written — see AUDIT.md CC2), we deny by default for ordinary users
     * but let these elevated roles through so admins can still operate the
     * unseeded modules.
     */
    public static $adminRolesBypass = ['admin', 'Super User'];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $route = $request->route();
        $actions = $route->getAction();
        $routeName = $route->getName();

        // Allowlisted routes (login redirect target, home, etc.)
        if ($routeName && in_array($routeName, self::$ignoredRoutes)) {
            return $next($request);
        }

        // Don't overwrite explicit `permissions` set on the route action;
        // fall back to the route name as the permission slug.
        if (!isset($actions['permissions']) && $routeName) {
            $actions['permissions'] = $routeName;
        }

        if (!$user = $request->user()) {
            return Redirect::route('login');
        }

        $permissions = $actions['permissions'] ?? null;

        // No permission slug → allow.
        if (!$permissions) {
            return $next($request);
        }

        // Defence-in-depth allowlist (matches either by route name or by
        // permission slug — some legacy routes use different names for each).
        if (in_array($permissions, self::$ignoredRoutes) || in_array($routeName, self::$ignoredRoutes)) {
            return $next($request);
        }

        // CC1 fix: instead of fail-OPEN on every exception, fail CLOSED by
        // default. Two narrow exceptions:
        //
        //  1. PermissionDoesNotExist — Spatie throws this when the slug isn't
        //     seeded. Most modules in this codebase have no permission seeder
        //     (CC2), so denying here would lock everyone out. As a stopgap
        //     we let elevated roles (`admin`, `Super User`) through so the
        //     system stays operable while CC2 is being addressed.
        //  2. Any other exception — log it and deny. We do NOT want a
        //     transient DB hiccup to silently disarm authorization.
        try {
            if ($user->hasPermissionTo($permissions)) {
                return $next($request);
            }
        } catch (PermissionDoesNotExist $e) {
            \Log::warning('Permission slug not seeded — falling back to role check', [
                'user_id'    => $user->id,
                'permission' => $permissions,
                'route'      => $routeName,
            ]);
            if ($user->hasAnyRole(self::$adminRolesBypass)) {
                return $next($request);
            }
            return abort(403, 'You do not have permission to access this resource.');
        } catch (\Throwable $e) {
            \Log::error('Permission middleware exception — denying', [
                'user_id'    => $user->id,
                'permission' => $permissions,
                'route'      => $routeName,
                'error'      => $e->getMessage(),
                'exception'  => get_class($e),
            ]);
            return abort(403, 'You do not have permission to access this resource.');
        }

        // Permission was checked successfully and the user does not have it.
        \Log::warning('User does not have required permission', [
            'user_id'    => $user->id,
            'permission' => $permissions,
            'route'      => $routeName,
            'url'        => $request->fullUrl(),
        ]);

        return abort(403, 'You do not have permission to access this resource.');
    }
}
