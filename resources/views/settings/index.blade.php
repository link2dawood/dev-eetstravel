@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Settings')

@section('content')
<x-ui.page-header
    title="Settings"
    description="Application-wide configuration keys."
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Settings'],
    ]"
>
    <x-slot name="actions">
        <x-ui.button as="a" href="{{ route('settings.create') }}" icon="plus">New setting</x-ui.button>
    </x-slot>
</x-ui.page-header>

<div class="rounded border border-slate-200 bg-white p-3 mb-4">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="relative flex-1 max-w-md">
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                <x-ui.icon name="search" size="sm" />
            </span>
            <input type="text" id="settings-search"
                   onkeyup="filterTable('settings-table', this.value)"
                   placeholder="Search settings..."
                   class="block w-full h-9 rounded border border-slate-300 bg-white pl-9 pr-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
        </div>
        <x-ui.button variant="secondary" icon="download" size="sm" onclick="exportTableToCSV('settings-table', 'settings_export.csv')">
            Export CSV
        </x-ui.button>
    </div>
</div>

@if(count($settings) === 0)
    <div class="rounded border border-slate-200 bg-white">
        <x-ui.empty-state icon="settings" title="No settings yet" message="Add a key to start configuring the app.">
            <x-ui.button as="a" href="{{ route('settings.create') }}" icon="plus">New setting</x-ui.button>
        </x-ui.empty-state>
    </div>
@else
    <div class="rounded border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table id="settings-table" class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                        <th class="px-4 py-3">{!! trans('main.Description') !!}</th>
                        <th class="px-4 py-3">{!! trans('main.Value') !!}</th>
                        <th class="px-4 py-3 text-right">{!! trans('main.Actions') !!}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($settings as $setting)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 text-slate-700">{{ $setting->description ?? '—' }}</td>
                            <td class="px-4 py-3 font-mono text-sm text-slate-800">{{ $setting->value ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('settings.edit', ['setting' => $setting->id]) }}"
                                       class="inline-flex h-7 w-7 items-center justify-center rounded text-slate-500 hover:bg-slate-100 hover:text-primary-700" title="Edit">
                                        <x-ui.icon name="edit" size="sm" />
                                    </a>
                                    <a href="#" onclick="event.preventDefault(); confirmDelete('{{ $setting->id }}');"
                                       class="inline-flex h-7 w-7 items-center justify-center rounded text-slate-500 hover:bg-danger-50 hover:text-danger-700" title="Delete">
                                        <x-ui.icon name="trash-2" size="sm" />
                                    </a>
                                    <form id="delete-form-{{ $setting->id }}" action="{{ route('settings.destroy', $setting->id) }}" method="POST" style="display:none;">
                                        @csrf @method('DELETE')
                                    </form>
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
    if (typeof initializeBootstrapTable === 'function') initializeBootstrapTable('settings-table');
});
function confirmDelete(settingId) {
    if (confirm('Are you sure you want to delete this setting?')) {
        document.getElementById('delete-form-' + settingId).submit();
    }
}
</script>
@endpush
