{{--
    Shared auth layout for /login, /register, /password/reset, etc.
    Standalone — does NOT extend the dashboard tabler-app layout because
    auth pages must render for guests (no sidebar / no header / no top
    nav). Loads ONLY tailwind.css.

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
        body { font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-slate-50 antialiased">
    <div class="min-h-screen grid lg:grid-cols-2">

        {{-- Left: brand panel (hidden on small screens) --}}
        <aside class="hidden lg:flex relative overflow-hidden bg-gradient-to-br from-primary-700 via-primary-600 to-primary-800 text-white px-12 py-16 flex-col justify-between">
            {{-- Decorative blurred dots --}}
            <div aria-hidden="true" class="absolute -top-24 -right-24 h-72 w-72 rounded-full bg-white/10 blur-3xl"></div>
            <div aria-hidden="true" class="absolute -bottom-32 -left-16 h-80 w-80 rounded-full bg-primary-400/30 blur-3xl"></div>

            <div class="relative">
                <div class="inline-flex items-center gap-2 text-lg font-semibold tracking-tight">
                    <span class="flex h-9 w-9 items-center justify-center rounded-md bg-white/15 backdrop-blur">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                            <path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.2c.4-.3.6-.7.5-1.2z"/>
                        </svg>
                    </span>
                    TMS
                </div>
            </div>

            @hasSection('brand-hero')
                @yield('brand-hero')
            @else
                <div class="relative">
                    <h1 class="text-4xl font-semibold tracking-tight leading-tight">
                        Tour management,<br />from quote to invoice.
                    </h1>
                    <p class="mt-4 text-base text-white/80 max-w-md">
                        Plan tours, manage suppliers, build quotes, and track every payment — all from one workspace.
                    </p>
                    <ul class="mt-8 space-y-3 text-sm text-white/85">
                        <li class="flex items-center gap-2">
                            <span class="flex h-5 w-5 items-center justify-center rounded-full bg-white/15">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="h-3 w-3"><path d="M20 6 9 17l-5-5"/></svg>
                            </span>
                            Quotations, room lists, and front sheets in one place
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="flex h-5 w-5 items-center justify-center rounded-full bg-white/15">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="h-3 w-3"><path d="M20 6 9 17l-5-5"/></svg>
                            </span>
                            Hotel, restaurant, and transfer catalog
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="flex h-5 w-5 items-center justify-center rounded-full bg-white/15">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="h-3 w-3"><path d="M20 6 9 17l-5-5"/></svg>
                            </span>
                            Per-office accounting and invoicing
                        </li>
                    </ul>
                </div>
            @endif

            <p class="relative text-xs text-white/60">© {{ date('Y') }} eetstravel.com — All rights reserved.</p>
        </aside>

        {{-- Right: form pane --}}
        <main class="flex items-center justify-center px-4 py-12 sm:px-6 lg:px-12">
            <div class="w-full max-w-md">
                {{-- Mobile-only logo (shown when the brand pane is hidden) --}}
                <div class="lg:hidden mb-8 text-center">
                    <div class="inline-flex items-center gap-2 text-lg font-semibold text-primary-700">
                        <span class="flex h-9 w-9 items-center justify-center rounded-md bg-primary-50">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
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
