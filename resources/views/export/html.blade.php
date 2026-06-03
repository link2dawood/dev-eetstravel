@if(isset($isDoc) && $isDoc)
<?php
header("Content-Type: application/vnd.ms-word");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("content-disposition: attachment; filename=" . ($download_name ?? 'itinerary.doc'));
?>
@endif
@php
    // ─── service taxonomy ──────────────────────────────────────────────────
    $serviceTypeMap = [
        0 => 'Hotel',
        1 => 'Event',
        2 => 'Guide',
        3 => 'Transfer',
        4 => 'Restaurant',
        5 => 'Package',
        6 => 'Cruise',
        7 => 'Flight',
    ];

    // ─── safe parsers ──────────────────────────────────────────────────────
    $tryParse = function ($v) {
        if (empty($v)) return null;
        try { return \Carbon\Carbon::parse($v); }
        catch (\Throwable $e) { return null; }
    };

    $fmtTime = function ($v) use ($tryParse) {
        $c = $tryParse($v);
        return $c ? $c->format('H:i') : null;
    };

    // ─── tour-level metadata ───────────────────────────────────────────────
    $depCarbon  = $tryParse($tour->departure_date ?? null);
    $retCarbon  = $tryParse($tour->retirement_date ?? null);
    $tourLength = ($depCarbon && $retCarbon) ? ($depCarbon->diffInDays($retCarbon) + 1) : null;

    // ─── derived collections ───────────────────────────────────────────────
    $transferList = collect($tourTransfers ?? [])->where('type', 3)->values();

    $hotelIndex = [];
    foreach ($tourDays ?? [] as $day) {
        foreach ($day->packages ?? [] as $p) {
            if ((int) ($p->type ?? -1) !== 0) continue;
            $key = ($p->reference ?? '_') . '|' . ($p->name ?? '');
            if (!isset($hotelIndex[$key])) {
                $hotelIndex[$key] = ['name' => $p->name ?? 'Hotel', 'package' => $p, 'dates' => []];
            }
            $hotelIndex[$key]['dates'][] = $day->date;
        }
    }
    $totalNights = array_sum(array_map(fn ($h) => count($h['dates']), $hotelIndex));

    $routeCities = array_values(array_filter([
        $tour->city_begin ?? null,
        $tour->city_end ?? null,
    ]));

    $issuedAt       = \Carbon\Carbon::now();
    $personInCharge = optional($usersResponsible)->name;

    // ─── logo lookup ───────────────────────────────────────────────────────
    $logoFile = null;
    foreach (['eets_logo.png', 'Eets_logo.png', 'EETS_logo.png', 'eets_logo.jpg'] as $c) {
        if (is_file(public_path('img/' . $c))) { $logoFile = $c; break; }
    }
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Itinerary — {{ $tour->name ?? 'Tour' }} — EETS</title>
<style>
:root {
    --bg:       #ffffff;
    --paper:    #fafaf9;
    --ink:      #0a0a0a;
    --ink-2:    #4a4a4a;
    --ink-3:    #757575;
    --ink-mute: #b3b3b3;
    --hair:     #e5e5e3;
    --hair-2:   #f0f0ee;
    --accent:   #9d2222;

    --sans: -apple-system, BlinkMacSystemFont, "Segoe UI Variable Text", "Segoe UI", system-ui, "Helvetica Neue", Arial, sans-serif;
    --mono: "SF Mono", "JetBrains Mono", "Cascadia Mono", "Menlo", "Consolas", "Liberation Mono", monospace;
}

@page          { size: A4; margin: 18mm 18mm 20mm; }
@page :first   { margin-top: 22mm; }

* { box-sizing: border-box; margin: 0; padding: 0; }
html { -webkit-text-size-adjust: 100%; }
body {
    background: var(--paper);
    color: var(--ink);
    font-family: var(--sans);
    font-size: 11pt;
    line-height: 1.55;
    font-feature-settings: "kern" 1, "liga" 1, "tnum" 1, "ss01" 1;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    text-rendering: geometricPrecision;
}

