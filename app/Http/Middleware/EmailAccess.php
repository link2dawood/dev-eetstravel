<?php
namespace App\Http\Middleware;

class EmailAccess
{
    public function handle($request, \Closure $next)
    {
        if (!$request->user()) return redirect()->route('login');
        try { $allowed = $request->user()->hasPermissionTo('email.main'); }
        catch (\Throwable $e) { $allowed = false; }
        abort_unless($allowed, 403);
        return $next($request);
    }
}
