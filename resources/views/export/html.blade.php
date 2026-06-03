@if(isset($isDoc) && $isDoc)
<?php
header("Content-Type: application/vnd.ms-word");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("content-disposition: attachment; filename=" . ($download_name ?? 'itinerary.doc'));
?>
@endif
@php
    // ─── helpers ────────────────────────────────────────────────────────────
    $serviceTypeMap = [
        0 => ['letter' => 'H', 'label' => 'Hotel',      'color' => '#8b6f47'],
        1 => ['letter' => 'E', 'label' => 'Event',      'color' => '#a85432'],
        2 => ['letter' => 'G', 'label' => 'Guide',      'color' => '#3d5942'],
        3 => ['letter' => 'T', 'label' => 'Transfer',   'color' => '#3a3f4a'],
        4 => ['letter' => 'R', 'label' => 'Restaurant', 'color' => '#a05c2c'],
        5 => ['letter' => 'P', 'label' => 'Package',    'color' => '#3d4d62'],
        6 => ['letter' => 'C', 'label' => 'Cruise',     'color' => '#2c5560'],
        7 => ['letter' => 'F', 'label' => 'Flight',     'color' => '#1f3a5f'],
    ];

    $roman = function ($n) {
        if ($n < 1) return '';
        if ($n > 40) return (string) $n;
        $map = [
            '', 'I','II','III','IV','V','VI','VII','VIII','IX','X',
            'XI','XII','XIII','XIV','XV','XVI','XVII','XVIII','XIX','XX',
            'XXI','XXII','XXIII','XXIV','XXV','XXVI','XXVII','XXVIII','XXIX','XXX',
            'XXXI','XXXII','XXXIII','XXXIV','XXXV','XXXVI','XXXVII','XXXVIII','XXXIX','XL',
        ];
        return $map[(int) $n] ?? (string) $n;
    };

    $tryParse = function ($v) {
        if (empty($v)) return null;
        try { return \Carbon\Carbon::parse($v); }
        catch (\Throwable $e) { return null; }
    };

    $fmtTime = function ($v) use ($tryParse) {
        $c = $tryParse($v);
        return $c ? $c->format('H:i') : null;
    };

    // ─── tour metadata ──────────────────────────────────────────────────────
    $depCarbon = $tryParse($tour->departure_date ?? null);
    $retCarbon = $tryParse($tour->retirement_date ?? null);
    $tourLength = ($depCarbon && $retCarbon) ? ($depCarbon->diffInDays($retCarbon) + 1) : null;

    // ─── derived collections ────────────────────────────────────────────────
    $transferList = collect($tourTransfers ?? [])->where('type', 3)->values();

    // hotels aggregated across days
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

    // route summary from begin/end cities (falls back to first/last day cities)
    $route = array_values(array_filter([
        $tour->city_begin ?? null,
        $tour->city_end ?? null,
    ]));

    // doc generation timestamp
    $issuedAt = \Carbon\Carbon::now();

    // logo lookup (case-insensitive helper)
    $logoFile = null;
    foreach (['eets_logo.png', 'Eets_logo.png', 'eets_logo.jpg', 'EETS_logo.png'] as $candidate) {
        if (is_file(public_path('img/' . $candidate))) { $logoFile = $candidate; break; }
    }
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Itinerary · {{ $tour->name ?? 'Tour' }} · EETS</title>
<style>
:root {
    --crimson:      #7a1a1a;
    --crimson-deep: #5a1010;
    --gold:         #b8924f;
    --gold-pale:    #e3d2a8;
    --ink:          #1a1614;
    --ink-soft:     #4a443e;
    --ink-fade:     #8a8580;
    --ink-mute:     #bab1a3;
    --paper:        #fbf8f1;
    --paper-deep:   #f2ebdb;
    --rule:         #ddd1b8;
    --rule-soft:    #ece3cd;

    --serif: 'Iowan Old Style', 'Palatino Linotype', Palatino, 'Book Antiqua', Georgia, serif;
    --sans:  'Avenir Next', Avenir, 'Source Sans 3', 'Source Sans Pro', system-ui, -apple-system, 'Segoe UI', sans-serif;
    --mono:  'SF Mono', 'JetBrains Mono', Menlo, Consolas, 'Courier New', monospace;
}