img { display: block; max-width: 100%; }
a   { color: inherit; text-decoration: none; }

.doc {
    max-width: 200mm;
    margin: 0 auto;
    background: var(--bg);
    padding: 22mm 24mm 24mm;
    position: relative;
}

@media screen {
    body { padding: 40px 16px; background: #ebe9e3; }
    .doc {
        background: var(--bg);
        box-shadow: 0 0 0 1px var(--hair),
                    0 24px 72px -28px rgba(20, 20, 18, 0.18);
    }
}

/* ─────────────────────────── COVER ─────────────────────────────────── */

.cover {
    min-height: 246mm;
    display: flex;
    flex-direction: column;
    page-break-after: always;
}

.cover__head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-bottom: 12mm;
}

.brand {
    display: flex;
    align-items: center;
    gap: 7mm;
}

.brand__logo  { height: 32px; width: auto; }

.brand__wordmark {
    font-size: 14pt;
    font-weight: 600;
    letter-spacing: 0.02em;
    color: var(--accent);
    line-height: 1;
}

.brand__sub {
    font-size: 7.5pt;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    color: var(--ink-3);
    line-height: 1.4;
    font-weight: 500;
}

.ref {
    text-align: right;
    line-height: 1.4;
}

.ref__key {
    display: block;
    font-size: 7pt;
    letter-spacing: 0.22em;
    text-transform: uppercase;
    color: var(--ink-3);
    font-weight: 500;
    margin-bottom: 3px;
}

.ref__val {
    font-family: var(--mono);
    font-size: 10pt;
    color: var(--ink);
    letter-spacing: 0.02em;
}

.cover__body {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 32mm 0 24mm;
}

.cover__doctype {
    font-size: 8.5pt;
    letter-spacing: 0.32em;
    text-transform: uppercase;
    color: var(--ink-3);
    font-weight: 500;
    margin-bottom: 14mm;
}

.cover__title {
    font-size: 30pt;
    font-weight: 500;
    line-height: 1.12;
    letter-spacing: -0.012em;
    color: var(--accent);
    max-width: 160mm;
    margin-bottom: 9mm;
}

.cover__subtitle {
    font-size: 12pt;
    color: var(--ink-2);
    font-weight: 400;
    line-height: 1.5;
    letter-spacing: 0;
}

.cover__subtitle .sep {
    color: var(--ink-mute);
    margin: 0 0.6em;
}

.cover__meta {
    margin-top: auto;
    border-top: 1px solid var(--hair);
    padding-top: 10mm;
}

.meta-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    column-gap: 10mm;
    row-gap: 9mm;
}

.meta-cell .k {
    display: block;
    font-size: 7pt;
    letter-spacing: 0.22em;
    text-transform: uppercase;
    color: var(--ink-3);
    font-weight: 500;
    margin-bottom: 3mm;
}

.meta-cell .v {
    font-size: 11.5pt;
    color: var(--ink);
    font-weight: 400;
    line-height: 1.4;
}

.meta-cell .v.mono {
    font-family: var(--mono);
    font-size: 10pt;
    letter-spacing: 0.01em;
}

.meta-cell .v .plus {
    color: var(--ink-3);
    font-weight: 400;
    margin-left: 1mm;
}

.meta-cell .v .none {
    color: var(--ink-mute);
}

.cover__foot {
    margin-top: 16mm;
    padding-top: 6mm;
    border-top: 1px solid var(--hair-2);
    display: flex;
    justify-content: space-between;
    align-items: baseline;
}

.cover__foot .a {
    font-size: 9pt;
    color: var(--ink-2);
}

.cover__foot .b {
    font-size: 7.5pt;
    letter-spacing: 0.22em;
    text-transform: uppercase;
    color: var(--ink-3);
    font-weight: 500;
}

/* ─────────────────────────── SECTION HEADER ────────────────────────── */

.section            { page-break-before: always; }
.section--first     { page-break-before: avoid; padding-top: 0; }

