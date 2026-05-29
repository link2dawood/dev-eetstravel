{{--
    /holiday — Calendar view.
    Custom month-grid built with Tailwind + vanilla JS. Events come from the
    controller as JSON (calendarEvents prop). Each event is keyed by its date
    (YYYY-MM-DD) and rendered as a pill in the corresponding day cell.

    Visual design mirrors a clean Linear/Notion-style calendar:
      - Sticky top bar: month/year title, week badge, date range, today nav,
        view dropdown, primary Add event CTA.
      - Day-of-week header row.
      - 6-row month grid with outside-month days dimmed.
      - Today's day number in a filled primary-600 circle.
      - Events render as soft-tinted pills with name + time.
      - "+N more" overflow when a day has more than 3 events.
      - Hover any day cell to reveal a small + icon for quick-add.
--}}
@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Calendar')

@section('content')
<x-ui.page-header
    title="Calendar"
    description="Holidays and events across the year."
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Calendar'],
    ]"
>
    <x-slot name="actions">
        <x-ui.button as="a" href="{{ route('holiday.create') }}" icon="plus">Add event</x-ui.button>
    </x-slot>
</x-ui.page-header>

<div class="rounded-lg border border-slate-200 bg-white" id="calendar-root" data-events='@json($calendarEvents)'>

    {{-- Header --}}
    <div class="flex flex-col gap-4 px-4 py-3 border-b border-slate-200 sm:flex-row sm:items-center sm:justify-between">
        {{-- Left: date pill + month + range --}}
        <div class="flex items-start gap-3">
            <div class="rounded-md border border-slate-200 bg-white px-3 py-2 text-center min-w-[60px]">
                <div class="text-[10px] font-medium uppercase tracking-wider text-primary-600" data-cal-month-short></div>
                <div class="text-xl font-semibold text-primary-700" data-cal-today-day></div>
            </div>
            <div class="min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-lg font-semibold text-slate-900" data-cal-title></h1>
                    <span class="inline-flex items-center rounded-full border border-slate-200 px-2 py-0.5 text-xs text-slate-600" data-cal-week-badge></span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5" data-cal-range></p>
            </div>
        </div>

        {{-- Right: search + nav + view + add --}}
        <div class="flex items-center gap-2 flex-wrap">
            <button type="button" class="inline-flex h-9 w-9 items-center justify-center rounded text-slate-500 hover:bg-slate-100" aria-label="Search">
                <x-ui.icon name="search" size="sm" />
            </button>

            {{-- Nav cluster: prev | Today | next --}}
            <div class="inline-flex rounded-md shadow-subtle border border-slate-200 overflow-hidden">
                <button type="button" data-cal-prev class="inline-flex h-9 w-9 items-center justify-center text-slate-600 hover:bg-slate-50" aria-label="Previous month">
                    <x-ui.icon name="arrow-left" size="sm" />
                </button>
                <button type="button" data-cal-today class="inline-flex h-9 items-center justify-center px-4 text-sm font-medium text-slate-700 hover:bg-slate-50 border-x border-slate-200">
                    Today
                </button>
                <button type="button" data-cal-next class="inline-flex h-9 w-9 items-center justify-center text-slate-600 hover:bg-slate-50" aria-label="Next month">
                    <x-ui.icon name="arrow-right" size="sm" />
                </button>
            </div>

            {{-- View dropdown (visual only) --}}
            <div class="inline-flex rounded-md shadow-subtle border border-slate-200">
                <button type="button" class="inline-flex h-9 items-center gap-1.5 rounded-md px-3 text-sm font-medium text-slate-700 hover:bg-slate-50">
                    Month view
                    <x-ui.icon name="chevron-down" size="xs" />
                </button>
            </div>

            <x-ui.button as="a" href="{{ route('holiday.create') }}" icon="plus" variant="primary">Add event</x-ui.button>
        </div>
    </div>

    {{-- Day-of-week strip --}}
    <div class="grid grid-cols-7 border-b border-slate-200 bg-slate-50">
        @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $dow)
            <div class="px-3 py-2 text-xs font-medium text-slate-500 {{ $loop->first ? '' : 'border-l border-slate-200' }}">
                {{ $dow }}
            </div>
        @endforeach
    </div>

    {{-- Month grid (populated by JS) --}}
    <div id="cal-grid" class="grid grid-cols-7 grid-rows-6 min-h-[680px]"></div>