@page { size: A4; margin: 16mm 14mm 18mm; }
@page :first { margin-top: 22mm; }

* { box-sizing: border-box; margin: 0; padding: 0; }
html { -webkit-text-size-adjust: 100%; }
body {
    background: var(--paper);
    color: var(--ink);
    font-family: var(--sans);
    font-size: 11pt;
    line-height: 1.55;
    font-feature-settings: 'kern' 1, 'liga' 1, 'onum' 1;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    text-rendering: geometricPrecision;
}

img { display: block; max-width: 100%; }

.doc {
    max-width: 200mm;
    margin: 0 auto;
    background: var(--paper);
    padding: 18mm 18mm 22mm;
    position: relative;
}

/* subtle warm grain — invisible on print, gives screen view texture */
.doc::before {
    content: '';
    position: absolute;
    inset: 0;
    pointer-events: none;
    opacity: 0.35;
    background:
        radial-gradient(ellipse at 8% 4%, rgba(184, 146, 79, 0.06), transparent 38%),
        radial-gradient(ellipse at 96% 96%, rgba(122, 26, 26, 0.04), transparent 50%);
    z-index: 0;
}
.doc > * { position: relative; z-index: 1; }

@media screen {
    body { padding: 36px 16px; background: #ece6d6; }
    .doc {
        background: var(--paper);
        box-shadow: 0 1px 0 rgba(0, 0, 0, 0.04),
                    0 24px 80px -32px rgba(60, 30, 18, 0.32);
        border: 1px solid var(--rule);
    }
}

/* ═══════════════════════════════════════════════════════════════════════
   COVER
   ═══════════════════════════════════════════════════════════════════════ */

.cover {
    min-height: 260mm;
    display: flex;
    flex-direction: column;
    page-break-after: always;
}

.cover__top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-bottom: 10mm;
    border-bottom: 0.5pt solid var(--rule);
    margin-bottom: 0;
}

.brand {
    display: flex;
    align-items: center;
    gap: 12px;
}

.brand__logo {
    height: 38px;
    width: auto;
}

.brand__wordmark {
    font-family: var(--serif);
    font-weight: 700;
    font-size: 22pt;
    color: var(--crimson);
    letter-spacing: 0.06em;
    line-height: 1;
}

.brand__name {
    font-family: var(--sans);
    font-size: 8pt;
    letter-spacing: 0.28em;
    text-transform: uppercase;
    color: var(--ink-fade);
    line-height: 1.4;
}

.cover__doc-no { text-align: right; }
.cover__doc-no .key {
    display: block;
    font-family: var(--sans);
    font-size: 7.5pt;
    letter-spacing: 0.32em;
    text-transform: uppercase;
    color: var(--ink-mute);
    margin-bottom: 2px;
}
.cover__doc-no .val {
    font-family: var(--mono);
    font-size: 10pt;
    color: var(--ink);
    letter-spacing: 0.04em;
}

.cover__hero {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 28mm 0 24mm;
}

.cover__eyebrow {
    font-family: var(--sans);
    font-size: 8.5pt;
    letter-spacing: 0.36em;
    text-transform: uppercase;
    color: var(--crimson);
    margin-bottom: 16mm;
    display: flex;
    align-items: center;
    gap: 4mm;
}

.cover__eyebrow::before,
.cover__eyebrow::after {
    content: '';
    height: 0;
    width: 8mm;
    border-top: 0.5pt solid var(--gold);
}

.cover__title {
    font-family: var(--serif);
    font-style: italic;
    font-weight: 400;
    font-size: 92pt;
    line-height: 0.92;
    color: var(--ink);
    letter-spacing: -0.015em;
    margin-bottom: 7mm;
}

.cover__rule {
    width: 56mm;
    height: 0;
    border-top: 1.5pt solid var(--crimson);
    margin-bottom: 10mm;
}

.cover__tour {
    font-family: var(--serif);
    font-weight: 400;
    font-size: 26pt;
    line-height: 1.18;
    color: var(--ink);
    letter-spacing: 0.005em;
    margin-bottom: 8mm;
    max-width: 150mm;
}

.cover__route {
    font-family: var(--sans);
    font-size: 11pt;
    letter-spacing: 0.36em;
    text-transform: uppercase;
    color: var(--ink-soft);
    margin-bottom: 14mm;
    line-height: 1.4;
}
.cover__route .dot { color: var(--gold); margin: 0 0.7em; }

