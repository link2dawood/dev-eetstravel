{{--
    Shared TMSClient portal layout.
    Standalone HTML; loads ONLY tailwind.css + the legacy JS bundles each
    view needs (jQuery, Bootstrap-JS for modals/dropdowns/tabs that the
    legacy code still binds to, sweetalert, custom.js, etc.).

    Slots
    -----
    @yield('title')          Page title
    @yield('content')        Main content area
    @push('styles')          Extra stylesheets
    @push('scripts')         Extra scripts
--}}
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', 'TMS Client')</title>

    {{-- Tailwind --}}
    <link href="{{ asset('css/tailwind.css') }}?v={{ file_exists(public_path('css/tailwind.css')) ? filemtime(public_path('css/tailwind.css')) : time() }}" rel="stylesheet" />

    {{-- Inter font --}}
    <style>
        @import url('https://rsms.me/inter/inter.css');
        body { font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, sans-serif; }
    </style>

    {{-- Legacy CSS bundles some pages still rely on. Kept after Tailwind so
         Tailwind utility classes win. --}}
    <link rel="stylesheet" href="{{ asset('clientassets/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('clientassets/fonts/fontawesome-free-5.15.4-web/css/all.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/bootstrap-tables.css') }}" />

    {{-- jQuery loaded in <head> so inline <script> blocks inside page
         content can call $(...) without crashing. The legacy footer order
         relied on parse-time delay that's no longer reliable. --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    @stack('styles')
</head>
<body class="min-h-screen bg-slate-50 antialiased text-slate-900">

    @hasSection('with-nav-false')
        {{-- Some pages (login) opt out of the nav by defining @section('with-nav-false') --}}
    @else
        @include('TMSClient.layouts.nav')
    @endif

    <main class="@hasSection('with-nav-false')@else pt-16 @endif min-h-screen">
        @yield('content')
    </main>

    {{-- Footer plugin scripts. Most rely on jQuery (loaded in <head>). --}}
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="{{ asset('clientassets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-steps/1.1.0/jquery.steps.min.js"></script>
    <script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.15.0/jquery.validate.js"></script>
    <script src="{{ asset('clientassets/js/custom.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap-tables.js') }}"></script>
    <script type="text/javascript" src='{{ asset('js/cities.js') }}'></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('scripts')
</body>
</html>