</div>

@endsection

@push('scripts')
<script>
(function () {
    const root = document.getElementById('calendar-root');
    if (!root) return;
    const grid = document.getElementById('cal-grid');

    // ----- Data -----
    let events = [];
    try { events = JSON.parse(root.getAttribute('data-events') || '[]'); } catch (e) { events = []; }

    // Group events by ISO date (YYYY-MM-DD).
    const eventsByDate = events.reduce(function (acc, ev) {
        if (!ev.date) return acc;
        (acc[ev.date] = acc[ev.date] || []).push(ev);
        return acc;
    }, {});

    // ----- State -----
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    let cursor = new Date(today.getFullYear(), today.getMonth(), 1);

    // ----- Helpers -----
    const MONTHS = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    const MONTHS_SHORT = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    function pad(n) { return n < 10 ? '0' + n : '' + n; }
    function ymd(d) { return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate()); }
    function isoWeek(d) {
        const t = new Date(d.valueOf());
        const day = (t.getDay() + 6) % 7;
        t.setDate(t.getDate() - day + 3);
        const firstThursday = new Date(t.getFullYear(), 0, 4);
        const diff = t - firstThursday;
        return 1 + Math.round((diff / 86400000 - 3 + ((firstThursday.getDay() + 6) % 7)) / 7);
    }

    // Soft palette for events that don't have a colour set.
    const PALETTE = [
        { bg: 'bg-info-50',    text: 'text-info-700',    ring: 'border-info-200' },
        { bg: 'bg-success-50', text: 'text-success-700', ring: 'border-success-200' },
        { bg: 'bg-warning-50', text: 'text-warning-700', ring: 'border-warning-200' },
        { bg: 'bg-danger-50',  text: 'text-danger-700',  ring: 'border-danger-200' },
        { bg: 'bg-primary-50', text: 'text-primary-700', ring: 'border-primary-200' },
        { bg: 'bg-slate-100',  text: 'text-slate-700',   ring: 'border-slate-200' },
    ];
    function paletteForEvent(ev) {
        if (ev.color) {
            // Render the supplied colour as an inline tint.
            return null;
        }
        // Stable assignment by id so the same event keeps the same colour.
        const seed = (ev.id || 0) % PALETTE.length;
        return PALETTE[seed];
    }

    // ----- Render -----
    function render() {
        // Header bits
        const monthName = MONTHS[cursor.getMonth()];
        const year = cursor.getFullYear();
        root.querySelector('[data-cal-title]').textContent = monthName + ' ' + year;
        root.querySelector('[data-cal-month-short]').textContent = MONTHS_SHORT[today.getMonth()].toUpperCase();
        root.querySelector('[data-cal-today-day]').textContent = today.getDate();
        root.querySelector('[data-cal-week-badge]').textContent = 'Week ' + isoWeek(new Date(cursor.getFullYear(), cursor.getMonth(), 15));

        const firstOfMonth = new Date(cursor.getFullYear(), cursor.getMonth(), 1);
        const lastOfMonth = new Date(cursor.getFullYear(), cursor.getMonth() + 1, 0);
        root.querySelector('[data-cal-range]').textContent =
            MONTHS[cursor.getMonth()] + ' ' + firstOfMonth.getDate() + ', ' + year +
            ' – ' +
            MONTHS[cursor.getMonth()] + ' ' + lastOfMonth.getDate() + ', ' + year;

        // Grid: start on the Sunday on or before day-1.
        const startDate = new Date(firstOfMonth);
        startDate.setDate(startDate.getDate() - startDate.getDay());

        grid.innerHTML = '';
        for (let i = 0; i < 42; i++) {
            const d = new Date(startDate);
            d.setDate(d.getDate() + i);
            const outsideMonth = d.getMonth() !== cursor.getMonth();
            const isToday = d.getTime() === today.getTime();
            const isSunday = d.getDay() === 0;
            const inLastRow = i >= 35;

            const cell = document.createElement('div');
            cell.className = [
                'relative px-2 pt-2 pb-1.5 min-h-[112px]',
                'border-slate-200',
                isSunday ? '' : 'border-l',
                inLastRow ? '' : 'border-b',
                outsideMonth ? 'bg-slate-50/40' : 'bg-white',
                'group',
            ].join(' ');

            // Day number (today gets a circle).
            const dayLabel = document.createElement('div');
            dayLabel.className = 'flex items-center justify-between';
            const num = document.createElement('span');
            if (isToday) {
                num.className = 'inline-flex h-7 w-7 items-center justify-center rounded-full bg-primary-600 text-sm font-medium text-white';
            } else {
                num.className = 'inline-block text-sm font-medium ' + (outsideMonth ? 'text-slate-400' : 'text-slate-900');
            }
            num.textContent = d.getDate();
            dayLabel.appendChild(num);

            // Hover quick-add (link to create form, prefilled with date).
            const addBtn = document.createElement('a');
            addBtn.href = '{{ route('holiday.create') }}';
            addBtn.className = 'opacity-0 group-hover:opacity-100 transition-opacity inline-flex h-6 w-6 items-center justify-center rounded-full border border-slate-300 bg-white text-slate-400 hover:text-slate-700';
            addBtn.title = 'Add event';
            addBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14"/><path d="M5 12h14"/></svg>';
            dayLabel.appendChild(addBtn);
            cell.appendChild(dayLabel);

            // Events
            const key = ymd(d);
            const dayEvents = eventsByDate[key] || [];
            const visible = dayEvents.slice(0, 3);
            const overflow = dayEvents.length - visible.length;

            const evWrap = document.createElement('div');
            evWrap.className = 'mt-1 space-y-1';
            visible.forEach(function (ev) {
                const a = document.createElement('a');
                a.href = ev.edit_url || '#';
                const pal = paletteForEvent(ev);
                if (pal) {
                    a.className = [
                        'flex items-center justify-between gap-2 rounded-md border',
                        pal.ring, pal.bg, pal.text,
                        'px-2 py-1 text-xs font-medium hover:brightness-95',
                    ].join(' ');
                } else {
                    a.className = 'flex items-center justify-between gap-2 rounded-md border px-2 py-1 text-xs font-medium hover:brightness-95';
                    a.style.background = hexToTint(ev.color, 0.10);
                    a.style.color = ev.color;
                    a.style.borderColor = hexToTint(ev.color, 0.20);
                }
                const left = document.createElement('span');
                left.className = 'truncate';
                left.textContent = ev.name;
                a.appendChild(left);
                if (ev.time) {
                    const t = document.createElement('span');
                    t.className = 'shrink-0 text-[10px] opacity-80';
                    t.textContent = ev.time;
                    a.appendChild(t);
                }
                evWrap.appendChild(a);
            });
            if (overflow > 0) {
                const more = document.createElement('div');
                more.className = 'text-[11px] text-slate-500 px-1';
                more.textContent = overflow + ' more...';
                evWrap.appendChild(more);
            }
            cell.appendChild(evWrap);

            grid.appendChild(cell);
        }
    }

    // Convert a CSS hex (#rrggbb) into an rgba tint so we can render arbitrary
    // user-chosen colours without depending on Tailwind classes for them.
    function hexToTint(hex, alpha) {
        if (!hex) return '';
        let c = hex.replace('#', '');
        if (c.length === 3) c = c.split('').map(function (x) { return x + x; }).join('');
        if (c.length !== 6) return hex;
        const r = parseInt(c.substr(0, 2), 16);
        const g = parseInt(c.substr(2, 2), 16);
        const b = parseInt(c.substr(4, 2), 16);
        return 'rgba(' + r + ',' + g + ',' + b + ',' + alpha + ')';
    }

    // ----- Wire nav -----
    root.querySelector('[data-cal-prev]').addEventListener('click', function () {
        cursor = new Date(cursor.getFullYear(), cursor.getMonth() - 1, 1);
        render();
    });
    root.querySelector('[data-cal-next]').addEventListener('click', function () {
        cursor = new Date(cursor.getFullYear(), cursor.getMonth() + 1, 1);
        render();
    });
    root.querySelector('[data-cal-today]').addEventListener('click', function () {
        cursor = new Date(today.getFullYear(), today.getMonth(), 1);
        render();
    });

    render();
})();
</script>
@endpush
