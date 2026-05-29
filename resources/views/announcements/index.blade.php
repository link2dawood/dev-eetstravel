@extends('scaffold-interface.layouts.tabler-app')
@section('title','Index')

@section('content')
<x-ui.page-header
    title="Announcements"
    description="Staff-facing announcements and replies."
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Announcements'],
    ]"
>
    <x-slot name="actions">
        @if(Auth::user()->can('announcements.create'))
            <x-ui.button as="a" href="{{ route('announcements.create') }}" icon="plus">New announcement</x-ui.button>
        @endif
    </x-slot>
</x-ui.page-header>

<div class="rounded border border-slate-200 bg-white p-3 mb-4">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="relative flex-1 max-w-md">
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                <x-ui.icon name="search" size="sm" />
            </span>
            <input type="text" id="announcements-search" onkeyup="filterTable('announcements-table', this.value)"
                   placeholder="Search announcements..."
                   class="block w-full h-9 rounded border border-slate-300 bg-white pl-9 pr-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
        </div>
        <x-ui.button variant="secondary" icon="download" size="sm" onclick="exportTableToCSV('announcements-table', 'announcements_export.csv')">
            Export CSV
        </x-ui.button>
    </div>
</div>

@if(count($announcements) === 0)
    <div class="rounded border border-slate-200 bg-white">
        <x-ui.empty-state icon="megaphone" title="No announcements" message="Post your first staff announcement.">
            @if(Auth::user()->can('announcements.create'))
                <x-ui.button as="a" href="{{ route('announcements.create') }}" icon="plus">New announcement</x-ui.button>
            @endif
        </x-ui.empty-state>
    </div>
@else
    <div class="rounded border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table id="announcements-table" class="min-w-full divide-y divide-slate-200 text-sm" style="background:#fff">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                        <th class="px-4 py-3">ID</th>
                        <th class="px-4 py-3">{!! trans('main.Title') !!}</th>
                        <th class="px-4 py-3">{!! trans('main.Content') !!}</th>
                        <th class="px-4 py-3">{!! trans('main.Time') !!}</th>
                        <th class="px-4 py-3">{!! trans('main.Sender') !!}</th>
                        <th class="px-4 py-3">{!! trans('main.Files') !!}</th>
                        <th class="px-4 py-3 text-right">{!! trans('main.Actions') !!}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($announcements as $announcement)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-mono text-xs text-slate-500">#{{ $announcement->id }}</td>
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $announcement->title }}</td>
                            <td class="px-4 py-3 text-slate-700 max-w-md">{{ Str::limit($announcement->content, 100) }}</td>
                            <td class="px-4 py-3 text-slate-500 text-xs whitespace-nowrap">{{ $announcement->created_at ? $announcement->created_at->format('Y-m-d H:i') : '' }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ optional($announcement->author)->name ?? 'Unknown' }}</td>
                            <td class="px-4 py-3 text-xs">
                                @if($announcement->files && $announcement->files->count() > 0)
                                    @foreach($announcement->files as $file)
                                        <a href="{{ $file->url }}" target="_blank" class="text-primary-700 hover:underline block truncate max-w-[160px]">{{ $file->name }}</a>
                                    @endforeach
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    @include('component.action_buttons', ['item' => $announcement, 'routePrefix' => 'announcements'])
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @if(method_exists($announcements, 'links'))
        <div class="mt-4">{{ $announcements->links() }}</div>
    @endif
@endif
@endsection