.section__head {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    padding-bottom: 7mm;
    border-bottom: 1px solid var(--hair);
    margin-bottom: 14mm;
}

.section__title {
    font-size: 18pt;
    font-weight: 500;
    color: var(--ink);
    letter-spacing: -0.005em;
    line-height: 1;
}

.section__sub {
    font-size: 8pt;
    letter-spacing: 0.22em;
    text-transform: uppercase;
    color: var(--ink-3);
    font-weight: 500;
}

/* ─────────────────────────── DAY BLOCKS ────────────────────────────── */

.day {
    margin-bottom: 14mm;
    page-break-inside: avoid;
}

.day:last-child { margin-bottom: 0; }

.day__head {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    padding-bottom: 4mm;
    margin-bottom: 5mm;
    border-bottom: 1px solid var(--hair);
}

.day__id {
    font-size: 11pt;
    font-weight: 600;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: var(--accent);
    line-height: 1;
}

.day__date {
    font-size: 10.5pt;
    color: var(--ink-2);
    font-weight: 400;
    line-height: 1;
}

.day__date .wd { color: var(--ink); font-weight: 500; }
.day__date .sep { color: var(--ink-mute); margin: 0 0.5em; }

/* package rows */

.pkgs { list-style: none; }

.pkg {
    display: grid;
    grid-template-columns: 28mm 24mm 1fr;
    column-gap: 8mm;
    padding: 5mm 0;
    align-items: start;
    page-break-inside: avoid;
}

.pkg + .pkg { border-top: 1px solid var(--hair-2); }

.pkg__time {
    font-family: var(--mono);
    font-size: 10pt;
    color: var(--ink);
    letter-spacing: 0.02em;
    line-height: 1.5;
    padding-top: 0.5mm;
    font-variant-numeric: tabular-nums;
    white-space: nowrap;
}

.pkg__time .dash {
    color: var(--ink-mute);
    margin: 0 0.3em;
}

.pkg__time .end {
    color: var(--ink-3);
}

.pkg__time--empty {
    color: var(--ink-mute);
    font-family: var(--sans);
}

.pkg__type {
    font-size: 8pt;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    color: var(--ink-3);
    font-weight: 500;
    padding-top: 1.5mm;
    line-height: 1.4;
}

.pkg__body { line-height: 1.4; min-width: 0; }

.pkg__name {
    font-size: 12pt;
    color: var(--ink);
    font-weight: 500;
    line-height: 1.3;
}

.pkg__meta {
    margin-top: 1.5mm;
    font-size: 10pt;
    color: var(--ink-2);
    line-height: 1.55;
}

.pkg__meta .arrow {
    color: var(--ink-3);
    margin: 0 0.3em;
    font-family: var(--sans);
}

.pkg__meta .row { display: block; }
.pkg__meta .row + .row { margin-top: 0.5mm; }

.pkg__meta .row.faint { color: var(--ink-3); }

.pkgs__empty {
    list-style: none;
    padding: 10mm 0;
    text-align: center;
    color: var(--ink-mute);
    font-size: 10pt;
    border-top: 1px dashed var(--hair-2);
    border-bottom: 1px dashed var(--hair-2);
}

.empty-state {
    text-align: center;
    color: var(--ink-3);
    padding: 32mm 0;
    font-size: 12pt;
}

/* ─────────────────────────── COACH PLAN TABLE ──────────────────────── */

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table thead th {
    font-size: 7.5pt;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    color: var(--ink-3);
    font-weight: 500;
    text-align: left;
    padding: 0 6mm 4mm 0;
    border-bottom: 1px solid var(--ink);
    vertical-align: bottom;
}

.data-table tbody td {
    padding: 5mm 6mm 5mm 0;
    border-bottom: 1px solid var(--hair-2);
    vertical-align: top;
    font-size: 10.5pt;
    line-height: 1.45;
}

.data-table tbody tr:last-child td { border-bottom: 1px solid var(--hair); }

.data-table .c-date .d {
    font-size: 13pt;
    color: var(--ink);
    font-weight: 500;
    line-height: 1;
}