.cover__dates {
    font-family: var(--serif);
    font-style: italic;
    font-size: 17pt;
    color: var(--ink);
    display: flex;
    align-items: baseline;
    gap: 10mm;
}
.cover__dates .dash { color: var(--gold); margin: 0 0.3em; }
.cover__dates .length {
    font-family: var(--sans);
    font-style: normal;
    font-size: 8.5pt;
    letter-spacing: 0.28em;
    text-transform: uppercase;
    color: var(--ink-mute);
    padding-left: 6mm;
    border-left: 0.5pt solid var(--rule);
}

/* meta panel */

.cover__meta {
    border-top: 0.5pt solid var(--rule);
    padding-top: 8mm;
    margin-top: auto;
}

.cover__meta-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    column-gap: 8mm;
}

.meta-cell { padding-left: 4mm; border-left: 0.5pt solid var(--rule); }
.meta-cell:first-child { border-left-color: var(--crimson); border-left-width: 1pt; }

.meta-cell .key {
    display: block;
    font-family: var(--sans);
    font-size: 7.5pt;
    letter-spacing: 0.28em;
    text-transform: uppercase;
    color: var(--ink-mute);
    margin-bottom: 2.5mm;
    line-height: 1;
}

.meta-cell .val {
    font-family: var(--serif);
    font-size: 13pt;
    color: var(--ink);
    line-height: 1.3;
}

.meta-cell .val.mono {
    font-family: var(--mono);
    font-size: 10.5pt;
    letter-spacing: 0.01em;
}

.meta-cell .val .plus {
    color: var(--ink-mute);
    font-size: 11pt;
    margin-left: 1mm;
}

.meta-cell .val .none {
    color: var(--ink-mute);
    font-style: italic;
}

.cover__foot {
    margin-top: 8mm;
    padding-top: 4mm;
    border-top: 0.5pt solid var(--rule-soft);
    display: flex;
    justify-content: space-between;
    align-items: baseline;
}

.cover__foot .sig {
    font-family: var(--serif);
    font-style: italic;
    font-size: 9pt;
    color: var(--ink-fade);
}

.cover__foot .stamp {
    font-family: var(--sans);
    font-size: 7.5pt;
    letter-spacing: 0.32em;
    text-transform: uppercase;
    color: var(--ink-mute);
}

/* ═══════════════════════════════════════════════════════════════════════
   SECTION HEADER
   ═══════════════════════════════════════════════════════════════════════ */

.section { page-break-before: always; }
.section--first { page-break-before: avoid; padding-top: 4mm; }

.section__head {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    border-bottom: 0.5pt solid var(--rule);
    padding-bottom: 6mm;
    margin-bottom: 12mm;
}

.section__title {
    font-family: var(--serif);
    font-style: italic;
    font-weight: 400;
    font-size: 36pt;
    line-height: 1;
    color: var(--ink);
    letter-spacing: -0.005em;
}

.section__sub {
    font-family: var(--sans);
    font-size: 8.5pt;
    letter-spacing: 0.32em;
    text-transform: uppercase;
    color: var(--ink-fade);
}

/* ═══════════════════════════════════════════════════════════════════════
   DAY BLOCKS
   ═══════════════════════════════════════════════════════════════════════ */

.day {
    margin-bottom: 14mm;
    page-break-inside: avoid;
}

.day__head {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    border-bottom: 1.5pt solid var(--ink);
    padding-bottom: 3mm;
    margin-bottom: 6mm;
}

.day__id { display: flex; flex-direction: column; align-items: flex-start; }

.day__label {
    font-family: var(--sans);
    font-size: 7.5pt;
    letter-spacing: 0.36em;
    text-transform: uppercase;
    color: var(--ink-mute);
    margin-bottom: -3mm;
    line-height: 1;
}

.day__numeral {
    font-family: var(--serif);
    font-style: italic;
    font-weight: 400;
    font-size: 60pt;
    line-height: 0.85;
    color: var(--crimson);
    letter-spacing: -0.02em;
    padding-left: 1mm;
}

.day__date-block { text-align: right; padding-bottom: 4mm; }

.day__weekday {
    font-family: var(--serif);
    font-style: italic;
    font-size: 11pt;
    color: var(--ink-soft);
    line-height: 1.2;
}

