@php
    use App\Client;
    $client_id = session('CLIENT_ID');
    $client = $client_id ? Client::find($client_id) : null;
    $clientName = $client->name ?? 'Account';
    $initial = strtoupper(substr($clientName, 0, 1));
@endphp

<header class="fixed top-0 left-0 right-0 z-30 bg-white border-b border-slate-200 shadow-subtle">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="h-16 flex items-center justify-between gap-4">

            {{-- Brand --}}
            <a href="{{ url('TMS-Client/home') }}" class="flex items-center gap-2 text-slate-900">
                <span class="flex h-9 w-9 items-center justify-center rounded-md bg-primary-50 text-primary-600">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                        <path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.2c.4-.3.6-.7.5-1.2z"/>
                    </svg>
                </span>
                <span class="text-base font-semibold tracking-tight">TMS</span>
            </a>

            {{-- Nav links --}}
            <nav class="hidden md:flex items-center gap-1">
                @php
                    $isHome = request()->is('TMS-Client/home') || request()->is('TMS-Client-tours*');
                    $isQR   = request()->is('TMS-Client/quotation_requests');
                @endphp
                <a href="{{ url('TMS-Client/home') }}"
                   class="inline-flex h-9 items-center rounded-md px-3 text-sm font-medium {{ $isHome ? 'bg-primary-50 text-primary-700' : 'text-slate-700 hover:bg-slate-100' }}">
                    Home
                </a>
                <a href="{{ url('TMS-Client/quotation_requests') }}"
                   class="inline-flex h-9 items-center rounded-md px-3 text-sm font-medium {{ $isQR ? 'bg-primary-50 text-primary-700' : 'text-slate-700 hover:bg-slate-100' }}">
                    Quotation Requests
                </a>
            </nav>

            {{-- User dropdown --}}
            <div class="relative" x-data="{ open: false }">
                <button type="button" onclick="this.parentElement.classList.toggle('open')"
                        class="inline-flex items-center gap-2 rounded-md px-2 py-1 hover:bg-slate-100">
                    <span class="hidden sm:inline text-sm font-medium text-slate-900">{{ $clientName }}</span>
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-primary-600 text-white text-xs font-semibold">{{ $initial }}</span>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 text-slate-400 hidden sm:inline"><polyline points="6 9 12 15 18 9"/></svg>
                </button>
                <div class="hidden absolute right-0 mt-2 w-44 rounded-md border border-slate-200 bg-white shadow-overlay py-1" data-user-dropdown>
                    <a href="{{ route('client.logout') }}"
                       class="flex items-center gap-2 px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
    // Toggle user dropdown via the .open class on the parent
    (function () {
        document.addEventListener('click', function (e) {
            document.querySelectorAll('header .relative').forEach(function (root) {
                if (!root.contains(e.target)) root.classList.remove('open');
            });
        });
        var style = document.createElement('style');
        style.textContent = 'header .relative.open > [data-user-dropdown] { display: block !important; }';
        document.head.appendChild(style);
    })();
</script>
