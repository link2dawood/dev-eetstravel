{{--
    Service vouchers for PDF and DOC ($mode: pdf | doc), one per page.
    Data comes from App\Helper\TourDocumentPresenter::vouchers().
--}}
@php
    $isDoc = $mode === 'doc';
    // DOC: relative path survives HTMLPurifier and is resolved against public/ by normalizeHtmlForPhpWord
    $logo = $isDoc ? 'img/eets_logo_small.jpg' : public_path('img/eets_logo_small.jpg');
    $ink = '#1f2937';
    $muted = '#6b7280';
    $accent = '#9f1239';
    $band = '#f3f4f6';
    $issuedOn = now()->format('j M Y');
    $issuer = $office['name'] ?? 'EETS Travel';
@endphp
@unless($isDoc)
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Vouchers - {{ $summary['name'] }}</title>
    <style>
        @page { margin: 14mm 12mm; }
        body { margin: 0; font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 11px; line-height: 1.45; color: {{ $ink }}; }
        table { border-collapse: collapse; width: 100%; }
        td { vertical-align: top; }
        .voucher { page-break-after: always; }
        .voucher:last-child { page-break-after: auto; }
        .head td { padding: 0; }
        .title { font-size: 22px; font-weight: bold; color: {{ $accent }}; letter-spacing: .5px; }
        .meta { text-align: right; font-size: 10px; color: {{ $muted }}; line-height: 1.6; }
        .meta strong { color: {{ $ink }}; font-size: 13px; }
        .block { margin-top: 14px; }
        .block-title { background: {{ $band }}; padding: 5px 8px; font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: .5px; color: {{ $muted }}; border-left: 4px solid {{ $accent }}; }
        .rows td { padding: 6px 8px; border-bottom: 1px solid #e5e7eb; }
        .rows td.label { width: 30%; color: {{ $muted }}; font-weight: bold; }
        .supplier { font-size: 14px; font-weight: bold; }
        .type { display: inline-block; padding: 2px 8px; border-radius: 3px; background: #eef2f7; color: #334155; font-size: 10px; font-weight: bold; text-transform: uppercase; }
        .terms { margin-top: 14px; font-size: 10px; color: #4b5563; }
        .sign td { padding-top: 34px; width: 50%; font-size: 10px; color: {{ $muted }}; }
        .sign span { display: block; border-top: 1px solid #9ca3af; padding-top: 4px; margin-right: 24px; }
    </style>
</head>
<body>
@endunless
@forelse($vouchers as $v)
    <div class="voucher">
        <table class="head">
            <tr>
                <td style="width:50%;"><p style="margin:0;"><img src="{{ $logo }}" alt="EETS" style="height:58px;"></p>
                    <p class="title" style="margin:0; font-size:22px; font-weight:bold; color:{{ $accent }};">Service Voucher</p>
                </td>
                <td class="meta" style="text-align:right; font-size:10px; color:{{ $muted }};">
                    <p style="margin:0; text-align:right;">Voucher no.</p>
                    <p style="margin:0; text-align:right; font-size:13px; font-weight:bold; color:{{ $ink }};">{{ $v['number'] }}</p>
                    <p style="margin:0; text-align:right;">Issued {{ $issuedOn }} by {{ $issuer }}</p>
                    @if($office && $office['phone'])<p style="margin:0; text-align:right;">Tel {{ $office['phone'] }}</p>@endif
                </td>
            </tr>
        </table>

        {{-- Who provides the service --}}
        <p class="block-title" style="background:{{ $band }}; font-weight:bold; font-size:10px; color:{{ $muted }}; padding:5px 8px; margin:14px 0 0;">TO (SERVICE PROVIDER)</p>
        <table class="rows">
            <tr><td class="label" style="width:30%; color:{{ $muted }}; font-weight:bold;">Provider</td><td><span class="supplier" style="font-size:14px; font-weight:bold;">{{ $v['supplier'] }}</span> &nbsp;<span class="type" style="font-size:10px; font-weight:bold; color:#334155;">{{ strtoupper($v['type']) }}</span></td></tr>
            @if($v['supplier_address'])<tr><td class="label" style="color:{{ $muted }}; font-weight:bold;">Address</td><td>{{ $v['supplier_address'] }}</td></tr>@endif
            @if($v['supplier_phone'] || $v['supplier_email'])<tr><td class="label" style="color:{{ $muted }}; font-weight:bold;">Contact</td><td>{{ trim($v['supplier_phone'] . ($v['supplier_phone'] && $v['supplier_email'] ? ' · ' : '') . $v['supplier_email']) }}</td></tr>@endif
        </table>

        {{-- When --}}
        <p class="block-title" style="background:{{ $band }}; font-weight:bold; font-size:10px; color:{{ $muted }}; padding:5px 8px; margin:14px 0 0;">WHEN</p>
        <table class="rows">
            @foreach($v['when'] as $label => $value)
                <tr><td class="label" style="width:30%; color:{{ $muted }}; font-weight:bold;">{{ $label }}</td><td><strong>{{ $value }}</strong></td></tr>
            @endforeach
        </table>

        {{-- For whom --}}
        <p class="block-title" style="background:{{ $band }}; font-weight:bold; font-size:10px; color:{{ $muted }}; padding:5px 8px; margin:14px 0 0;">FOR</p>
        <table class="rows">
            <tr><td class="label" style="width:30%; color:{{ $muted }}; font-weight:bold;">Group</td><td>{{ $v['tour'] }}</td></tr>
            <tr><td class="label" style="color:{{ $muted }}; font-weight:bold;">Guests</td><td>{{ $v['guests'] }}</td></tr>
            @if($v['tour_leader'])<tr><td class="label" style="color:{{ $muted }}; font-weight:bold;">Tour leader</td><td>{{ $v['tour_leader'] }}</td></tr>@endif
        </table>

        {{-- What exactly --}}
        @if(count($v['details']))
            <p class="block-title" style="background:{{ $band }}; font-weight:bold; font-size:10px; color:{{ $muted }}; padding:5px 8px; margin:14px 0 0;">SERVICE DETAILS</p>
            <table class="rows">
                @foreach($v['details'] as $label => $value)
                    <tr><td class="label" style="width:30%; color:{{ $muted }}; font-weight:bold;">{{ $label }}</td><td>{{ $value }}</td></tr>
                @endforeach
            </table>
        @endif

        <p class="terms" style="font-size:10px; color:#4b5563; margin-top:14px;">
            Please provide the services listed above and invoice <strong>{{ $issuer }}</strong>@if($office && $office['address']), {{ $office['address'] }}@endif,
            quoting voucher no. {{ $v['number'] }}. Extras not listed here are paid directly by the guests.
            @if($v['in_charge']) Questions: {{ $v['in_charge'] }}@if($office && $office['phone']), {{ $office['phone'] }}@endif.@endif
        </p>

        <table class="sign">
            <tr>
                <td style="padding-top:34px; width:50%; font-size:10px; color:{{ $muted }};"><span>Issued by (signature / stamp)</span></td>
                <td style="padding-top:34px; width:50%; font-size:10px; color:{{ $muted }};"><span>Service provided - provider signature</span></td>
            </tr>
        </table>
    </div>
@empty
    <p style="color:{{ $muted }};">No services are marked for vouchers on this tour.</p>
@endforelse
@unless($isDoc)
</body>
</html>
@endunless