.day__date {
    font-family: var(--serif);
    font-size: 13.5pt;
    color: var(--ink);
    line-height: 1.2;
    margin-top: 1mm;
}

.day__date .none { color: var(--ink-mute); font-style: italic; font-size: 11pt; }

/* package rows */

.pkgs {
    list-style: none;
    display: flex;
    flex-direction: column;
}

.pkg {
    display: grid;
    grid-template-columns: 24mm 36mm 1fr;
    column-gap: 6mm;
    padding: 5mm 0;
    border-bottom: 0.5pt solid var(--rule-soft);
    page-break-inside: avoid;
    align-items: start;
}
.pkg:last-child { border-bottom: none; }

/* time column */

.pkg__time {
    font-family: var(--mono);
    font-size: 10pt;
    letter-spacing: 0.02em;
    line-height: 1.5;
    color: var(--ink);
}

.pkg__time-start {
    display: block;
    color: var(--ink);
    font-weight: 600;
    font-size: 11pt;
    letter-spacing: 0.04em;
}

.pkg__time-end {
    display: block;
    color: var(--ink-fade);
    margin-top: 0.5mm;
    position: relative;
    padding-left: 7mm;
}
.pkg__time-end::before {
    content: '';
    position: absolute;
    left: 0;
    top: 50%;
    width: 5mm;
    height: 0;
    border-top: 0.5pt solid var(--ink-mute);
}

.pkg__time--empty {
    font-family: var(--serif);
    font-style: italic;
    color: var(--ink-mute);
    font-size: 10pt;
}

/* chip column */

.pkg__chip {
    display: flex;
    align-items: center;
    gap: 3mm;
}

.pkg__chip-mark {
    width: 8mm;
    height: 8mm;
    border: 0.75pt solid var(--chip-color, var(--ink));
    color: var(--chip-color, var(--ink));
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-family: var(--mono);
    font-weight: 600;
    font-size: 10pt;
    flex-shrink: 0;
    line-height: 1;
}

.pkg__chip-label {
    font-family: var(--sans);
    font-size: 8.5pt;
    letter-spacing: 0.24em;
    text-transform: uppercase;
    color: var(--chip-color, var(--ink));
    font-weight: 500;
}

/* body column */

.pkg__body { line-height: 1.4; min-width: 0; }

.pkg__name {
    font-family: var(--serif);
    font-size: 13pt;
    color: var(--ink);
    line-height: 1.25;
    margin-bottom: 1mm;
}

.pkg__route,
.pkg__addr,
.pkg__desc {
    font-family: var(--sans);
    font-size: 9.5pt;
    color: var(--ink-soft);
    margin-top: 1mm;
}

.pkg__route {
    font-style: italic;
    color: var(--ink-soft);
}
.pkg__route .arrow {
    color: var(--gold);
    margin: 0 0.3em;
    font-style: normal;
}

.pkg__addr { color: var(--ink-fade); }
.pkg__desc { color: var(--ink-fade); font-size: 9pt; }

