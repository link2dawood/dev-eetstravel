{{--
    Tour itinerary for HTML, PDF and DOC ($mode: html | pdf | doc).
    Data comes from App\Helper\TourDocumentPresenter. Markup stays simple
    (tables, no flexbox, no headings inside cells) so dompdf and PhpWord can render it.
--}}
@php
    $isPdf = $mode === 'pdf';
    $isDoc = $mode === 'doc';
    // DOC: relative path survives HTMLPurifier and is resolved against public/ by normalizeHtmlForPhpWord
    $logo = $isPdf ? public_path('img/eets_logo_small.jpg') : ($isDoc ? 'img/eets_logo_small.jpg' : asset('img/eets_logo_small.jpg'));
    $ink = '#1f2937';      // body text
    $muted = '#6b7280';    // labels, secondary text
    $accent = '#9f1239';   // EETS red, used sparingly
    $band = '#f3f4f6';     // light background for headers
@endphp
@unless($isDoc)
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Itinerary - {{ $summary['name'] }}</title>
    <style>
        @page { margin: 14mm 12mm; }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: 'DejaVu Sans', 'Segoe UI', Arial, sans-serif; font-size: 11px; line-height: 1.45; color: {{ $ink }}; background: #fff; }
        .page { max-width: 190mm; margin: 0 auto; padding: {{ $isPdf ? '0' : '24px 16px' }}; }
        table { border-collapse: collapse; width: 100%; }
        td, th { vertical-align: top; }
        .header td { padding: 0; }
        .office { text-align: right; font-size: 10px; color: {{ $muted }}; line-height: 1.5; }
        .office strong { color: {{ $ink }}; font-size: 11px; }
        .doc-title { margin: 14px 0 2px; font-size: 22px; letter-spacing: .5px; color: {{ $accent }}; font-weight: bold; }
        .tour-name { margin: 0 0 12px; font-size: 15px; font-weight: bold; }
        .tour-name span { font-weight: normal; color: {{ $muted }}; font-size: 12px; }
        .section { margin: 18px 0 8px; padding-bottom: 4px; border-bottom: 2px solid {{ $accent }}; font-size: 13px; font-weight: bold; text-transform: uppercase; letter-spacing: .6px; }
        .facts td { padding: 5px 8px; border-bottom: 1px solid #e5e7eb; }
        .facts td.label { width: 28%; color: {{ $muted }}; font-weight: bold; }
        .grid th { background: {{ $band }}; text-align: left; padding: 6px 8px; font-size: 10px; color: {{ $muted }}; text-transform: uppercase; letter-spacing: .4px; border-bottom: 1px solid #d1d5db; }
        .grid td { padding: 6px 8px; border-bottom: 1px solid #e5e7eb; }
        .day { margin-top: 14px; }
        /* keep each row whole; keeping whole days together left half-empty pages */
        tr { page-break-inside: avoid; }
        .day-head { page-break-after: avoid; }
        .day-head { background: {{ $band }}; border-left: 4px solid {{ $accent }}; padding: 7px 10px; font-size: 13px; font-weight: bold; }
        .day-head span { font-weight: normal; color: {{ $muted }}; }
        .items td { padding: 7px 8px; border-bottom: 1px solid #eef0f2; }
        .items td.time { width: 16%; white-space: nowrap; font-weight: bold; }
        .items td.what { width: 16%; }
        .type { display: inline-block; padding: 1px 6px; border-radius: 3px; background: #eef2f7; color: #334155; font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: .3px; }
        .title { font-weight: bold; font-size: 12px; }
        .line { color: #4b5563; font-size: 10px; }
        .note { margin: 8px 0 0; padding: 7px 10px; background: #fffbeb; border-left: 3px solid #f59e0b; }
        .overnight { margin: 6px 0 0; padding: 5px 10px; color: {{ $muted }}; font-size: 10px; }
        .empty { color: {{ $muted }}; font-style: italic; padding: 7px 8px; }
        .footer { margin-top: 22px; padding-top: 8px; border-top: 1px solid #e5e7eb; color: {{ $muted }}; font-size: 9px; }
        @media print { .page { padding: 0; } }
        @media (max-width: 600px) { .items td.what { display: none; } .facts td.label { width: 40%; } }
    </style>
</head>
<body>
@endunless
<div class="page">
    {{-- Header: logo + office --}}
    <table class="header">
        <tr>
            <td style="width:45%;"><img src="{{ $logo }}" alt="EETS" style="height:{{ $isDoc ? '60' : '64' }}px;"></td>
            <td class="office" style="text-align:right; color:{{ $muted }}; font-size:10px;">
                @if($office)
                    <p style="margin:0; text-align:right; font-weight:bold; color:{{ $ink }}; font-size:11px;">{{ $office['name'] }}</p>
                    <p style="margin:0; text-align:right;">{{ $office['address'] }}</p>
                    <p style="margin:0; text-align:right;">@if($office['phone'])Tel {{ $office['phone'] }}@endif @if($office['fax']) · Fax {{ $office['fax'] }}@endif</p>
                @endif
            </td>
        </tr>
    </table>

    <p class="doc-title" style="color:{{ $accent }}; font-size:22px; font-weight:bold; margin:14px 0 2px;">Tour Itinerary</p>
    <p class="tour-name" style="font-size:15px; font-weight:bold; margin:0 0 12px;">{{ $summary['name'] }}@if($summary['code']) <span style="color:{{ $muted }}; font-weight:normal; font-size:12px;">· Ref. {{ $summary['code'] }}</span>@endif</p>

    {{-- At a glance --}}
    <p class="section" style="font-weight:bold; font-size:13px; color:{{ $accent }};">At a glance</p>
    <table class="facts">
        @foreach ([
            'Dates' => $summary['start'] . ' – ' . $summary['end'] . ($summary['duration'] ? '  (' . $summary['duration'] . ')' : ''),
            'Route' => $summary['route'],
            'Group' => $summary['group'],
            'Rooms' => $summary['rooms'],
            'Tour leader' => trim($summary['tour_leader'] . ($summary['tour_leader_phone'] ? ' · ' . $summary['tour_leader_phone'] : ''), ' ·'),
            'Your contact' => trim($summary['in_charge'] . ($summary['in_charge_email'] ? ' · ' . $summary['in_charge_email'] : '') . (($office['phone'] ?? '') ? ' · Office ' . $office['phone'] : ''), ' ·'),
        ] as $label => $value)
            @if(trim((string) $value) !== '')
                <tr>
                    <td class="label" style="width:28%; color:{{ $muted }}; font-weight:bold;">{{ $label }}</td>
                    <td>{{ $value }}</td>
                </tr>
            @endif
        @endforeach
    </table>

    {{-- Hotels overview --}}
    @if(count($hotels))
        <p class="section" style="font-weight:bold; font-size:13px; color:{{ $accent }};">Hotels</p>
        <table class="grid">
            <tr>
                <th style="background:{{ $band }};">Hotel</th>
                <th style="background:{{ $band }};">Check-in</th>
                <th style="background:{{ $band }};">Check-out</th>
                <th style="background:{{ $band }};">Nights</th>
                <th style="background:{{ $band }};">Contact</th>
            </tr>
            @foreach($hotels as $hotel)
                <tr>
                    <td><p style="margin:0; font-weight:bold;">{{ $hotel['name'] }}</p>@if($hotel['address'])<p class="line" style="margin:0; color:#4b5563; font-size:10px;">{{ $hotel['address'] }}</p>@endif</td>
                    <td style="white-space:nowrap;">{{ $hotel['check_in'] }}</td>
                    <td style="white-space:nowrap;">{{ $hotel['check_out'] }}</td>
                    <td>{{ $hotel['nights'] }}</td>
                    <td>{{ $hotel['phone'] ?: '-' }}</td>
                </tr>
            @endforeach
        </table>
    @endif

    {{-- Day by day --}}
    <p class="section" style="font-weight:bold; font-size:13px; color:{{ $accent }};">Day by day</p>
    @foreach($days as $day)
        <div class="day">
            <p class="day-head" style="background:{{ $band }}; font-weight:bold; font-size:13px; padding:7px 10px; margin:12px 0 0;">Day {{ $day['number'] }} <span style="font-weight:normal; color:{{ $muted }};">· {{ $day['date'] }}</span></p>
            @if(count($day['items']))
                <table class="items">
                    @foreach($day['items'] as $item)
                        <tr>
                            <td class="time" style="width:16%; font-weight:bold;">{{ $item['time'] ?: '-' }}</td>
                            <td class="what" style="width:16%;"><span class="type" style="font-size:9px; font-weight:bold; color:#334155;">{{ strtoupper($item['type']) }}</span></td>
                            <td>
                                <p class="title" style="margin:0; font-weight:bold; font-size:12px;">{{ $item['title'] }}</p>
                                @foreach($item['lines'] as $line)
                                    <p class="line" style="margin:0; color:#4b5563; font-size:10px;">{{ $line }}</p>
                                @endforeach
                            </td>
                        </tr>
                    @endforeach
                </table>
            @elseif(!count($day['notes']))
                <p class="empty" style="color:{{ $muted }}; font-style:italic;">Free day - no services planned.</p>
            @endif
            @foreach($day['notes'] as $note)
                <p class="note" style="background:#fffbeb; padding:7px 10px;">{!! nl2br(e($note)) !!}</p>
            @endforeach
            @if($day['overnight'])
                <p class="overnight" style="color:{{ $muted }}; font-size:10px;">Overnight: {{ $day['overnight'] }}</p>
            @endif
        </div>
    @endforeach

    <p class="footer" style="color:{{ $muted }}; font-size:9px; margin-top:22px;">
        All times are local. The programme may change locally; your tour leader will confirm daily details.
        @if($office && $office['phone']) In case of emergency call {{ $office['name'] }}: {{ $office['phone'] }}.@endif
        Issued {{ now()->format('j M Y') }}.
    </p>
</div>
@unless($isDoc)
</body>
</html>
@endunless
