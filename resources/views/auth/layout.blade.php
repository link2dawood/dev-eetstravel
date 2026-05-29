{{--
    Shared auth layout for /login, /register, /password/reset, etc.
    Standalone — does NOT extend the dashboard tabler-app layout because
    auth pages must render for guests (no sidebar / no header / no top
    nav). Loads ONLY tailwind.css.

    NOTE: Preflight is disabled project-wide (see tailwind.config.js) so
    we cannot rely on Tailwind's base resets here. The split-screen layout
    therefore uses plain CSS classes + a <style> block at the bottom for
    the 50/50 columns, the mobile hide rule, and the typography reset on
    body / heading / list elements. This is the root-cause fix — earlier
    versions used `grid lg:grid-cols-2` + `hidden lg:flex` which depended
    on Preflight defaults and produced a broken thin teal strip.

    Slots
    -----
    @yield('title')      Page-specific <title>
    @yield('content')    Right-pane form content
    @yield('brand-hero') Optional override for the left-pane brand area
--}}
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'TMS') — Tour Management System</title>
    <link href="{{ asset('css/tailwind.css') }}?v={{ file_exists(public_path('css/tailwind.css')) ? filemtime(public_path('css/tailwind.css')) : time() }}" rel="stylesheet" />
    <style>
        @import url('https://rsms.me/inter/inter.css');

        /* Local reset (Preflight is off project-wide). */
        html, body { margin: 0; padding: 0; }
        body {
            font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, sans-serif;
            background: #f8fafc;
            color: #0f172a;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }
        *, *::before, *::after { box-sizing: border-box; }

        /* Split shell — flex with explicit 50/50 above lg, single column below. */
        .auth-shell {
            min-height: 100vh;
            display: flex;
            flex-direction: row;
            align-items: stretch;
        }
        .auth-aside {
            width: 50%;
            flex-shrink: 0;
            position: relative;
            overflow: hidden;
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 4rem 3rem;
            background-image: linear-gradient(to bottom right, #0f766e 0%, #0d9488 50%, #115e59 100%);
        }
        .auth-main {
            flex: 1 1 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 1.5rem;
        }
        .auth-card { width: 100%; max-width: 28rem; }
        .auth-mobile-logo { text-align: center; margin-bottom: 2rem; }

        @media (max-width: 1023.98px) {
            .auth-aside { display: none; }
            .auth-main { width: 100%; padding: 3rem 1rem; }
        }
        @media (min-width: 1024px) {
            .auth-mobile-logo { display: none; }
        }

        /* Brand-side typography (Preflight off, so reset margins / list style). */
        .auth-aside h1 { margin: 0; font-size: 2.25rem; line-height: 1.15; font-weight: 600; letter-spacing: -0.02em; }
        .auth-aside p  { margin: 1rem 0 0; color: rgba(255,255,255,0.85); max-width: 28rem; }
        .auth-aside ul { list-style: none; padding: 0; margin: 2rem 0 0; }
        .auth-aside li { display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: rgba(255,255,255,0.9); margin-top: 0.75rem; }
        .auth-aside li:first-child { margin-top: 0; }

        /* Decorative blurred orbs. */
        .auth-orb {
            position: absolute;
            border-radius: 9999px;
            filter: blur(48px);
            pointer-events: none;
        }
        .auth-orb-tr { top: -6rem; right: -6rem; height: 18rem; width: 18rem; background: rgba(255,255,255,0.10); }
        .auth-orb-bl { bottom: -8rem; left: -4rem; height: 20rem; width: 20rem; background: rgba(45,212,191,0.30); }

        /* Brand mark chip. */
        .auth-brand-row { position: relative; display: inline-flex; align-items: center; gap: 0.5rem; font-size: 1.125rem; font-weight: 600; letter-spacing: -0.02em; }
        .auth-brand-chip { display: inline-flex; height: 2.25rem; width: 2.25rem; align-items: center; justify-content: center; border-radius: 0.375rem; background: rgba(255,255,255,0.15); backdrop-filter: blur(8px); }
        .auth-bullet-chip { display: inline-flex; height: 1.25rem; width: 1.25rem; align-items: center; justify-content: center; border-radius: 9999px; background: rgba(255,255,255,0.15); flex-shrink: 0; }
        .auth-foot { position: relative; margin: 0; color: rgba(255,255,255,0.65); font-size: 0.75rem; }

        /* Mobile-logo chip on the form pane. */
        .auth-mobile-brand { display: inline-flex; align-items: center; gap: 0.5rem; font-size: 1.125rem; font-weight: 600; color: #0f766e; }
        .auth-mobile-chip  { display: inline-flex; height: 2.25rem; width: 2.25rem; align-items: center; justify-content: center; border-radius: 0.375rem; background: #f0fdfa; }
    </style>
</head>
<body>
    <div class="auth-shell">

        {{-- Left: brand panel (hidden under 1024px via the .auth-aside @media rule) --}}
        <aside class="auth-aside">
            <div class="auth-orb auth-orb-tr" aria-hidden="true"></div>
            <div class="auth-orb auth-orb-bl" aria-hidden="true"></div>

            <div class="auth-brand-row">
                <span class="auth-brand-chip">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="20" height="20">
                        <path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.2c.4-.3.6-.7.5-1.2z"/>
                    </svg>
                </span>
                TMS
            </div>

            @hasSection('brand-hero')
                @yield('brand-hero')
            @else
                <div style="position:relative;">
                    <h1>Tour management,<br />from quote to invoice.</h1>
                    <p>Plan tours, manage suppliers, build quotes, and track every payment — all from one workspace.</p>
                    <ul>
                        <li>
                            <span class="auth-bullet-chip">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" width="12" height="12"><path d="M20 6 9 17l-5-5"/></svg>
                            </span>
                            Quotations, room lists, and front sheets in one place
                        </li>
                        <li>
                            <span class="auth-bullet-chip">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" width="12" height="12"><path d="M20 6 9 17l-5-5"/></svg>
                            </span>
                            Hotel, restaurant, and transfer catalog
                        </li>
                        <li>
                            <span class="auth-bullet-chip">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" width="12" height="12"><path d="M20 6 9 17l-5-5"/></svg>
                            </span>
                            Per-office accounting and invoicing
                        </li>
                    </ul>
                </div>
            @endif

            <p class="auth-foot">© {{ date('Y') }} eetstravel.com — All rights reserved.</p>
        </aside>

        {{-- Right: form pane --}}
        <main class="auth-main">
            <div class="auth-card">
                {{-- Mobile-only logo (shown when the brand pane is hidden) --}}
                <div class="auth-mobile-logo">
                    <div class="auth-mobile-brand">
                        <span class="auth-mobile-chip">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="20" height="20">
                                <path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.2c.4-.3.6-.7.5-1.2z"/>
                            </svg>
                        </span>
                        TMS
                    </div>
                </div>

                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