/* per-type chip color */
.pkg--type-0 { --chip-color: #8b6f47; }
.pkg--type-1 { --chip-color: #a85432; }
.pkg--type-2 { --chip-color: #3d5942; }
.pkg--type-3 { --chip-color: #3a3f4a; }
.pkg--type-4 { --chip-color: #a05c2c; }
.pkg--type-5 { --chip-color: #3d4d62; }
.pkg--type-6 { --chip-color: #2c5560; }
.pkg--type-7 { --chip-color: #1f3a5f; }

.pkgs__empty {
    list-style: none;
    padding: 10mm 0;
    text-align: center;
    color: var(--ink-mute);
    font-style: italic;
    font-family: var(--serif);
    font-size: 11pt;
    border-top: 0.25pt dashed var(--rule);
    border-bottom: 0.25pt dashed var(--rule);
}

.empty-state {
    padding: 24mm 0;
    text-align: center;
    color: var(--ink-fade);
    font-family: var(--serif);
    font-style: italic;
    font-size: 14pt;
}

/* ═══════════════════════════════════════════════════════════════════════
   COACH PLAN TABLE
   ═══════════════════════════════════════════════════════════════════════ */

.coach-table {
    width: 100%;
    border-collapse: collapse;
}

.coach-table thead th {
    text-align: left;
    font-family: var(--sans);
    font-size: 7.5pt;
    letter-spacing: 0.32em;
    text-transform: uppercase;
    color: var(--ink-fade);
    font-weight: 600;
    padding: 2mm 4mm 4mm 0;
    border-bottom: 0.75pt solid var(--ink);
    vertical-align: bottom;
}

.coach-table tbody td {
    padding: 5mm 4mm 5mm 0;
    border-bottom: 0.5pt solid var(--rule-soft);
    vertical-align: top;
    font-size: 10.5pt;
}

.coach-table tbody tr:last-child td {
    border-bottom: 0.5pt solid var(--rule);
}

.coach-table .cell-date .day {
    font-family: var(--serif);
    font-size: 13pt;
    color: var(--crimson);
    line-height: 1;
    margin-bottom: 0;
}

.coach-table .cell-date .month {
    font-family: var(--sans);
    font-size: 8pt;
    letter-spacing: 0.28em;
    text-transform: uppercase;
    color: var(--ink-fade);
    margin-top: 1mm;
}

.coach-table .cell-date .hh {
    font-family: var(--mono);
    font-size: 10pt;
    color: var(--ink-soft);
    margin-top: 2mm;
    letter-spacing: 0.04em;
}

.coach-table .svc-name {
    font-family: var(--serif);
    font-size: 11.5pt;
    color: var(--ink);
    line-height: 1.25;
}

.coach-table .svc-route {
    font-family: var(--sans);
    font-style: italic;
    font-size: 9pt;
    color: var(--ink-fade);
    margin-top: 1.5mm;
}
.coach-table .svc-route .arrow { color: var(--gold); margin: 0 0.4em; font-style: normal; }

.coach-table .driver {
    font-family: var(--serif);
    color: var(--ink);
    margin-bottom: 1mm;
}

.coach-table .phone {
    font-family: var(--mono);
    font-size: 9.5pt;
    color: var(--ink-soft);
    letter-spacing: 0.02em;
}

.coach-table .none {
    color: var(--ink-mute);
    font-family: var(--serif);
    font-style: italic;
}

/* ═══════════════════════════════════════════════════════════════════════
   ACCOMMODATION LIST
   ═══════════════════════════════════════════════════════════════════════ */

.hotel-list { list-style: none; }

.hotel {
    padding: 7mm 0;
    border-top: 0.5pt solid var(--rule);
    page-break-inside: avoid;
}

.hotel:first-child { border-top: 1.5pt solid var(--ink); }
.hotel:last-child { border-bottom: 0.5pt solid var(--rule); }

.hotel__head {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    gap: 8mm;
    margin-bottom: 4mm;
}

.hotel__name {
    font-family: var(--serif);
    font-size: 16pt;
    color: var(--ink);
    line-height: 1.2;
}

.hotel__dates {
    text-align: right;
    flex-shrink: 0;
}
.hotel__dates .range {
    font-family: var(--serif);
    font-style: italic;
    font-size: 11pt;
    color: var(--ink-soft);
}
.hotel__dates .nights {
    display: block;
    font-family: var(--sans);
    font-size: 7.5pt;
    letter-spacing: 0.32em;
    text-transform: uppercase;
    color: var(--crimson);
    margin-top: 2mm;
}

.hotel__meta-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    column-gap: 8mm;
    row-gap: 2mm;
    margin-top: 4mm;
}

.hotel__field {
    display: grid;
    grid-template-columns: 20mm 1fr;
    column-gap: 4mm;
    align-items: baseline;
}

.hotel__field .key {
    font-family: var(--sans);
    font-size: 7.5pt;
    letter-spacing: 0.28em;
    text-transform: uppercase;
    color: var(--ink-mute);
}

.hotel__field .val {
    font-family: var(--sans);
    font-size: 10pt;
    color: var(--ink-soft);
    line-height: 1.4;
}

.hotel__field .val.mono {
    font-family: var(--mono);
    font-size: 9.5pt;
    letter-spacing: 0.02em;
}

/* ═══════════════════════════════════════════════════════════════════════
   DOCUMENT FOOTER
   ═══════════════════════════════════════════════════════════════════════ */

.doc__footer {
    margin-top: 18mm;
    padding-top: 5mm;
    border-top: 0.5pt solid var(--rule);
    display: flex;
    justify-content: space-between;
    align-items: baseline;
}

.doc__footer .brand {
    font-family: var(--serif);
    font-style: italic;
    font-size: 9pt;
    color: var(--ink-fade);
}

.doc__footer .stamp {
    font-family: var(--sans);
    font-size: 7.5pt;
    letter-spacing: 0.28em;
    text-transform: uppercase;
    color: var(--ink-mute);
}

/* ═══════════════════════════════════════════════════════════════════════
   PRINT TWEAKS
   ═══════════════════════════════════════════════════════════════════════ */

@media print {
    body { background: white; padding: 0; }
    .doc {
        box-shadow: none;
        border: none;
        padding: 0;
        max-width: none;
        background: white;
    }
    .doc::before { display: none; }
}

/* ═══════════════════════════════════════════════════════════════════════
   NARROW SCREEN FALLBACK (responsive when viewed on phones)
   ═══════════════════════════════════════════════════════════════════════ */

@media screen and (max-width: 720px) {
    body { padding: 12px 6px; }
    .doc { padding: 14mm 8mm 18mm; }
    .cover__title { font-size: 64pt; }
    .cover__tour { font-size: 20pt; }
    .cover__meta-grid { grid-template-columns: 1fr 1fr; row-gap: 6mm; }
    .section__title { font-size: 28pt; }
    .pkg { grid-template-columns: 22mm 1fr; }
    .pkg__chip { grid-column: 2; margin-top: -3mm; margin-bottom: 1mm; }
    .pkg__body { grid-column: 2; }
    .hotel__meta-grid { grid-template-columns: 1fr; }
    .coach-table thead th:nth-child(4),
    .coach-table tbody td:nth-child(4) { display: none; }
}
</style>
</head>
<body>

<div class="doc">

    {{-- ═════════════════════════════════ COVER ═════════════════════════════════ --}}
    <section class="cover">

        <div class="cover__top">
            <div class="brand">
                @if($logoFile)
                    <img class="brand__logo" src="{{ asset('img/' . $logoFile) }}" alt="EETS">
                @else
                    <span class="brand__wordmark">EETS</span>
                @endif
                <div class="brand__name">Europe Express<br>Travel Service</div>
            </div>
            <div class="cover__doc-no">
                <span class="key">Reference No.</span>
                <span class="val">{{ $tour->external_name ?? sprintf('EET-%05d', $tour->id ?? 0) }}</span>
            </div>
        </div>

        <div class="cover__hero">
            <div class="cover__eyebrow">Confidential · Tour Briefing</div>

            <h1 class="cover__title">Itinerary</h1>
            <div class="cover__rule"></div>

            <div class="cover__tour">{{ $tour->name ?? 'Untitled Tour' }}</div>

            @if(count($route) > 0)
                <div class="cover__route">
                    @foreach($route as $i => $city)
                        @if($i > 0)<span class="dot">·</span>@endif
                        <span>{{ $city }}</span>
                    @endforeach
                </div>
            @endif

            @if($depCarbon || $retCarbon)
                <div class="cover__dates">
                    @if($depCarbon){{ $depCarbon->format('d M Y') }}@endif
                    @if($depCarbon && $retCarbon)<span class="dash">—</span>@endif
                    @if($retCarbon){{ $retCarbon->format('d M Y') }}@endif
                    @if($tourLength)
                        <span class="length">{{ $tourLength }} {{ $tourLength === 1 ? 'Day' : 'Days' }}</span>
                    @endif
                </div>
            @endif
        </div>

        <div class="cover__meta">
            <div class="cover__meta-grid">
                <div class="meta-cell">
                    <span class="key">Pax</span>
                    <span class="val">
                        {{ $tour->pax ?? '—' }}@if(!empty($tour->pax_free))<span class="plus">+ {{ $tour->pax_free }}</span>@endif
                    </span>
                </div>
                <div class="meta-cell">
                    <span class="key">Rooms</span>
                    <span class="val">
                        @if(!empty($tour->rooms)){{ $tour->rooms }}@else<span class="none">—</span>@endif
                    </span>
                </div>
                <div class="meta-cell">
                    <span class="key">Tour Leader</span>
                    <span class="val">
                        @if(!empty($tour->itinerary_tl)){{ $tour->itinerary_tl }}@else<span class="none">Not assigned</span>@endif
                    </span>
                </div>
                <div class="meta-cell">
                    <span class="key">Mobile</span>
                    <span class="val mono">
                        @if(!empty($tour->phone)){{ $tour->phone }}@else<span class="none">—</span>@endif
                    </span>
                </div>
            </div>
        </div>

        <div class="cover__foot">
            <div class="sig">EETS · Europe Express Travel Service Int'l Co., Ltd.</div>
            <div class="stamp">Issued · {{ $issuedAt->format('d M Y') }}</div>
        </div>
    </section>

    {{-- ═══════════════════════════════ PROGRAMME ═══════════════════════════════ --}}

    <section class="section section--first">
        <header class="section__head">
            <h2 class="section__title">Programme</h2>
            <div class="section__sub">
                @if($tourLength){{ $tourLength }} {{ $tourLength === 1 ? 'Day' : 'Days' }}@else Day by Day @endif
            </div>
        </header>

        @forelse($tourDays ?? [] as $i => $day)
            @php
                $dayCarbon = $tryParse($day->date ?? null);
                $pkgs = collect($day->packages ?? []);
            @endphp
            <article class="day">
                <header class="day__head">
                    <div class="day__id">
                        <div class="day__label">Day {{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}</div>
                        <div class="day__numeral">{{ $roman($i + 1) }}</div>
                    </div>
                    <div class="day__date-block">
                        @if($dayCarbon)
                            <div class="day__weekday">{{ $dayCarbon->format('l') }}</div>
                            <div class="day__date">{{ $dayCarbon->format('d F Y') }}</div>
                        @else
                            <div class="day__date"><span class="none">Date not set</span></div>
                        @endif
                    </div>
                </header>

                <ol class="pkgs">
                    @forelse($pkgs as $pkg)
                        @php
                            $type = (int) ($pkg->type ?? -1);
                            $info = $serviceTypeMap[$type] ?? ['letter' => '·', 'label' => 'Item', 'color' => 'var(--ink)'];
                            $startT = $fmtTime($pkg->time_from ?? null);
                            $endT   = $fmtTime($pkg->time_to ?? null);
                            $svc    = optional($pkg->service());
                            $hasRoute = ($type === 3 || $type === 2)
                                && (!empty($pkg->pickup_des) || !empty($pkg->drop_des));
                            $descSame = !empty($pkg->description)
                                && trim($pkg->description) === trim($pkg->name ?? '');
                        @endphp
                        <li class="pkg pkg--type-{{ $type }}">
                            <div class="pkg__time">
                                @if($startT)
                                    <span class="pkg__time-start">{{ $startT }}</span>
                                    @if($endT && $endT !== $startT)
                                        <span class="pkg__time-end">{{ $endT }}</span>
                                    @endif
                                @else
                                    <span class="pkg__time--empty">— —</span>
                                @endif
                            </div>

                            <div class="pkg__chip">
                                <span class="pkg__chip-mark">{{ $info['letter'] }}</span>
                                <span class="pkg__chip-label">{{ $info['label'] }}</span>
                            </div>

                            <div class="pkg__body">
                                <div class="pkg__name">{{ $pkg->name ?? 'Untitled service' }}</div>

                                @if($hasRoute)
                                    <div class="pkg__route">
                                        @if(!empty($pkg->pickup_des)){{ $pkg->pickup_des }}@endif
                                        @if(!empty($pkg->pickup_des) && !empty($pkg->drop_des))<span class="arrow">→</span>@endif
                                        @if(!empty($pkg->drop_des)){{ $pkg->drop_des }}@endif
                                    </div>
                                @endif

                                @if(!empty($svc->address_first))
                                    <div class="pkg__addr">{{ $svc->address_first }}</div>
                                @endif

                                @if(!empty($pkg->description) && !$descSame)
                                    <div class="pkg__desc">{{ $pkg->description }}</div>
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

    {{-- ═══════════════════════════════ COACH PLAN ══════════════════════════════ --}}

    @if($transferList->count() > 0)
        <section class="section">
            <header class="section__head">
                <h2 class="section__title">Coach Plan</h2>
                <div class="section__sub">{{ $transferList->count() }} {{ $transferList->count() === 1 ? 'Movement' : 'Movements' }}</div>
            </header>

            <table class="coach-table">
                <thead>
                    <tr>
                        <th style="width: 26mm">Date</th>
                        <th>Service</th>
                        <th style="width: 40mm">Driver</th>
                        <th style="width: 36mm">Mobile</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($transferList as $t)
                    @php
                        $tStart = $tryParse($t->time_from ?? null);
                        $drivers = collect();
                        try {
                            if (method_exists($t, 'getTransferDrivers')) {
                                $drivers = collect($t->getTransferDrivers());
                            }
                        } catch (\Throwable $e) {}
                    @endphp
                    <tr>
                        <td class="cell-date">
                            @if($tStart)
                                <div class="day">{{ $tStart->format('d') }}</div>
                                <div class="month">{{ $tStart->format('M Y') }}</div>
                                <div class="hh">{{ $tStart->format('H:i') }}</div>
                            @else
                                <span class="none">—</span>
                            @endif
                        </td>
                        <td>
                            <div class="svc-name">{{ $t->name ?? 'Transfer' }}</div>
                            @if(!empty($t->pickup_des) || !empty($t->drop_des))
                                <div class="svc-route">
                                    {{ $t->pickup_des ?? '—' }}
                                    <span class="arrow">→</span>
                                    {{ $t->drop_des ?? '—' }}
                                </div>
                            @endif
                        </td>
                        <td>
                            @forelse($drivers as $d)
                                <div class="driver">{{ $d->name ?? '—' }}</div>
                            @empty
                                <span class="none">Not assigned</span>
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

    {{-- ═══════════════════════════════ ACCOMMODATION ═══════════════════════════ --}}

    @if(!empty($hotelIndex))
        <section class="section">
            <header class="section__head">
                <h2 class="section__title">Accommodation</h2>
                <div class="section__sub">
                    {{ count($hotelIndex) }} {{ count($hotelIndex) === 1 ? 'Property' : 'Properties' }} ·
                    {{ $totalNights }} {{ $totalNights === 1 ? 'Night' : 'Nights' }}
                </div>
            </header>

            <ul class="hotel-list">
                @foreach($hotelIndex as $h)
                    @php
                        $dateCarbons = collect($h['dates'])
                            ->map(fn ($d) => $tryParse($d))
                            ->filter();
                        $hotelStart = $dateCarbons->min();
                        $hotelEnd   = $dateCarbons->max();
                        $nights     = count($h['dates']);
                        $svc        = optional($h['package']->service());
                    @endphp
                    <li class="hotel">
                        <div class="hotel__head">
                            <div class="hotel__name">{{ $h['name'] }}</div>
                            <div class="hotel__dates">
                                @if($hotelStart && $hotelEnd)
                                    <span class="range">
                                        {{ $hotelStart->format('d M') }}
                                        @if($hotelEnd->ne($hotelStart))
                                            <span style="color:var(--gold)">—</span>
                                            {{ $hotelEnd->format('d M Y') }}
                                        @else
                                            {{ $hotelEnd->format('Y') }}
                                        @endif
                                    </span>
                                @endif
                                <span class="nights">{{ $nights }} {{ $nights === 1 ? 'Night' : 'Nights' }}</span>
                            </div>
                        </div>

                        <div class="hotel__meta-grid">
                            @if(!empty($svc->address_first))
                                <div class="hotel__field">
                                    <span class="key">Address</span>
                                    <span class="val">{{ $svc->address_first }}</span>
                                </div>
                            @endif
                            @if(!empty($svc->work_phone))
                                <div class="hotel__field">
                                    <span class="key">Phone</span>
                                    <span class="val mono">{{ $svc->work_phone }}</span>
                                </div>
                            @endif
                            @if(!empty($svc->work_email))
                                <div class="hotel__field">
                                    <span class="key">Email</span>
                                    <span class="val mono">{{ $svc->work_email }}</span>
                                </div>
                            @endif
                            @if(!empty($h['package']->reference))
                                <div class="hotel__field">
                                    <span class="key">Ref.</span>
                                    <span class="val mono">{{ $h['package']->reference }}</span>
                                </div>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    {{-- ═════════════════════════════ DOCUMENT FOOTER ════════════════════════════ --}}

    <footer class="doc__footer">
        <div class="brand">EETS · Europe Express Travel Service Int'l Co., Ltd.</div>
        <div class="stamp">Generated · {{ $issuedAt->format('d M Y · H:i') }}</div>
    </footer>

</div>

</body>
</html>
