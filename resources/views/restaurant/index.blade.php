@extends('scaffold-interface.layouts.tabler-app')
@section('title','Restaurants')

@section('content')
<x-ui.page-header
    title="Restaurants"
    description="Restaurant suppliers and negotiated rates."
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Restaurants'],
    ]"
>
    <x-slot name="actions">
        @if(Auth::user()->can('restaurant.create'))
            <x-ui.button as="a" href="{{ route('restaurant.create') }}" icon="plus">
                {!! trans('main.New') ?? 'New' !!} restaurant
            </x-ui.button>
        @endif
    </x-slot>
</x-ui.page-header>

@if(session('export_all'))
    <div class="mb-4 flex items-start gap-3 rounded border border-info-600/20 bg-info-50 px-4 py-3 text-sm text-info-700">
        <x-ui.icon name="info" class="mt-0.5 text-info-600" />
        <div class="flex-1">{{ session('export_all') }}</div>
    </div>
@endif

@if(Session::has('message'))
    <div class="mb-4 flex items-start gap-3 rounded border border-danger-600/20 bg-danger-50 px-4 py-3 text-sm text-danger-700">
        <x-ui.icon name="alert-circle" class="mt-0.5 text-danger-600" />
        <div class="flex-1">{{ Session::get('message') }}</div>
    </div>
@endif

{{-- Toolbar: search + export --}}
<div class="rounded border border-slate-200 bg-white p-3 mb-4">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="relative flex-1 max-w-md">
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                <x-ui.icon name="search" size="sm" />
            </span>
            <input type="text"
                   id="restaurants-search"
                   onkeyup="filterTable('restaurants-table', this.value); filterCards('restaurants-cards', this.value);"
                   placeholder="Search restaurants on this page..."
                   class="block w-full h-9 rounded border border-slate-300 bg-white pl-9 pr-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
        </div>
        <div class="flex items-center gap-2">
            <x-ui.button variant="secondary" icon="download" size="sm"
                         onclick="exportTableToCSV('restaurants-table', 'restaurants_export.csv')">
                Export CSV
            </x-ui.button>
        </div>
    </div>
</div>

@if(count($restaurants) === 0)
    <div class="rounded border border-slate-200 bg-white">
        <x-ui.empty-state
            icon="utensils"
            title="No restaurants yet"
            message="Add your first restaurant to start building tour packages.">
            @if(Auth::user()->can('restaurant.create'))
                <x-ui.button as="a" href="{{ route('restaurant.create') }}" icon="plus">Add a restaurant</x-ui.button>
            @endif
        </x-ui.empty-state>
    </div>