.data-table .c-date .m {
    font-size: 7.5pt;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    color: var(--ink-3);
    font-weight: 500;
    margin-top: 1.5mm;
    line-height: 1;
}

.data-table .c-date .t {
    font-family: var(--mono);
    font-size: 9.5pt;
    color: var(--ink-2);
    letter-spacing: 0.02em;
    margin-top: 3mm;
    line-height: 1;
}

.data-table .svc-name {
    font-size: 11pt;
    color: var(--ink);
    font-weight: 500;
    line-height: 1.3;
}

.data-table .svc-route {
    font-size: 9.5pt;
    color: var(--ink-2);
    margin-top: 1.5mm;
}

.data-table .svc-route .arrow {
    color: var(--ink-3);
    margin: 0 0.3em;
}

.data-table .driver {
    font-size: 10.5pt;
    color: var(--ink);
    margin-bottom: 1mm;
    line-height: 1.3;
}

.data-table .phone {
    font-family: var(--mono);
    font-size: 9.5pt;
    color: var(--ink-2);
    letter-spacing: 0.01em;
}

.data-table .none { color: var(--ink-mute); }

/* ─────────────────────────── ACCOMMODATION ─────────────────────────── */

.hotel-list { list-style: none; }

.hotel {
    padding: 8mm 0;
    page-break-inside: avoid;
}

.hotel + .hotel { border-top: 1px solid var(--hair); }

.hotel__head {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    margin-bottom: 5mm;
    gap: 10mm;
}

.hotel__name {
    font-size: 14pt;
    font-weight: 500;
    color: var(--ink);
    line-height: 1.25;
}

.hotel__dates {
    text-align: right;
    flex-shrink: 0;
}

.hotel__dates .range {
    font-size: 10pt;
    color: var(--ink-2);
}

.hotel__dates .nights {
    display: block;
    font-size: 7pt;
    letter-spacing: 0.22em;
    text-transform: uppercase;
    color: var(--ink-3);
    font-weight: 500;
    margin-top: 2mm;
}

.hotel__fields {
    display: grid;
    grid-template-columns: 1fr 1fr;
    column-gap: 12mm;
    row-gap: 3mm;
    margin-top: 4mm;
}

.hotel__field {
    display: grid;
    grid-template-columns: 18mm 1fr;
    column-gap: 5mm;
    align-items: baseline;
    font-size: 10pt;
}

.hotel__field .k {
    font-size: 7pt;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    color: var(--ink-3);
    font-weight: 500;
}

.hotel__field .v {
    color: var(--ink-2);
    line-height: 1.4;
}

.hotel__field .v.mono {
    font-family: var(--mono);
    font-size: 9.5pt;
    color: var(--ink);
    letter-spacing: 0.01em;
}

/* ─────────────────────────── DOC FOOTER ────────────────────────────── */

.doc__footer {
    margin-top: 22mm;
    padding-top: 6mm;
    border-top: 1px solid var(--hair);
    display: flex;
    justify-content: space-between;
    align-items: baseline;
}

.doc__footer .a {
    font-size: 9pt;
    color: var(--ink-2);
}

.doc__footer .b {
    font-size: 7.5pt;
    letter-spacing: 0.22em;
    text-transform: uppercase;
    color: var(--ink-3);
    font-weight: 500;
}

/* ─────────────────────────── PRINT ─────────────────────────────────── */

@media print {
    body { background: white; padding: 0; }
    .doc {
        box-shadow: none;
        padding: 0;
        max-width: none;
        background: white;
    }
}

/* ─────────────────────────── NARROW SCREEN ─────────────────────────── */

