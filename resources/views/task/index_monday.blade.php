{{--
    Tasks index — clean SaaS-style list page (Phase 3 redesign).

    Original work. Common list-page patterns (page header, filter row,
    status tabs, data table with priority + due-date columns, pagination).
    No third-party code reproduced.

    Data passed in by TaskController@index:
      $todoTasks       LengthAwarePaginator   page param 'todo_page'
      $completedTasks  LengthAwarePaginator   page param 'completed_page'
      $abortedTasks    LengthAwarePaginator   page param 'aborted_page'
      $task_types      string (json-encoded)  Task::$taskTypes flipped
      $statuses        Collection<App\Status> task statuses
      $title           string
--}}
@extends('scaffold-interface.layouts.tabler-app')

@section('title', 'Tasks')

@section('content')

@php
    $user = \Illuminate\Support\Facades\Auth::user();

    // Which tab is active. Query param `tab=…` selects; default is todo.
    $tab = request()->query('tab', 'todo');
    $allowedTabs = ['todo', 'completed', 'aborted'];
    if (!in_array($tab, $allowedTabs, true)) { $tab = 'todo'; }

    $tabs = [
        ['key' => 'todo',      'label' => 'To do',     'count' => $todoTasks->total(),      'paginator' => $todoTasks],
        ['key' => 'completed', 'label' => 'Completed', 'count' => $completedTasks->total(), 'paginator' => $completedTasks],
        ['key' => 'aborted',   'label' => 'Aborted',   'count' => $abortedTasks->total(),   'paginator' => $abortedTasks],
    ];
    $activeTab = collect($tabs)->firstWhere('key', $tab);
    $paginator = $activeTab['paginator'];

    // Map priority → variant + label. Tasks use a 1..3 priority scale
    // (Low/Normal/High); some rows may have a string or null.
    $priorityFor = function ($p) {
        if ($p === null || $p === '' || $p == 0) return ['variant' => 'neutral', 'label' => 'Low'];
        if (is_string($p)) {
            $low = strtolower($p);
            if (str_contains($low, 'high'))   return ['variant' => 'danger',  'label' => 'High'];
            if (str_contains($low, 'med') || str_contains($low, 'normal')) return ['variant' => 'warning', 'label' => 'Normal'];
            return ['variant' => 'neutral', 'label' => ucfirst($p)];
        }
        $p = (int) $p;
        if ($p >= 3) return ['variant' => 'danger',  'label' => 'High'];
        if ($p == 2) return ['variant' => 'warning', 'label' => 'Normal'];
        return ['variant' => 'neutral', 'label' => 'Low'];
    };

    // Status variant by name keyword
    $statusVariant = function ($name) {
        if (!$name) return 'neutral';
        $low = strtolower($name);
        if (str_contains($low, 'done') || str_contains($low, 'complete')) return 'success';
        if (str_contains($low, 'abort') || str_contains($low, 'cancel'))  return 'danger';
        if (str_contains($low, 'progress'))                                 return 'info';
        if (str_contains($low, 'review') || str_contains($low, 'wait'))    return 'warning';
        return 'primary';
    };

    $today    = \Carbon\Carbon::today();
    $searchTerm = trim((string) request()->query('q', ''));
    $statusFilter = request()->query('status', '');
@endphp

<x-ui.page-header
    title="Tasks"
    description="Everything to do, by status. Click a task to open or edit it."
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Tasks'],
    ]"
>
    <x-slot name="actions">
        @if ($user->can('task.create'))
            <x-ui.button as="a" href="{{ url('/task/create') }}" variant="primary" icon="plus">
                New task
            </x-ui.button>
        @endif
    </x-slot>
</x-ui.page-header>