@else

    {{-- Desktop table --}}
    <div class="hidden md:block rounded border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table id="restaurants-table" class="min-w-full divide-y divide-slate-200 datatable text-sm" style="background:#fff;">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(0, 'restaurants-table')">
                            <span class="inline-flex items-center gap-1">ID <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                        </th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(1, 'restaurants-table')">
                            <span class="inline-flex items-center gap-1">{{ trans('main.Name') }} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                        </th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(2, 'restaurants-table')">
                            <span class="inline-flex items-center gap-1">{{ trans('main.Address') }} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                        </th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(3, 'restaurants-table')">
                            <span class="inline-flex items-center gap-1">{{ trans('main.Country') }} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                        </th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(4, 'restaurants-table')">
                            <span class="inline-flex items-center gap-1">{{ trans('main.City') }} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                        </th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(5, 'restaurants-table')">
                            <span class="inline-flex items-center gap-1">{{ trans('main.WorkPhone') }} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                        </th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(6, 'restaurants-table')">
                            <span class="inline-flex items-center gap-1">{{ trans('main.ContactEmail') }} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" /></span>
                        </th>
                        <th class="px-4 py-3 text-right">{{ trans('main.Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($restaurants as $restaurant)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3 font-mono text-xs text-slate-500">#{{ $restaurant->id }}</td>
                            <td class="px-4 py-3">
                                @if(Auth::user()->can('restaurant.show'))
                                    <a href="{{ route('restaurant.show', ['restaurant' => $restaurant->id]) }}" class="font-medium text-slate-900 hover:text-primary-700">
                                        {{ $restaurant->name }}
                                    </a>
                                @else
                                    <span class="font-medium text-slate-900">{{ $restaurant->name }}</span>
                                @endif
                                @if(!empty($restaurant->city_name))
                                    <p class="mt-0.5 text-xs text-slate-500 truncate max-w-xs">{{ $restaurant->city_name }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-700">{{ $restaurant->address ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $restaurant->country_name ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $restaurant->city_name ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-700">
                                @if($restaurant->work_phone)
                                    <a href="tel:{{ $restaurant->work_phone }}" class="hover:text-primary-700">{{ $restaurant->work_phone }}</a>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-700">
                                @if($restaurant->contact_email)
                                    <a href="mailto:{{ $restaurant->contact_email }}" class="hover:text-primary-700 truncate inline-block max-w-[200px]" title="{{ $restaurant->contact_email }}">{{ $restaurant->contact_email }}</a>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    @if(Auth::user()->can('restaurant.show'))
                                        <a href="{{ route('restaurant.show', ['restaurant' => $restaurant->id]) }}"
                                           class="inline-flex h-7 w-7 items-center justify-center rounded text-slate-500 hover:bg-slate-100 hover:text-slate-700"
                                           title="View">
                                            <x-ui.icon name="eye" size="sm" />
                                        </a>
                                    @endif
                                    @if(Auth::user()->can('restaurant.edit'))
                                        <a href="{{ route('restaurant.edit', ['restaurant' => $restaurant->id]) }}"
                                           class="inline-flex h-7 w-7 items-center justify-center rounded text-slate-500 hover:bg-slate-100 hover:text-primary-700"
                                           title="Edit">
                                            <x-ui.icon name="edit" size="sm" />
                                        </a>
                                    @endif
                                    @if(Auth::user()->can('restaurant.destroy'))
                                        <button type="button"
                                                onclick="restaurantDeleteConfirm({{ $restaurant->id }}, @js($restaurant->name))"
                                                class="inline-flex h-7 w-7 items-center justify-center rounded text-slate-500 hover:bg-danger-50 hover:text-danger-700"
                                                title="Delete">
                                            <x-ui.icon name="trash-2" size="sm" />
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Mobile card list --}}
    <div id="restaurants-cards" class="md:hidden space-y-3">
        @foreach($restaurants as $restaurant)
            <div data-card-row class="rounded border border-slate-200 bg-white p-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        @if(Auth::user()->can('restaurant.show'))
                            <a href="{{ route('restaurant.show', ['restaurant' => $restaurant->id]) }}" data-card-name class="block font-medium text-slate-900 hover:text-primary-700 truncate">
                                {{ $restaurant->name }}
                            </a>
                        @else
                            <span data-card-name class="block font-medium text-slate-900 truncate">{{ $restaurant->name }}</span>
                        @endif
                        <p class="text-xs text-slate-500 font-mono mt-0.5">#{{ $restaurant->id }}</p>
                    </div>
                    <div class="flex items-center gap-1 shrink-0">
                        @if(Auth::user()->can('restaurant.edit'))
                            <a href="{{ route('restaurant.edit', ['restaurant' => $restaurant->id]) }}"
                               class="inline-flex h-8 w-8 items-center justify-center rounded text-slate-500 hover:bg-slate-100 hover:text-primary-700"
                               aria-label="Edit">
                                <x-ui.icon name="edit" size="sm" />
                            </a>
                        @endif
                        @if(Auth::user()->can('restaurant.destroy'))
                            <button type="button"
                                    onclick="restaurantDeleteConfirm({{ $restaurant->id }}, @js($restaurant->name))"
                                    class="inline-flex h-8 w-8 items-center justify-center rounded text-slate-500 hover:bg-danger-50 hover:text-danger-700"
                                    aria-label="Delete">
                                <x-ui.icon name="trash-2" size="sm" />
                            </button>
                        @endif
                    </div>
                </div>

                <dl class="mt-3 grid grid-cols-2 gap-x-3 gap-y-2 text-xs">
                    @if($restaurant->country_name || $restaurant->city_name)
                        <div class="col-span-2">
                            <dt class="text-slate-500 uppercase tracking-wide">Location</dt>
                            <dd class="text-slate-700">
                                {{ trim(($restaurant->city_name ?? '') . (($restaurant->city_name && $restaurant->country_name) ? ', ' : '') . ($restaurant->country_name ?? '')) ?: '—' }}
                            </dd>
                        </div>
                    @endif
                    @if(!empty($restaurant->address))
                        <div class="col-span-2">
                            <dt class="text-slate-500 uppercase tracking-wide">Address</dt>
                            <dd class="text-slate-700">{{ $restaurant->address }}</dd>
                        </div>
                    @endif
                    @if($restaurant->work_phone)
                        <div>
                            <dt class="text-slate-500 uppercase tracking-wide">Phone</dt>
                            <dd><a href="tel:{{ $restaurant->work_phone }}" class="text-primary-700">{{ $restaurant->work_phone }}</a></dd>
                        </div>
                    @endif
                    @if($restaurant->contact_email)
                        <div class="col-span-2">
                            <dt class="text-slate-500 uppercase tracking-wide">Email</dt>
                            <dd><a href="mailto:{{ $restaurant->contact_email }}" class="text-primary-700 break-all">{{ $restaurant->contact_email }}</a></dd>
                        </div>
                    @endif
                </dl>
            </div>
        @endforeach
    </div>

    {{-- Pagination strip --}}
    @if($restaurants->hasPages())
        <nav class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between" aria-label="Pagination">
            <p class="text-xs text-slate-500">
                Showing <span class="font-medium text-slate-700">{{ $restaurants->firstItem() }}</span>
                to <span class="font-medium text-slate-700">{{ $restaurants->lastItem() }}</span>
                of <span class="font-medium text-slate-700">{{ $restaurants->total() }}</span> restaurants
            </p>
            <div class="flex items-center gap-1 flex-wrap">
                @if($restaurants->onFirstPage())
                    <span class="inline-flex h-9 items-center gap-1 rounded border border-slate-200 px-3 text-sm text-slate-400 cursor-not-allowed">
                        <x-ui.icon name="chevron-left" size="xs" />Previous
                    </span>
                @else
                    <a href="{{ $restaurants->previousPageUrl() }}" class="inline-flex h-9 items-center gap-1 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 hover:bg-slate-50">
                        <x-ui.icon name="chevron-left" size="xs" />Previous
                    </a>
                @endif

                @foreach($restaurants->onEachSide(1)->getUrlRange(max(1, $restaurants->currentPage() - 2), min($restaurants->lastPage(), $restaurants->currentPage() + 2)) as $page => $url)
                    @if($page == $restaurants->currentPage())
                        <span class="inline-flex h-9 min-w-[2.25rem] items-center justify-center rounded bg-primary-600 px-3 text-sm font-medium text-white">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="inline-flex h-9 min-w-[2.25rem] items-center justify-center rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 hover:bg-slate-50">{{ $page }}</a>
                    @endif
                @endforeach

                @if($restaurants->hasMorePages())
                    <a href="{{ $restaurants->nextPageUrl() }}" class="inline-flex h-9 items-center gap-1 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 hover:bg-slate-50">
                        Next<x-ui.icon name="chevron-right" size="xs" />
                    </a>
                @else
                    <span class="inline-flex h-9 items-center gap-1 rounded border border-slate-200 px-3 text-sm text-slate-400 cursor-not-allowed">
                        Next<x-ui.icon name="chevron-right" size="xs" />
                    </span>
                @endif
            </div>
        </nav>
    @endif

@endif

{{-- Delete confirmation modal (Tailwind, vanilla JS) --}}
<div id="restaurantDeleteModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-slate-900/50" onclick="restaurantDeleteCancel()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4 pointer-events-none">
        <div class="relative w-full max-w-md rounded-md bg-white shadow-overlay pointer-events-auto">
            <div class="px-5 py-4 border-b border-slate-200 flex items-start gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-danger-50 text-danger-600 shrink-0">
                    <x-ui.icon name="alert-triangle" />
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-sm font-semibold text-slate-900">Delete restaurant?</h3>
                    <p class="mt-1 text-sm text-slate-500">
                        You're about to delete <strong id="restaurantDeleteName" class="text-slate-700"></strong>.
                        This will remove the restaurant from your supplier catalog. Existing tours that reference it stay intact.
                    </p>
                </div>
            </div>
            <div class="px-5 py-3 bg-slate-50 rounded-b-md flex items-center justify-end gap-2">
                <button type="button" onclick="restaurantDeleteCancel()" class="inline-flex h-9 items-center rounded border border-slate-300 bg-white px-4 text-sm font-medium text-slate-700 hover:bg-slate-100">Cancel</button>
                <a id="restaurantDeleteConfirmBtn" href="#" class="inline-flex h-9 items-center gap-2 rounded bg-danger-600 px-4 text-sm font-medium text-white hover:bg-danger-700">
                    <x-ui.icon name="trash-2" size="sm" />
                    Delete restaurant
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/bootstrap-tables.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof initializeBootstrapTable === 'function') {
            initializeBootstrapTable('restaurants-table');
        }
    });

    window.filterCards = window.filterCards || function (containerId, value) {
        var c = document.getElementById(containerId);
        if (!c) return;
        var q = (value || '').toLowerCase();
        c.querySelectorAll('[data-card-row]').forEach(function (row) {
            var name = (row.querySelector('[data-card-name]')?.textContent || '').toLowerCase();
            row.style.display = (!q || name.indexOf(q) !== -1) ? '' : 'none';
        });
    };

    window.restaurantDeleteConfirm = function (id, name) {
        var modal = document.getElementById('restaurantDeleteModal');
        var label = document.getElementById('restaurantDeleteName');
        var btn   = document.getElementById('restaurantDeleteConfirmBtn');
        if (!modal || !btn) return;
        label.textContent = name || ('restaurant #' + id);
        btn.href = '{{ url("restaurant") }}/' + id + '/delete';
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    };
    window.restaurantDeleteCancel = function () {
        var modal = document.getElementById('restaurantDeleteModal');
        if (!modal) return;
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    };
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') window.restaurantDeleteCancel();
    });
</script>
@endpush
