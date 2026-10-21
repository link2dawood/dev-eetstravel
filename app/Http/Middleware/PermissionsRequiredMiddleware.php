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
        $usesRoutePermission = !isset($actions['permissions']);
        
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

        // Billing permissions use the legacy transactions.* names throughout PermissionHelper.
        // Resolve route aliases to those same permissions so visible actions can be opened.
        $accountingAliases = [
            'accounting.create' => 'transactions.create', 'accounting.store' => 'transactions.create',
            'accounting.show' => 'transactions.show', 'accounting.edit' => 'transactions.edit',
            'accounting.update' => 'transactions.edit', 'accounts.update' => 'transactions.edit',
            'accounting.destroy' => 'transactions.destroy', 'accounts.destroy' => 'transactions.destroy',
            'add__invoice_payment' => 'transactions.create', 'inv_payment.store' => 'transactions.create',
            'accounting_pdf_export' => 'transactions.show', 'accounting_excel_export' => 'transactions.show',
            'invoice.update' => 'invoices.edit', 'payment.store' => 'invoices.create',
            'add_payment' => 'invoices.create', 'taxes_update' => 'taxes.edit',
            'taxes.store' => 'taxes.create', 'office.store' => 'office.create', 'invoices.store' => 'invoices.create',
            'tour_expenses.store' => 'tour_expenses.create', 'utility_expenses.store' => 'utility_expenses.create',
            'employes-salary.store' => 'employes-salary.create', 'office_earning.store' => 'office_earning.create',
            'office_balance.store' => 'office_balance.create',
            'officeInvoices.index' => 'office.index', 'officeInvoices.show' => 'office.show',
            'officeInvoices.create' => 'office.create', 'officeInvoices.store' => 'office.create',
            'officeInvoices.stored' => 'office.create', 'officeInvoices.edit' => 'office.edit',
            'officeInvoices.update' => 'office.edit', 'office_invoices.update' => 'office.edit',
            'officeInvoices.destroy' => 'office.destroy', 'office_invoices.destroy' => 'office.destroy',
            'office_invoices_detail.show' => 'office.show', 'office_invoices_pdf_export' => 'office.show',
        ];
        if (!isset($actions['permissions']) || $actions['permissions'] === $routeName) {
            $permissions = $accountingAliases[$routeName] ?? $permissions;
        }

        // Resource saves use the same capabilities as their create/edit forms.
        // Explicit route permissions and the billing aliases above take precedence.
        if ($usesRoutePermission && !isset($accountingAliases[$routeName]) && is_string($permissions)) {
            $permissions = preg_replace_callback('/\.(store|update)$/', function ($match) {
                return $match[1] === 'store' ? '.create' : '.edit';
            }, $permissions);
        }
        
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
            // A missing permission or failed lookup must not authorize the request.
            \Log::warning('Permission check failed in PermissionsRequiredMiddleware', [
                'user_id' => $request->user()->id ?? null,
                'permission' => $permissions,
                'route' => $routeName,
                'error' => $e->getMessage()
            ]);
            abort(403, 'You do not have permission to access this resource.');
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
