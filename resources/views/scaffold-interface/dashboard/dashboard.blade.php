{{--
    Staff dashboard — clean SaaS-style layout, Phase 3 redesign.

    Original work. Layout patterns (KPI tile row, two-column main grid,
    activity list) are common to office dashboards and are not copied
    from any specific third party.

    Data passed in by AppController@dashboard:
      $announcements    Collection<App\Announcement>  top 5 recent
      $todoTasks        Collection<App\Task>          assigned to me, not done
      $completedTasks   Collection<App\Task>          assigned to me, done
      $abortedTasks     Collection<App\Task>          assigned to me, aborted
      $statuses         Collection<App\Status>        all task statuses
      $imapConnected    bool
      $main_chat, $chatUsers, $room_types, $tourUsers  (other widgets)
--}}
@extends('scaffold-interface.layouts.tabler-app')

@section('title', 'Dashboard')

@section('post_styles')
    <link href="{{ asset('css/calendar-enhancements.css') }}?v={{ filemtime(public_path('css/calendar-enhancements.css')) }}" rel="stylesheet">
@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $user           = \Illuminate\Support\Facades\Auth::user();
        $todayDate      = \Carbon\Carbon::today();
        $weekEnd        = \Carbon\Carbon::now()->endOfWeek();
        $monthStart     = \Carbon\Carbon::now()->startOfMonth();

        // KPI counts. try/catch so a missing column or N+1 hiccup can't
        // crash the dashboard during the in-flight backend audit work.
        try { $myOpenTasks = $todoTasks->count(); } catch (\Throwable $e) { $myOpenTasks = 0; }

        try {
            $activeToursCount = \App\Tour::where(function ($q) use ($todayDate) {
                $q->whereNull('retirement_date')
                  ->orWhere('retirement_date', '>=', $todayDate->toDateString());
            })->count();
        } catch (\Throwable $e) { $activeToursCount = null; }

        try {
            $completedThisMonth = $completedTasks->filter(function ($t) use ($monthStart) {
                return $t->updated_at && $t->updated_at >= $monthStart;
            })->count();
        } catch (\Throwable $e) { $completedThisMonth = 0; }

        try { $announcementCount = $announcements->count(); } catch (\Throwable $e) { $announcementCount = 0; }

        // Today's tasks (subset of $todoTasks). Ordered by deadline asc.
        try {
            $tasksToday = $todoTasks->filter(function ($t) use ($todayDate) {
                return $t->dead_line && \Carbon\Carbon::parse($t->dead_line)->isSameDay($todayDate);
            })->take(6);
        } catch (\Throwable $e) { $tasksToday = collect(); }

        // This-week tasks (excluding today)
        try {
            $tasksThisWeek = $todoTasks->filter(function ($t) use ($todayDate, $weekEnd) {
                if (!$t->dead_line) return false;
                $d = \Carbon\Carbon::parse($t->dead_line);
                return $d->gt($todayDate->copy()->endOfDay()) && $d->lte($weekEnd);
            })->take(6);
        } catch (\Throwable $e) { $tasksThisWeek = collect(); }

        // Recent tours (last 5 created)
        try {
            $recentTours = \App\Tour::orderByDesc('created_at')->take(5)->get();
        } catch (\Throwable $e) { $recentTours = collect(); }

        // Recent tasks (latest across all states, with assignees) for the
        // "Recent tasks" panel below. We re-fetch directly so we get the
        // latest activity regardless of which "todo/completed/aborted"
        // bucket the assigner-scoped collections above filtered into.
        try {
            $recentTasks = \App\Task::with(['status', 'assignedTo', 'tour', 'assigned_users'])
                ->orderByDesc('updated_at')
                ->take(6)
                ->get();
        } catch (\Throwable $e) { $recentTasks = collect(); }

        // Helpers — avatar initials + a stable colour from the user's name.
        if (!function_exists('tms_user_initials')) {
            function tms_user_initials($name) {
                $name = trim((string) $name);
                if ($name === '') return '?';
                $parts = preg_split('/\s+/', $name);
                if (count($parts) >= 2) {
                    return strtoupper(mb_substr($parts[0], 0, 1) . mb_substr($parts[1], 0, 1));
                }
                return strtoupper(mb_substr($parts[0], 0, 2));
            }
        }
        if (!function_exists('tms_user_color')) {
            function tms_user_color($name) {
                $palette = ['#0d9488','#0073ea','#a25ddc','#e2445c','#00c875','#fdab3d','#f43f5e','#6366f1','#0891b2','#7c3aed'];
                $hash = 0;
                foreach (str_split((string) $name) as $ch) { $hash = ($hash * 31 + ord($ch)) & 0xffffff; }
                return $palette[$hash % count($palette)];
            }
        }
        // Collect assignees for a task: prefer many-to-many; fall back to assignedTo
        if (!function_exists('tms_task_assignees')) {
            function tms_task_assignees($task) {
                $assignees = collect();
                if ($task->relationLoaded('assigned_users') && $task->assigned_users->count()) {
                    $assignees = $task->assigned_users;
                } elseif ($task->assignedTo) {
                    $assignees = collect([$task->assignedTo]);
                }
                return $assignees;
            }
        }
    @endphp

    <x-ui.page-header
        title="Dashboard"
        description="Today's overview — tours, tasks, and recent activity."
        :breadcrumbs="[['label' => 'Home']]"
    >
        <x-slot name="actions">
            @if ($user->can('tour.create'))
                <x-ui.button as="a" href="{{ url('/tour/create') }}" variant="secondary" icon="map-pin">
                    New tour
                </x-ui.button>
            @endif
            @if ($user->can('task.create'))
                <x-ui.button as="a" href="{{ url('/task/create') }}" variant="primary" icon="plus">
                    New task
                </x-ui.button>
            @endif
        </x-slot>
    </x-ui.page-header>

    {{-- KPI tiles --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        @php
            $kpis = [
                [
                    'label'    => 'My open tasks',
                    'value'    => $myOpenTasks,
                    'icon'     => 'list-checks',
                    'href'     => url('/task'),
                    'sub'      => $tasksToday->count() . ' due today',
                    'subColor' => $tasksToday->count() > 0 ? 'text-warning-600' : 'text-slate-500',
                ],
                [
                    'label'    => 'Active tours',
                    'value'    => $activeToursCount ?? '—',
                    'icon'     => 'map',
                    'href'     => url('/tour'),
                    'sub'      => 'Currently in progress',
                    'subColor' => 'text-slate-500',
                ],
                [
                    'label'    => 'Completed this month',
                    'value'    => $completedThisMonth,
                    'icon'     => 'check-circle-2',
                    'href'     => url('/task'),
                    'sub'      => 'Closed since ' . $monthStart->format('M j'),
                    'subColor' => 'text-success-700',
                ],
                [
                    'label'    => 'Announcements',
                    'value'    => $announcementCount,
                    'icon'     => 'megaphone',
                    'href'     => url('/announcements'),
                    'sub'      => 'In the last 5',
                    'subColor' => 'text-slate-500',
                ],
            ];
        @endphp

        @foreach ($kpis as $kpi)
            <a href="{{ $kpi['href'] }}" class="group block rounded border border-slate-200 bg-white p-4 transition-colors hover:border-slate-300 hover:bg-slate-50">
                <div class="flex items-start justify-between">
                    <span class="text-xs font-medium uppercase tracking-wide text-slate-500">{{ $kpi['label'] }}</span>
                    <span class="flex h-8 w-8 items-center justify-center rounded-md bg-slate-100 text-slate-500 group-hover:bg-primary-50 group-hover:text-primary-600 transition-colors">
                        <x-ui.icon :name="$kpi['icon']" />
                    </span>
                </div>
                <div class="mt-3 text-2xl font-semibold text-slate-900 leading-none">{{ $kpi['value'] }}</div>
                <div class="mt-2 text-xs {{ $kpi['subColor'] }}">{{ $kpi['sub'] }}</div>
            </a>
        @endforeach
    </div>

    {{-- Main grid: Calendar (left, 8/12) + Today/This week (right, 4/12) --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 mb-6">

        <div class="lg:col-span-8">
            @include('scaffold-interface.dashboard.components.tasks_calendar')
        </div>

        <div class="lg:col-span-4 space-y-4">

            {{-- Today --}}
            <div class="rounded border border-slate-200 bg-white">
                <header class="flex items-center justify-between border-b border-slate-200 px-4 py-3">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900">Today</h3>
                        <p class="mt-0.5 text-xs text-slate-500">{{ $todayDate->translatedFormat('l, F j') }}</p>
                    </div>
                    <a href="{{ url('/task') }}" class="text-xs font-medium text-primary-600 hover:text-primary-700">View all</a>
                </header>
                <ul class="divide-y divide-slate-100">
                    @forelse ($tasksToday as $task)
                        @php $assignees = tms_task_assignees($task); @endphp
                        <li>
                            <a href="{{ url('/task/' . $task->id . '/edit') }}" class="flex items-start gap-3 px-4 py-3 hover:bg-slate-50">
                                <span class="mt-1 h-2 w-2 shrink-0 rounded-full bg-warning-600"></span>
                                <span class="flex-1 min-w-0">
                                    <span class="block truncate text-sm text-slate-900">{{ $task->content ?: 'Untitled task' }}</span>
                                    @if ($task->tour)
                                        <span class="block truncate text-xs text-slate-500 mt-0.5">{{ optional($task->tour)->name }}</span>
                                    @endif
                                </span>
                                @if ($assignees->count())
                                    <span class="shrink-0 flex -space-x-1.5" title="{{ $assignees->pluck('name')->implode(', ') }}">
                                        @foreach ($assignees->take(3) as $u)
                                            <span class="inline-flex h-6 w-6 items-center justify-center rounded-full text-[10px] font-semibold text-white ring-2 ring-white"
                                                  style="background-color: {{ tms_user_color($u->name ?? '') }};">{{ tms_user_initials($u->name ?? '') }}</span>
                                        @endforeach
                                        @if ($assignees->count() > 3)
                                            <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-slate-200 text-[10px] font-semibold text-slate-700 ring-2 ring-white">+{{ $assignees->count() - 3 }}</span>
                                        @endif
                                    </span>
                                @endif
                                @php $statusName = $task->getStatusName(); $statusColor = $task->getStatusColor(); @endphp
                                @if ($statusName && $statusName !== 'Unknown')
                                    <span class="shrink-0 inline-flex items-center rounded px-2 py-0.5 text-xs font-medium"
                                          style="background-color: {{ $statusColor }}20; color: {{ $statusColor }};">{{ $statusName }}</span>
                                @endif
                            </a>
                        </li>
                    @empty
                        <li class="px-4 py-6 text-center text-xs text-slate-500">Nothing due today.</li>
                    @endforelse
                </ul>
            </div>

            {{-- This week --}}
            <div class="rounded border border-slate-200 bg-white">
                <header class="flex items-center justify-between border-b border-slate-200 px-4 py-3">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900">This week</h3>
                        <p class="mt-0.5 text-xs text-slate-500">Through {{ $weekEnd->translatedFormat('F j') }}</p>
                    </div>
                </header>
                <ul class="divide-y divide-slate-100">
                    @forelse ($tasksThisWeek as $task)
                        @php $assignees = tms_task_assignees($task); @endphp
                        <li>
                            <a href="{{ url('/task/' . $task->id . '/edit') }}" class="flex items-start gap-3 px-4 py-3 hover:bg-slate-50">
                                <span class="mt-1 h-2 w-2 shrink-0 rounded-full bg-info-600"></span>
                                <span class="flex-1 min-w-0">
                                    <span class="block truncate text-sm text-slate-900">{{ $task->content ?: 'Untitled task' }}</span>
                                    <span class="block text-xs text-slate-500 mt-0.5">
                                        @if ($task->dead_line)
                                            {{ \Carbon\Carbon::parse($task->dead_line)->translatedFormat('l, M j') }}
                                        @endif
                                    </span>
                                </span>
                                @if ($assignees->count())
                                    <span class="shrink-0 flex -space-x-1.5" title="{{ $assignees->pluck('name')->implode(', ') }}">
                                        @foreach ($assignees->take(3) as $u)
                                            <span class="inline-flex h-6 w-6 items-center justify-center rounded-full text-[10px] font-semibold text-white ring-2 ring-white"
                                                  style="background-color: {{ tms_user_color($u->name ?? '') }};">{{ tms_user_initials($u->name ?? '') }}</span>
                                        @endforeach
                                        @if ($assignees->count() > 3)
                                            <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-slate-200 text-[10px] font-semibold text-slate-700 ring-2 ring-white">+{{ $assignees->count() - 3 }}</span>
                                        @endif
                                    </span>
                                @endif
                            </a>
                        </li>
                    @empty
                        <li class="px-4 py-6 text-center text-xs text-slate-500">No tasks scheduled this week.</li>
                    @endforelse
                </ul>
            </div>

        </div>
    </div>

    {{-- Recent tasks --}}
    <div class="rounded border border-slate-200 bg-white mb-6">
        <header class="flex items-center justify-between border-b border-slate-200 px-4 py-3">
            <div>
                <h3 class="text-sm font-semibold text-slate-900">Recent tasks</h3>
                <p class="mt-0.5 text-xs text-slate-500">Latest task activity and assignees</p>
            </div>
            <a href="{{ url('/task') }}" class="text-xs font-medium text-primary-600 hover:text-primary-700">View all</a>
        </header>
        @if ($recentTasks->isEmpty())
            <div class="px-4 py-8 text-center text-sm text-slate-500">
                <x-ui.icon name="list-checks" size="md" class="mx-auto mb-2 text-slate-300" />
                No tasks yet.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50">
                        <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                            <th class="px-4 py-2.5">Task</th>
                            <th class="px-4 py-2.5">Assignees</th>
                            <th class="px-4 py-2.5">Status</th>
                            <th class="px-4 py-2.5">Deadline</th>
                            <th class="px-4 py-2.5">Updated</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($recentTasks as $task)
                            @php
                                $assignees   = tms_task_assignees($task);
                                $statusName  = $task->getStatusName();
                                $statusColor = $task->getStatusColor();
                                $deadline    = $task->dead_line ? \Carbon\Carbon::parse($task->dead_line) : null;
                                $overdue     = $deadline && $deadline->isPast();
                            @endphp
                            <tr class="hover:bg-slate-50" data-task-row="{{ $task->id }}">
                                <td class="px-4 py-3 max-w-[28rem]">
                                    <span class="dash-inline-edit block truncate font-medium text-slate-900 cursor-text rounded px-1 -mx-1 hover:bg-slate-100"
                                          data-task-id="{{ $task->id }}" data-field="content"
                                          title="Click to edit">{{ $task->content ?: 'Untitled task' }}</span>
                                    @if ($task->tour)
                                        <a href="{{ url('/tour/' . $task->tour->id) }}" class="block truncate text-xs text-slate-500 mt-0.5 hover:text-primary-600">{{ optional($task->tour)->name }}</a>
                                    @endif
                                    <a href="{{ url('/task/' . $task->id . '/edit') }}" class="mt-1 inline-block text-[10px] text-slate-400 hover:text-primary-600">Open →</a>
                                </td>
                                <td class="px-4 py-3">
                                    @if ($assignees->count())
                                        <div class="flex -space-x-1.5" title="{{ $assignees->pluck('name')->implode(', ') }}">
                                            @foreach ($assignees->take(4) as $u)
                                                <span class="inline-flex h-7 w-7 items-center justify-center rounded-full text-[11px] font-semibold text-white ring-2 ring-white"
                                                      style="background-color: {{ tms_user_color($u->name ?? '') }};">{{ tms_user_initials($u->name ?? '') }}</span>
                                            @endforeach
                                            @if ($assignees->count() > 4)
                                                <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-slate-200 text-[11px] font-semibold text-slate-700 ring-2 ring-white">+{{ $assignees->count() - 4 }}</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-400">Unassigned</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <button type="button"
                                            class="dash-inline-status inline-flex items-center gap-1.5 rounded px-2 py-0.5 text-xs font-medium hover:ring-2 hover:ring-slate-300 transition"
                                            data-task-id="{{ $task->id }}"
                                            data-status-id="{{ $task->status }}"
                                            style="background-color: {{ ($statusName && $statusName !== 'Unknown') ? $statusColor.'20' : '#f1f5f9' }}; color: {{ ($statusName && $statusName !== 'Unknown') ? $statusColor : '#94a3b8' }};"
                                            title="Click to change status">
                                        <span class="dash-status-label">{{ ($statusName && $statusName !== 'Unknown') ? $statusName : 'Set status' }}</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                                    </button>
                                </td>
                                <td class="px-4 py-3 text-xs {{ $overdue ? 'text-danger-600 font-medium' : 'text-slate-600' }}">
                                    {{ $deadline ? $deadline->translatedFormat('M j, H:i') : '—' }}
                                </td>
                                <td class="px-4 py-3 text-xs text-slate-500">
                                    {{ $task->updated_at ? $task->updated_at->diffForHumans() : '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Recent tours + Announcements --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">

        <div class="rounded border border-slate-200 bg-white">
            <header class="flex items-center justify-between border-b border-slate-200 px-4 py-3">
                <h3 class="text-sm font-semibold text-slate-900">Recent tours</h3>
                <a href="{{ url('/tour') }}" class="text-xs font-medium text-primary-600 hover:text-primary-700">View all</a>
            </header>
            @if ($recentTours->isEmpty())
                <div class="px-4 py-8 text-center text-sm text-slate-500">
                    <x-ui.icon name="map" size="md" class="mx-auto mb-2 text-slate-300" />
                    No tours yet.
                </div>
            @else
                <ul class="divide-y divide-slate-100">
                    @foreach ($recentTours as $tour)
                        <li>
                            <a href="{{ url('/tour/' . $tour->id) }}" class="flex items-start gap-3 px-4 py-3 hover:bg-slate-50">
                                <span class="mt-1 h-2 w-2 shrink-0 rounded-full" style="background-color: {{ \App\Tour::$statusColors[$tour->status] ?? '#94a3b8' }}"></span>
                                <span class="flex-1 min-w-0">
                                    <span class="block truncate text-sm font-medium text-slate-900">{{ $tour->name }}</span>
                                    <span class="block text-xs text-slate-500 mt-0.5">
                                        @if ($tour->departure_date)
                                            {{ \Carbon\Carbon::parse($tour->departure_date)->translatedFormat('M j') }}
                                            @if ($tour->retirement_date)
                                                — {{ \Carbon\Carbon::parse($tour->retirement_date)->translatedFormat('M j') }}
                                            @endif
                                        @else
                                            No dates
                                        @endif
                                        @if ($tour->pax) · {{ $tour->pax }} pax @endif
                                    </span>
                                </span>
                                <x-ui.icon name="chevron-right" size="xs" class="mt-1 text-slate-300" />
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="rounded border border-slate-200 bg-white">
            <header class="flex items-center justify-between border-b border-slate-200 px-4 py-3">
                <h3 class="text-sm font-semibold text-slate-900">Announcements</h3>
                <a href="{{ url('/announcements') }}" class="text-xs font-medium text-primary-600 hover:text-primary-700">View all</a>
            </header>
            @if ($announcements->isEmpty())
                <div class="px-4 py-8 text-center text-sm text-slate-500">
                    <x-ui.icon name="megaphone" size="md" class="mx-auto mb-2 text-slate-300" />
                    No announcements yet.
                </div>
            @else
                <ul class="divide-y divide-slate-100">
                    @foreach ($announcements as $announcement)
                        <li class="px-4 py-3">
                            <div class="flex items-start gap-3">
                                <span class="mt-1 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-slate-100 text-slate-500">
                                    <x-ui.icon name="megaphone" size="xs" />
                                </span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-slate-900 truncate">
                                        {{ $announcement->title ?? $announcement->subject ?? 'Untitled' }}
                                    </p>
                                    <p class="text-xs text-slate-500 mt-0.5">
                                        @if ($announcement->sender) {{ $announcement->sender }} @endif
                                        @if ($announcement->created_at) · {{ $announcement->created_at->diffForHumans() }} @endif
                                    </p>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

    </div>

    {{-- Modals required by inline JS (existing partials, untouched) --}}
    @include('component.modal_add_tour')
    @include('scaffold-interface.dashboard.components.create_task_popup')

    {{-- Inline-edit script for the Recent tasks panel.
         - Click .dash-inline-edit (the title)   → input field, Enter to save, Esc to cancel
         - Click .dash-inline-status (the pill)  → status dropdown, pick to save
         POSTs to /task/{id}/update-field with {field, value}. CSRF taken
         from the <meta name="csrf-token"> tag at the top of @section('content'). --}}
    <script>
    (function () {
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        // Task-type statuses for the dropdown — only pass id/name/color the picker needs.
        const TASK_STATUSES = @json(($statuses ?? collect())->where('type', 'task')->values()->map(fn($s) => [
            'id'    => $s->id,
            'name'  => $s->name,
            'color' => $s->color ?: '#64748b',
        ]));

        function saveField(taskId, field, value) {
            return fetch('/task/' + taskId + '/update-field', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
                body: JSON.stringify({ field: field, value: value }),
            }).then(r => r.json().catch(() => ({})).then(j => ({ ok: r.ok, body: j })));
        }

        // ---------- Inline content edit ----------
        document.addEventListener('click', function (e) {
            const span = e.target.closest('.dash-inline-edit');
            if (!span || span.dataset.editing === '1') return;

            const taskId = span.dataset.taskId;
            const field  = span.dataset.field;
            const original = span.textContent.trim();

            span.dataset.editing = '1';
            const input = document.createElement('input');
            input.type = 'text';
            input.value = original === 'Untitled task' ? '' : original;
            input.className = 'block w-full h-7 rounded border border-primary-500 bg-white px-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-primary-500/30';
            const placeholder = span.cloneNode(true);
            span.replaceWith(input);
            input.focus();
            input.select();

            let committed = false;

            function restore(value, edited) {
                if (committed) return;
                committed = true;
                placeholder.textContent = value || 'Untitled task';
                placeholder.dataset.editing = '0';
                input.replaceWith(placeholder);
                if (edited) {
                    placeholder.style.transition = 'background-color .8s ease';
                    placeholder.style.backgroundColor = 'rgba(13,148,136,0.15)';
                    setTimeout(() => placeholder.style.backgroundColor = '', 800);
                }
            }

            input.addEventListener('blur', () => commit(input.value.trim()));
            input.addEventListener('keydown', (ev) => {
                if (ev.key === 'Enter')  { ev.preventDefault(); commit(input.value.trim()); }
                if (ev.key === 'Escape') { ev.preventDefault(); restore(original, false); }
            });

            function commit(newValue) {
                if (newValue === original) { restore(original, false); return; }
                if (!newValue) { restore(original, false); return; }
                saveField(taskId, field, newValue).then(({ ok }) => {
                    restore(ok ? newValue : original, ok);
                }).catch(() => restore(original, false));
            }
        });

        // ---------- Status dropdown ----------
        function closeAllStatusPickers() {
            document.querySelectorAll('.dash-status-popup').forEach(el => el.remove());
        }

        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.dash-inline-status');
            if (!btn) { closeAllStatusPickers(); return; }
            e.stopPropagation();

            // Toggle if already open for this button
            const existing = document.querySelector('.dash-status-popup');
            const wasOpenFor = existing?.dataset.taskId === btn.dataset.taskId;
            closeAllStatusPickers();
            if (wasOpenFor) return;

            const popup = document.createElement('div');
            popup.className = 'dash-status-popup rounded border border-slate-200 bg-white shadow-lg py-1 z-50';
            popup.style.position = 'absolute';
            popup.style.minWidth = '180px';
            popup.dataset.taskId = btn.dataset.taskId;

            TASK_STATUSES.forEach(s => {
                const item = document.createElement('button');
                item.type = 'button';
                item.className = 'flex w-full items-center gap-2 px-3 py-1.5 text-left text-sm hover:bg-slate-50';
                item.innerHTML = `<span class="inline-block h-2.5 w-2.5 rounded-full" style="background-color:${s.color}"></span><span class="text-slate-700">${s.name}</span>`;
                item.onclick = (ev) => {
                    ev.stopPropagation();
                    saveField(btn.dataset.taskId, 'status', s.id).then(({ ok, body }) => {
                        if (ok) {
                            const color = body?.status?.color || s.color;
                            const name  = body?.status?.name  || s.name;
                            btn.querySelector('.dash-status-label').textContent = name;
                            btn.style.backgroundColor = color + '20';
                            btn.style.color = color;
                            btn.dataset.statusId = s.id;
                            btn.style.transition = 'box-shadow .6s ease';
                            btn.style.boxShadow = '0 0 0 3px rgba(13,148,136,0.25)';
                            setTimeout(() => btn.style.boxShadow = '', 600);
                        }
                        closeAllStatusPickers();
                    }).catch(() => closeAllStatusPickers());
                };
                popup.appendChild(item);
            });

            document.body.appendChild(popup);
            const r = btn.getBoundingClientRect();
            popup.style.top  = (r.bottom + window.scrollY + 4) + 'px';
            popup.style.left = (r.left + window.scrollX) + 'px';
        });

        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeAllStatusPickers(); });
    })();
    </script>

@endsection

@section('post_scripts_calendar')
    {{-- FullCalendar bootstrap. The element it renders into is inside
         scaffold-interface.dashboard.components.tasks_calendar and is
         fed by /home/getToursTasksForCalendar. --}}
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var calendarEl = document.getElementById('bootsnipp-calendar');
            if (!calendarEl) return;

            var COLOURS = { tour: '#0d9488', task: '#2563eb', holiday: '#dc2626' };

            function classify(event) {
                if (event.id === 'Holiday') return 'holiday';
                if (event.c_type === 'month') return 'task';
                return 'tour';
            }

            function transform(event) {
                var kind  = classify(event);
                var color = event.backgroundColor || COLOURS[kind] || '#64748b';
                return {
                    id: event.id,
                    title: event.title,
                    start: event.date,
                    allDay: event.allDay !== undefined ? event.allDay : false,
                    backgroundColor: color,
                    borderColor: color,
                    textColor: '#ffffff',
                    classNames: ['event-' + kind],
                    extendedProps: { original: event, c_type: event.c_type }
                };
            }

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                height: 'auto',
                expandRows: true,
                headerToolbar: { left: 'prev,next today', center: 'title', right: '' },
                events: function (info, successCallback, failureCallback) {
                    var url = '/home/getToursTasksForCalendar?start=' + info.startStr + '&end=' + info.endStr;
                    fetch(url, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        },
                        credentials: 'same-origin'
                    })
                    .then(function (response) {
                        if (!response.ok) {
                            if (response.status === 401 || response.status === 403) {
                                successCallback([]);
                                return null;
                            }
                            throw new Error('Network response not OK: ' + response.status);
                        }
                        return response.json();
                    })
                    .then(function (events) {
                        if (!events) return;
                        successCallback(events.map(transform));
                    })
                    .catch(function (err) {
                        console.error('Error loading calendar data:', err);
                        failureCallback(err);
                    });
                },
                eventClick: function (info) {
                    if (info.event.url) {
                        window.open(info.event.url);
                        info.jsEvent.preventDefault();
                    } else if (info.event && info.event.id !== 'Holiday' && info.event.id !== 'error-1' && !String(info.event.id || '').startsWith('sample-')) {
                        window.location = '{{ url("task") }}/' + info.event.id + '/edit?calendar_edit=1';
                    }
                }
            });

            calendar.render();
        });
    </script>
@endsection