{{-- Filter bar: search + status select --}}
<form method="GET" action="{{ url('/task') }}" class="mb-4 flex flex-wrap items-center gap-2">
    <input type="hidden" name="tab" value="{{ $tab }}" />

    <div class="relative flex-1 min-w-[220px] max-w-md">
        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
            <x-ui.icon name="search" />
        </span>
        <input
            type="search"
            name="q"
            value="{{ $searchTerm }}"
            placeholder="Search task name…"
            class="block w-full h-9 rounded border border-slate-300 bg-white pl-9 pr-3 text-sm text-slate-900 placeholder:text-slate-400 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600"
        />
    </div>

    @if ($statuses && $statuses->count() > 0)
        <select name="status"
                class="h-9 rounded border border-slate-300 bg-white pl-3 pr-9 text-sm text-slate-700 shadow-subtle appearance-none focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
            <option value="">All statuses</option>
            @foreach ($statuses as $s)
                <option value="{{ $s->id }}" @if ($statusFilter == $s->id) selected @endif>{{ $s->name }}</option>
            @endforeach
        </select>
    @endif

    <x-ui.button type="submit" variant="secondary" icon="filter">Apply</x-ui.button>

    @if ($searchTerm !== '' || $statusFilter !== '')
        <a href="{{ url('/task?tab=' . $tab) }}" class="text-xs text-slate-500 hover:text-slate-700 underline-offset-2 hover:underline">
            Clear
        </a>
    @endif
</form>

{{-- Tab bar --}}
<div class="border-b border-slate-200 mb-0" role="tablist" aria-label="Task buckets">
    <nav class="-mb-px flex gap-6 overflow-x-auto">
        @foreach ($tabs as $t)
            @php
                $isActive = $t['key'] === $tab;
                $queryString = http_build_query(array_merge(request()->query(), ['tab' => $t['key']]));
            @endphp
            <a href="{{ url('/task') }}?{{ $queryString }}"
               role="tab"
               aria-selected="{{ $isActive ? 'true' : 'false' }}"
               class="group inline-flex items-center gap-2 whitespace-nowrap border-b-2 px-1 pb-3 pt-2 text-sm transition-colors
                      {{ $isActive
                          ? 'border-primary-600 text-primary-700 font-medium'
                          : 'border-transparent text-slate-600 hover:text-slate-900 hover:border-slate-300' }}">
                <span>{{ $t['label'] }}</span>
                <span class="inline-flex h-5 min-w-[1.25rem] items-center justify-center rounded-full px-1.5 text-xs font-medium
                             {{ $isActive ? 'bg-primary-50 text-primary-700' : 'bg-slate-100 text-slate-600' }}">
                    {{ $t['count'] }}
                </span>
            </a>
        @endforeach
    </nav>
</div>