@media screen and (max-width: 720px) {
    body { padding: 16px 8px; background: var(--paper); }
    .doc { padding: 14mm 8mm 18mm; box-shadow: 0 0 0 1px var(--hair); }
    .cover { min-height: auto; }
    .cover__body { padding: 18mm 0 14mm; }
    .cover__title { font-size: 22pt; }
    .meta-grid { grid-template-columns: 1fr 1fr; row-gap: 6mm; }
    .pkg { grid-template-columns: 24mm 1fr; }
    .pkg__type { grid-column: 2; margin-top: 1mm; margin-bottom: 1mm; padding-top: 0; }
    .pkg__body { grid-column: 2; }
    .hotel__fields { grid-template-columns: 1fr; }
    .data-table thead th:nth-child(4),
    .data-table tbody td:nth-child(4) { display: none; }
}
</style>
</head>
<body>
<div class="doc">

    {{-- ─────────────────────────── COVER ──────────────────────────── --}}
    <section class="cover">

        <header class="cover__head">
            <div class="brand">
                @if($logoFile)
                    <img class="brand__logo" src="{{ asset('img/' . $logoFile) }}" alt="EETS">
                @else
                    <span class="brand__wordmark">EETS</span>
                @endif
                <div class="brand__sub">Europe Express<br>Travel Service</div>
            </div>
            <div class="ref">
                <span class="ref__key">Reference</span>
                <span class="ref__val">{{ $tour->external_name ?? sprintf('EET-%05d', $tour->id ?? 0) }}</span>
            </div>
        </header>

        <div class="cover__body">
            <div class="cover__doctype">Itinerary</div>
            <h1 class="cover__title">{{ $tour->name ?? 'Untitled Tour' }}</h1>
            <div class="cover__subtitle">
                @php $subParts = []; @endphp
                @if(count($routeCities) > 0) @php $subParts[] = implode(' → ', $routeCities); @endphp @endif
                @if($depCarbon && $retCarbon) @php $subParts[] = $depCarbon->format('j M') . ' – ' . $retCarbon->format('j M Y'); @endphp
                @elseif($depCarbon) @php $subParts[] = $depCarbon->format('j M Y'); @endphp @endif
                @if($tourLength) @php $subParts[] = $tourLength . ' ' . ($tourLength === 1 ? 'day' : 'days'); @endphp @endif

                @foreach($subParts as $i => $part)
                    @if($i > 0)<span class="sep">·</span>@endif{{ $part }}
                @endforeach
            </div>
        </div>

        <div class="cover__meta">
            <div class="meta-grid">
                <div class="meta-cell">
                    <span class="k">Pax</span>
                    <span class="v">{{ $tour->pax ?? '—' }}@if(!empty($tour->pax_free))<span class="plus">+{{ $tour->pax_free }}</span>@endif</span>
                </div>
                <div class="meta-cell">
                    <span class="k">Rooms</span>
                    <span class="v">@if(!empty($tour->rooms)){{ $tour->rooms }}@else<span class="none">—</span>@endif</span>
                </div>
                <div class="meta-cell">
                    <span class="k">Tour Leader</span>
                    <span class="v">@if(!empty($tour->itinerary_tl)){{ $tour->itinerary_tl }}@else<span class="none">—</span>@endif</span>
                </div>
                <div class="meta-cell">
                    <span class="k">Mobile</span>
                    <span class="v mono">@if(!empty($tour->phone)){{ $tour->phone }}@else<span class="none">—</span>@endif</span>
                </div>
                @if(!empty($personInCharge))
                <div class="meta-cell" style="grid-column: span 2;">
                    <span class="k">Person in charge</span>
                    <span class="v">{{ $personInCharge }}</span>
                </div>
                @endif
            </div>
        </div>

        <div class="cover__foot">
            <div class="a">EETS · Europe Express Travel Service Int'l Co., Ltd.</div>
            <div class="b">Issued {{ $issuedAt->format('j M Y') }}</div>
        </div>
    </section>

    {{-- ─────────────────────────── PROGRAMME ──────────────────────── --}}
    <section class="section section--first">
        <header class="section__head">
            <h2 class="section__title">Programme</h2>
            <div class="section__sub">
                @if($tourLength){{ $tourLength }} {{ $tourLength === 1 ? 'day' : 'days' }}@else day by day @endif
            </div>
        </header>

        @forelse($tourDays ?? [] as $i => $day)
            @php
                $dayCarbon = $tryParse($day->date ?? null);
                $pkgs = collect($day->packages ?? []);
            @endphp
            <article class="day">
                <header class="day__head">
                    <div class="day__id">Day {{ $i + 1 }}</div>
                    <div class="day__date">
                        @if($dayCarbon)
                            <span class="wd">{{ $dayCarbon->format('l') }}</span><span class="sep">·</span>{{ $dayCarbon->format('j F Y') }}
                        @else
                            <span style="color: var(--ink-mute)">Date not set</span>
                        @endif
                    </div>
                </header>

                <ol class="pkgs">
                    @forelse($pkgs as $pkg)
                        @php
                            $type      = (int) ($pkg->type ?? -1);
                            $typeLabel = $serviceTypeMap[$type] ?? 'Item';
                            $startT    = $fmtTime($pkg->time_from ?? null);
                            $endT      = $fmtTime($pkg->time_to ?? null);
                            $svc       = optional($pkg->service());
                            $hasRoute  = ($type === 3 || $type === 2)
                                         && (!empty($pkg->pickup_des) || !empty($pkg->drop_des));
                            $descSame  = !empty($pkg->description)
                                         && trim($pkg->description) === trim($pkg->name ?? '');
                        @endphp
                        <li class="pkg">
                            <div class="pkg__time">
                                @if($startT){{ $startT }}@if($endT && $endT !== $startT)<span class="dash">–</span><span class="end">{{ $endT }}</span>@endif
                                @else<span class="pkg__time--empty">—</span>@endif
                            </div>
                            <div class="pkg__type">{{ $typeLabel }}</div>
                            <div class="pkg__body">
                                <div class="pkg__name">{{ $pkg->name ?? 'Untitled service' }}</div>
                                @if($hasRoute || !empty($svc->address_first) || (!empty($pkg->description) && !$descSame))
                                <div class="pkg__meta">
                                    @if($hasRoute)
                                        <span class="row">
                                            @if(!empty($pkg->pickup_des)){{ $pkg->pickup_des }}@endif
                                            @if(!empty($pkg->pickup_des) && !empty($pkg->drop_des))<span class="arrow">→</span>@endif
                                            @if(!empty($pkg->drop_des)){{ $pkg->drop_des }}@endif
                                        </span>
                                    @endif
                                    @if(!empty($svc->address_first))
                                        <span class="row faint">{{ $svc->address_first }}</span>
                                    @endif
                                    @if(!empty($pkg->description) && !$descSame)
                                        <span class="row">{{ $pkg->description }}</span>
                                    @endif
                                </div>
                                @endif
                            </div>
                        </li>
                    @empty
                        <li class="pkgs__empty">No services scheduled for this day</li>
                    @endforelse
                </ol>
            </article>
        @empty
            <div class="empty-state">This itinerary has no scheduled days yet.</div>
        @endforelse
    </section>

    {{-- ─────────────────────────── COACH PLAN ─────────────────────── --}}
    @if($transferList->count() > 0)
    <section class="section">
        <header class="section__head">
            <h2 class="section__title">Coach plan</h2>
            <div class="section__sub">{{ $transferList->count() }} {{ $transferList->count() === 1 ? 'movement' : 'movements' }}</div>
        </header>

        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 24mm">Date</th>
                    <th>Service</th>
                    <th style="width: 40mm">Driver</th>
                    <th style="width: 36mm">Mobile</th>
                </tr>
            </thead>
            <tbody>
            @foreach($transferList as $t)
                @php
                    $tStart  = $tryParse($t->time_from ?? null);
                    $drivers = collect();
                    try {
                        if (method_exists($t, 'getTransferDrivers')) {
                            $drivers = collect($t->getTransferDrivers());
                        }
                    } catch (\Throwable $e) {}
                @endphp
                <tr>
                    <td class="c-date">
                        @if($tStart)
                            <div class="d">{{ $tStart->format('j') }}</div>
                            <div class="m">{{ $tStart->format('M Y') }}</div>
                            <div class="t">{{ $tStart->format('H:i') }}</div>
                        @else
                            <span class="none">—</span>
                        @endif
                    </td>
                    <td>
                        <div class="svc-name">{{ $t->name ?? 'Transfer' }}</div>
                        @if(!empty($t->pickup_des) || !empty($t->drop_des))
                            <div class="svc-route">
                                {{ $t->pickup_des ?? '—' }}<span class="arrow">→</span>{{ $t->drop_des ?? '—' }}
                            </div>
                        @endif
                    </td>
                    <td>
                        @forelse($drivers as $d)
                            <div class="driver">{{ $d->name ?? '—' }}</div>
                        @empty
                            <span class="none">—</span>
                        @endforelse
                    </td>
                    <td>
                        @forelse($drivers as $d)
                            <div class="phone">{{ $d->phone ?? '—' }}</div>
                        @empty
                            <span class="none">—</span>
                        @endforelse
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </section>
    @endif

    {{-- ─────────────────────────── ACCOMMODATION ──────────────────── --}}
    @if(!empty($hotelIndex))
    <section class="section">
        <header class="section__head">
            <h2 class="section__title">Accommodation</h2>
            <div class="section__sub">
                {{ count($hotelIndex) }} {{ count($hotelIndex) === 1 ? 'property' : 'properties' }} ·
                {{ $totalNights }} {{ $totalNights === 1 ? 'night' : 'nights' }}
            </div>
        </header>

        <ul class="hotel-list">
            @foreach($hotelIndex as $h)
                @php
                    $dateCarbons = collect($h['dates'])->map(fn ($d) => $tryParse($d))->filter();
                    $hotelStart  = $dateCarbons->min();
                    $hotelEnd    = $dateCarbons->max();
                    $nights      = count($h['dates']);
                    $svc         = optional($h['package']->service());
                @endphp
                <li class="hotel">
                    <div class="hotel__head">
                        <div class="hotel__name">{{ $h['name'] }}</div>
                        <div class="hotel__dates">
                            @if($hotelStart && $hotelEnd)
                                <span class="range">
                                    @if($hotelEnd->ne($hotelStart))
                                        {{ $hotelStart->format('j M') }} – {{ $hotelEnd->format('j M Y') }}
                                    @else
                                        {{ $hotelStart->format('j M Y') }}
                                    @endif
                                </span>
                            @endif
                            <span class="nights">{{ $nights }} {{ $nights === 1 ? 'night' : 'nights' }}</span>
                        </div>
                    </div>

                    @if(!empty($svc->address_first) || !empty($svc->work_phone) || !empty($svc->work_email) || !empty($h['package']->reference))
                    <div class="hotel__fields">
                        @if(!empty($svc->address_first))
                        <div class="hotel__field">
                            <span class="k">Address</span>
                            <span class="v">{{ $svc->address_first }}</span>
                        </div>
                        @endif
                        @if(!empty($svc->work_phone))
                        <div class="hotel__field">
                            <span class="k">Phone</span>
                            <span class="v mono">{{ $svc->work_phone }}</span>
                        </div>
                        @endif
                        @if(!empty($svc->work_email))
                        <div class="hotel__field">
                            <span class="k">Email</span>
                            <span class="v mono">{{ $svc->work_email }}</span>
                        </div>
                        @endif
                        @if(!empty($h['package']->reference))
                        <div class="hotel__field">
                            <span class="k">Ref.</span>
                            <span class="v mono">{{ $h['package']->reference }}</span>
                        </div>
                        @endif
                    </div>
                    @endif
                </li>
            @endforeach
        </ul>
    </section>
    @endif

    {{-- ─────────────────────────── DOC FOOTER ─────────────────────── --}}
    <footer class="doc__footer">
        <div class="a">EETS · Europe Express Travel Service Int'l Co., Ltd.</div>
        <div class="b">Generated {{ $issuedAt->format('j M Y · H:i') }}</div>
    </footer>

</div>
</body>
</html>
