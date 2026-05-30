<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Reject GET requests to destructive routes (...delete, ...destroy, ...remove)
 * that arrive without a matching same-origin Referer header.
 *
 * AUDIT.md CC4 mitigation. The codebase has 92 destructive routes
 * registered as GET (Ajaxis-style confirmation modals). Converting them
 * all to POST would require touching every confirm-modal partial and
 * every JS handler that opens one — out of scope for this pass. The
 * Referer check is a defence-in-depth fence that stops the most common
 * attack shapes against unauthenticated GET deletes:
 *
 *   - <img src="https://app/foo/123/delete"> planted in a comment/email
 *     → browsers DO send Referer cross-origin (in many cases), but to a
 *     DIFFERENT origin → blocked here.
 *   - Browser / antivirus link prefetch → no Referer sent → blocked.
 *   - User opening a malicious link in a new tab → Referer is the
 *     attacker's site → blocked.
 *
 * It does NOT stop a co-resident XSS on the same origin from firing a
 * delete — but those are CC1/CC15 territory.
 *
 * Limitation: a strict Referer check breaks direct-URL paste
 * (Referer is empty). We accept that trade-off because pasting
 * "/something/delete" into the URL bar is never a normal workflow.
 */
class SameOriginDestructive
{
    public function handle(Request $request, Closure $next)
    {
        // Only act on GET — POST/PUT/DELETE already require CSRF.
        if (!$request->isMethod('GET')) {
            return $next($request);
        }

        $path = $request->path();
        // Only block known destructive verbs in the URL.
        if (!preg_match('#(?:^|/)(delete|destroy|remove)(?:Msg)?(?:/|$)#i', $path)) {
            return $next($request);
        }

        // The companion *deleteMsg / *deleteMsg endpoints return a modal
        // (READ-only — no DB writes). Don't block those, only the
        // actual destroy actions.
        if (preg_match('#deleteMsg|destroyMsg|removeMsg#i', $path)) {
            return $next($request);
        }

        $referer = $request->headers->get('referer');
        $appUrl  = parse_url(config('app.url'), PHP_URL_HOST);
        $hostOk  = false;

        if ($referer) {
            $refHost = parse_url($referer, PHP_URL_HOST);
            if ($refHost && $appUrl && strcasecmp($refHost, $appUrl) === 0) {
                $hostOk = true;
            }
            // Also allow if Referer host matches the current request host
            // (covers dev/staging where APP_URL drifts from the actual host).
            if (!$hostOk && $refHost && strcasecmp($refHost, $request->getHost()) === 0) {
                $hostOk = true;
            }
        }

        if (!$hostOk) {
            \Log::warning('Blocked destructive GET with missing/mismatched Referer', [
                'path'    => $path,
                'referer' => $referer,
                'ip'      => $request->ip(),
                'user_id' => optional($request->user())->id,
            ]);
            return abort(403, 'Destructive action requires same-origin Referer.');
        }

        return $next($request);
    }
}