{{-- Data table --}}
<div class="rounded-b border border-t-0 border-slate-200 bg-white">
    @if ($paginator->isEmpty())
        <div class="flex flex-col items-center justify-center px-6 py-16 text-center">
            <span class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                <x-ui.icon name="check-circle-2" size="lg" />
            </span>
            <h3 class="text-sm font-semibold text-slate-900">
                @if ($tab === 'todo')      Nothing to do
                @elseif ($tab === 'completed') No completed tasks yet
                @else                          No aborted tasks
                @endif
            </h3>
            <p class="mt-1 max-w-sm text-sm text-slate-500">
                @if ($tab === 'todo')
                    You're all caught up. Create a new task when something comes up.
                @elseif ($tab === 'completed')
                    Completed tasks will appear here once you start finishing them.
                @else
                    Aborted tasks appear here when someone cancels them.
                @endif
            </p>
            @if ($tab === 'todo' && $user->can('task.create'))
                <div class="mt-4">
                    <x-ui.button as="a" href="{{ url('/task/create') }}" variant="primary" icon="plus" size="sm">
                        New task
                    </x-ui.button>
                </div>
            @endif
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wide text-slate-500">Task</th>
                        <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wide text-slate-500">Owner</th>
                        <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wide text-slate-500">Tour</th>
                        <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wide text-slate-500">Priority</th>
                        <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wide text-slate-500">Deadline</th>
                        <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wide text-slate-500">Status</th>
                        <th class="px-4 py-2 text-right text-xs font-medium uppercase tracking-wide text-slate-500"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($paginator as $task)
                        @php
                            $deadline = $task->dead_line ? \Carbon\Carbon::parse($task->dead_line) : null;
                            $overdue  = $deadline && $deadline->lt($today) && $tab === 'todo';
                            $priority = $priorityFor($task->priority ?? null);
                            $statusName = optional($task->status)->name ?? '—';
                            $statusVar  = $statusVariant($statusName);
                            $ownerName  = optional($task->assignedTo)->name
                                        ?? ($task->assigned_users->first()->name ?? null)
                                        ?? null;

                            // Task "name". The model is sometimes `title`,
                            // sometimes `content` (the table has both).
                            $taskName   = $task->title ?? $task->name ?? trim(strip_tags((string) $task->content)) ?: 'Untitled task';
                            if (mb_strlen($taskName) > 90) {
                                $taskName = mb_substr($taskName, 0, 90) . '…';
                            }
                        @endphp
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3">
                                <a href="{{ url('/task/' . $task->id . '/edit') }}" class="block min-w-0 group">
                                    <span class="block truncate text-sm font-medium text-slate-900 group-hover:text-primary-700">
                                        {{ $taskName }}
                                    </span>
                                    @if ($task->epic && optional($task->epic)->name)
                                        <span class="block truncate text-xs text-slate-500 mt-0.5">{{ $task->epic->name }}</span>
                                    @endif
                                </a>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600 max-w-[160px] truncate">
                                @if ($ownerName)
                                    <span class="inline-flex items-center gap-2">
                                        <x-ui.avatar :name="$ownerName" size="xs" />
                                        <span class="truncate">{{ $ownerName }}</span>
                                    </span>
                                @else
                                    <span class="text-slate-400">Unassigned</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600 max-w-[180px] truncate">
                                @if (optional($task->tour)->name)
                                    <a href="{{ url('/tour/' . $task->tour->id) }}" class="hover:text-primary-700 hover:underline underline-offset-2">{{ $task->tour->name }}</a>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <x-ui.badge :variant="$priority['variant']" dot>{{ $priority['label'] }}</x-ui.badge>
                            </td>
                            <td class="px-4 py-3 text-sm whitespace-nowrap {{ $overdue ? 'text-danger-700 font-medium' : 'text-slate-700' }}">
                                @if ($deadline)
                                    {{ $deadline->translatedFormat('M j, Y') }}
                                    @if ($overdue)
                                        <span class="ml-1 text-xs font-medium uppercase tracking-wide text-danger-600">overdue</span>
                                    @endif
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <x-ui.badge :variant="$statusVar">{{ $statusName }}</x-ui.badge>
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ url('/task/' . $task->id . '/edit') }}"
                                       class="inline-flex h-8 w-8 items-center justify-center rounded text-slate-400 hover:bg-slate-100 hover:text-slate-700"
                                       title="Edit">
                                        <x-ui.icon name="edit" />
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination footer (the active paginator drives this) --}}
        @if ($paginator->hasPages())
            <div class="flex items-center justify-between border-t border-slate-200 px-4 py-3 text-sm text-slate-500">
                <span>
                    Showing
                    <span class="font-medium text-slate-700">{{ $paginator->firstItem() }}</span>
                    to
                    <span class="font-medium text-slate-700">{{ $paginator->lastItem() }}</span>
                    of
                    <span class="font-medium text-slate-700">{{ $paginator->total() }}</span>
                </span>

                <div class="flex items-center gap-1">
                    @if ($paginator->onFirstPage())
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded text-slate-300 cursor-not-allowed">
                            <x-ui.icon name="chevron-left" />
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}"
                           class="inline-flex h-8 w-8 items-center justify-center rounded text-slate-500 hover:bg-slate-100 hover:text-slate-700"
                           aria-label="Previous page">
                            <x-ui.icon name="chevron-left" />
                        </a>
                    @endif

                    <span class="px-3 text-slate-700">
                        Page <span class="font-medium">{{ $paginator->currentPage() }}</span>
                        / {{ $paginator->lastPage() }}
                    </span>

                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}"
                           class="inline-flex h-8 w-8 items-center justify-center rounded text-slate-500 hover:bg-slate-100 hover:text-slate-700"
                           aria-label="Next page">
                            <x-ui.icon name="chevron-right" />
                        </a>
                    @else
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded text-slate-300 cursor-not-allowed">
                            <x-ui.icon name="chevron-right" />
                        </span>
                    @endif
                </div>
            </div>
        @endif
    @endif
</div>

@endsection
