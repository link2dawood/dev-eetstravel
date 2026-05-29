@extends('scaffold-interface.layouts.tabler-app')
@section('title','Room types')

@section('content')
<x-ui.page-header
    title="Room types"
    description="Catalog of room categories used by hotels and quotations."
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Room types'],
    ]"
>
    <x-slot name="actions">
        @if(Auth::user()->can('room_types.create'))
            <x-ui.button as="a" href="{{ route('room_types.create') }}" icon="plus">New room type</x-ui.button>
        @endif
    </x-slot>
</x-ui.page-header>

@if(count($room_types) === 0)
    <div class="rounded border border-slate-200 bg-white">
        <x-ui.empty-state icon="bed" title="No room types yet" message="Add your first room category — single, double, suite, etc.">
            @if(Auth::user()->can('room_types.create'))
                <x-ui.button as="a" href="{{ route('room_types.create') }}" icon="plus">New room type</x-ui.button>
            @endif
        </x-ui.empty-state>
    </div>
@else
    <div class="rounded border border-slate-200 bg-white">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-4 py-3 border-b border-slate-200">
            <div class="w-full sm:max-w-xs">
                <input type="text" id="room-types-search" placeholder="{{ trans('main.Search') ?? 'Search room types…' }}"
                       onkeyup="filterTable('room-types-table', this.value)"
                       class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
            </div>
            <div>
                <button type="button" onclick="exportTableToCSV('room-types-table', 'room_types_export.csv')"
                        class="inline-flex items-center gap-1.5 rounded border border-slate-300 bg-white px-3 h-9 text-sm text-slate-700 hover:bg-slate-50 shadow-subtle">
                    <x-ui.icon name="download" size="sm" /> Export CSV
                </button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table id="room-types-table" class="min-w-full divide-y divide-slate-200 text-sm bootstrap-table" style="background:#fff">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(0, 'room-types-table')">ID <x-ui.icon name="arrows-sort" size="xs" class="text-slate-400" /></th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(1, 'room-types-table')">{{ trans('main.Name') }} <x-ui.icon name="arrows-sort" size="xs" class="text-slate-400" /></th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(2, 'room-types-table')">{{ trans('main.Code') }} <x-ui.icon name="arrows-sort" size="xs" class="text-slate-400" /></th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(3, 'room-types-table')">{{ trans('main.Sortorder') }} <x-ui.icon name="arrows-sort" size="xs" class="text-slate-400" /></th>
                        <th class="px-4 py-3 text-right">{{ trans('main.Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($room_types as $room_type)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-mono text-xs text-slate-500">#{{ $room_type->id }}</td>
                            <td class="px-4 py-3 font-medium text-slate-900" data-delete-label>{{ $room_type->name ?? '' }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $room_type->code ?? '' }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $room_type->sort_order ?? '' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    @include('component.action_buttons', ['item' => $room_type, 'routePrefix' => 'room_types'])
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
@endsection

@push('scripts')
<script src="{{ asset('js/bootstrap-tables.js') }}"></script>
<script>
    $(document).ready(function () {
        if (typeof initializeBootstrapTable === 'function') {
            initializeBootstrapTable('room-types-table');
        }
    });
</script>
@endpush

@include('component.delete_modal_simple')
