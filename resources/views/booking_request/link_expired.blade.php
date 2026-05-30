{{-- Public link-expired page. Standalone HTML, no @extends — served when
     a supplier opens a booking-request URL that's past its option date. --}}
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Link expired — TMS</title>
    <link href="{{ asset('css/tailwind.css') }}?v={{ file_exists(public_path('css/tailwind.css')) ? filemtime(public_path('css/tailwind.css')) : time() }}" rel="stylesheet">
    <style>
        @import url('https://rsms.me/inter/inter.css');
        html, body { margin: 0; padding: 0; }
        body {
            font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, sans-serif;
            background: linear-gradient(135deg, #f0fdfa 0%, #f8fafc 60%, #fef3c7 100%);
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }
        *, *::before, *::after { box-sizing: border-box; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-lg rounded-2xl bg-white shadow-xl border border-slate-200 overflow-hidden">
        <div class="px-6 sm:px-10 py-12 text-center">
            <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-warning-100">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-10 w-10">
                    <circle cx="12" cy="12" r="10" />
                    <polyline points="12 6 12 12 16 14" />
                </svg>
            </div>

            <h1 class="text-2xl sm:text-3xl font-semibold tracking-tight text-slate-900">
                Oops, this link has expired
            </h1>
            <p class="mt-3 text-sm sm:text-base text-slate-600">
                This URL is no longer valid. Please contact your operations contact at eetstravel for a fresh link or to re-open the offer window.
            </p>

            <div class="mt-8">
                <a href="https://dev.eetstravel.com/" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-2 rounded-md bg-primary-600 px-5 h-11 text-sm font-medium text-white hover:bg-primary-700 shadow">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                        <path d="M3 12h18M12 3l9 9-9 9" />
                    </svg>
                    Go to eetstravel.com
                </a>
            </div>
        </div>

        <div class="border-t border-slate-200 bg-slate-50 px-6 py-3 text-center">
            <p class="text-xs text-slate-500">© {{ date('Y') }} eetstravel.com</p>
        </div>
    </div>
</body>
</html>
