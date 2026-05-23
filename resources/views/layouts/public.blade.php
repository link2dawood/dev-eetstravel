{{--
    layouts.public — minimal, no-auth, no-JS Tailwind shell for
    client-facing pages (landing page, future shareable artifacts).

    Why a dedicated layout instead of extending scaffold-interface.layouts.tabler-app:
    - This is served to end clients, NOT staff. Showing the staff
      sidebar would leak internal navigation (Invoices, Tasks, Email…)
      to anyone with a share link.
    - It runs without auth. No jQuery, no Bootstrap, no Alpine — just
      compiled Tailwind. Smaller payload, faster TTFB, fewer cookies
      to worry about.
    - The staff layout is paused mid-Tailwind-migration; this layout
      is fully Tailwind from day one.

    Sections / yields:
      @yield('title')        — Page <title>.
      @yield('content')      — Main body content.
      @stack('head')         — Optional extra <head> entries (rare).

    Used by:
      resources/views/export/landing_page.blade.php
      resources/views/errors/landing-not-found.blade.php
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', config('app.name', 'TMS'))</title>

    <link rel="preconnect" href="https://rsms.me">
    <link rel="stylesheet" href="{{ asset('css/tailwind.css') }}">

    @stack('head')

    {{-- Print-friendly: hide chrome, expand content, drop bg colors --}}
    <style>
        @media print {
            html, body { background: #fff !important; }
            .print\:hidden { display: none !important; }
        }
    </style>
</head>
<body class="h-full bg-slate-50 text-slate-800 antialiased font-sans">
    @yield('content')
</body>
</html>
