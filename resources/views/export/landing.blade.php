{{--
    Client-facing tour landing page (route: landing_page).
    Data: App\Helper\TourDocumentPresenter (same source as the itinerary) + $heroImage.
    Self-contained: no CDN assets, inline SVG icons, responsive and printable.
--}}
@php
    $icons = [
        'Hotel' => '<path d="M3 7v11m0-4h18m0 4v-8a2 2 0 0 0-2-2h-8v6"/><circle cx="7" cy="10" r="2"/>',
        'Restaurant' => '<path d="M4 3v7a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2V3M6.5 3v18M17 21V3c-2 1.5-3 4-3 7 0 2 1 3 3 3"/>',
        'Transfer' => '<rect x="3" y="5" width="18" height="11" rx="2"/><path d="M3 11h18M7 19v-3m10 3v-3"/><circle cx="7.5" cy="13.5" r=".5"/><circle cx="16.5" cy="13.5" r=".5"/>',
        'Guide' => '<circle cx="12" cy="7" r="3"/><path d="M6 21v-2a6 6 0 0 1 12 0v2"/>',
        'Sightseeing' => '<path d="M4 8h3l2-3h6l2 3h3v11H4z"/><circle cx="12" cy="13" r="3.5"/>',
        'Cruise' => '<path d="M3 17c2 2 4 2 6 0 2 2 4 2 6 0 2 2 4 2 6 0M5 14l1-6h12l1 6M12 4v4"/>',
        'Flight' => '<path d="M10 14 3 11l1-2 7 1 4-6 2 1-2 7 6 2-1 2-6-1-3 5-2-1z"/>',
        'default' => '<circle cx="12" cy="12" r="4"/>',
    ];
    $icon = function ($type) use ($icons) {
        return '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . ($icons[$type] ?? $icons['default']) . '</svg>';
    };
    $nights = collect($hotels)->sum('nights');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $summary['name'] }} - Your tour</title>
    <meta name="robots" content="noindex">
    <style>
        :root {
            --ink: #1c2333; --muted: #667085; --line: #e6e8ee; --bg: #f6f7fb; --card: #fff;
            --brand: #9f1239; --brand-soft: #fdf2f5; --navy: #1f2a44;
            --radius: 14px; --shadow: 0 1px 2px rgba(16,24,40,.05), 0 8px 24px rgba(16,24,40,.06);
        }
        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body { overflow-x: hidden; overflow-wrap: anywhere; margin: 0; font-family: system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; color: var(--ink); background: var(--bg); line-height: 1.55; -webkit-font-smoothing: antialiased; }
        img { max-width: 100%; display: block; }
        a { color: var(--brand); }
        .wrap { max-width: 1040px; margin: 0 auto; padding: 0 20px; }

        /* top bar */
        .topbar { background: #fff; border-bottom: 1px solid var(--line); }
        .topbar .wrap { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding-top: 10px; padding-bottom: 10px; }
        .topbar img { height: 46px; width: auto; }
        .topbar .contact { font-size: 14px; color: var(--muted); text-align: right; }
        .topbar .contact strong { color: var(--ink); }

        /* hero */
        .hero { position: relative; color: #fff; background: linear-gradient(135deg, var(--navy), var(--brand)); }
        .hero.has-image { background-size: cover; background-position: center; }
        .hero::after { content: ''; position: absolute; inset: 0; background: linear-gradient(180deg, rgba(15,20,35,.15), rgba(15,20,35,.75)); }
        .hero .wrap { position: relative; z-index: 1; padding-top: 96px; padding-bottom: 44px; }
        .eyebrow { display: inline-block; font-size: 12px; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; background: rgba(255,255,255,.16); padding: 5px 10px; border-radius: 999px; }
        .hero h1 { margin: 14px 0 8px; font-size: clamp(28px, 5vw, 46px); line-height: 1.12; letter-spacing: -.01em; }
        .hero .meta { font-size: 17px; opacity: .95; }
        .hero .meta span + span::before { content: '·'; margin: 0 10px; opacity: .7; }

        /* facts */
        .facts { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(190px, 100%), 1fr)); gap: 14px; margin-top: -28px; position: relative; z-index: 2; }
        .fact { background: var(--card); border-radius: var(--radius); box-shadow: var(--shadow); padding: 16px 18px; }
        .fact .label { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: var(--muted); }
        .fact .value { margin-top: 4px; font-size: 16px; font-weight: 600; }

        /* sections */
        section { margin-top: 44px; }
        h2 { font-size: 22px; margin: 0 0 6px; letter-spacing: -.01em; }
        .lead { color: var(--muted); margin: 0 0 18px; }

        /* day jump links */
        .daynav { display: flex; gap: 8px; overflow-x: auto; padding-bottom: 6px; margin-bottom: 18px; scrollbar-width: thin; }
        .daynav a { flex: 0 0 auto; text-decoration: none; color: var(--ink); background: var(--card); border: 1px solid var(--line); border-radius: 999px; padding: 6px 12px; font-size: 13px; font-weight: 600; }
        .daynav a:hover, .daynav a:focus { border-color: var(--brand); color: var(--brand); }

        /* days */
        .day { display: grid; grid-template-columns: 92px minmax(0, 1fr); gap: 18px; margin-bottom: 18px; scroll-margin-top: 16px; }
        .datebadge { background: var(--card); border-radius: var(--radius); box-shadow: var(--shadow); text-align: center; padding: 12px 6px; align-self: start; position: sticky; top: 16px; }
        .datebadge .d { font-size: 11px; font-weight: 800; letter-spacing: .1em; color: var(--brand); text-transform: uppercase; }
        .datebadge .n { font-size: 30px; font-weight: 800; line-height: 1.1; }
        .datebadge .m { font-size: 13px; color: var(--muted); font-weight: 600; }
        .daycard { background: var(--card); border-radius: var(--radius); box-shadow: var(--shadow); overflow: hidden; }
        .daycard header { padding: 14px 18px; border-bottom: 1px solid var(--line); display: flex; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
        .daycard header h3 { margin: 0; font-size: 17px; }
        .daycard header .sub { color: var(--muted); font-size: 14px; }
        .item { display: grid; grid-template-columns: 74px minmax(0, 1fr) auto; gap: 14px; padding: 14px 18px; border-bottom: 1px solid var(--line); align-items: start; }
        .item:last-child { border-bottom: 0; }
        .time { font-weight: 700; font-size: 14px; white-space: nowrap; padding-top: 2px; }
        .type { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: var(--brand); background: var(--brand-soft); border-radius: 999px; padding: 3px 10px 3px 8px; }
        .title { font-weight: 700; font-size: 16px; margin: 6px 0 2px; }
        .detail { color: var(--muted); font-size: 14px; margin: 0; }
        .thumb { width: 120px; height: 84px; border-radius: 10px; object-fit: cover; background: var(--line); }
        .note { padding: 14px 18px; background: #fffbeb; border-top: 1px solid #fde68a; font-size: 14px; color: #5b4a1a; }
        .note p { margin: 0 0 4px; }
        .overnight { display: flex; align-items: center; gap: 8px; padding: 10px 18px; background: #f8fafc; color: var(--muted); font-size: 14px; border-top: 1px solid var(--line); }
        .free { padding: 16px 18px; color: var(--muted); font-style: italic; }

        /* hotels */
        .hotels { display: grid; grid-template-columns: repeat(auto-fill, minmax(min(260px, 100%), 1fr)); gap: 16px; }
        .hotel { background: var(--card); border-radius: var(--radius); box-shadow: var(--shadow); overflow: hidden; }
        .hotel .img { height: 150px; background: linear-gradient(135deg, #e8ebf3, #f5e9ee); display: flex; align-items: center; justify-content: center; color: #b6bccb; }
        .hotel .img img { width: 100%; height: 150px; object-fit: cover; }
        .hotel .body { padding: 14px 16px 16px; }
        .hotel h3 { margin: 0 0 4px; font-size: 16px; }
        .hotel .when { font-size: 14px; font-weight: 600; }
        .hotel .addr { font-size: 13px; color: var(--muted); margin-top: 6px; }

        /* contacts */
        .contacts { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(240px, 100%), 1fr)); gap: 16px; }
        .contact-card { background: var(--card); border-radius: var(--radius); box-shadow: var(--shadow); padding: 16px 18px; }
        .contact-card .label { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: var(--muted); }
        .contact-card .name { font-weight: 700; margin-top: 4px; }
        .contact-card .line { color: var(--muted); font-size: 14px; }

        footer { margin: 48px 0 0; padding: 22px 0 36px; color: var(--muted); font-size: 13px; border-top: 1px solid var(--line); background: #fff; }

        @media (max-width: 640px) {
            .wrap { padding: 0 16px; }
            .facts { grid-template-columns: minmax(0, 1fr); }
            .hero .wrap { padding-top: 56px; padding-bottom: 40px; }
            .hero .meta span { display: block; }
            .hero .meta span + span::before { content: none; }
            .day { grid-template-columns: minmax(0, 1fr); gap: 8px; }
            .daycard header .sub { width: 100%; }
            .datebadge { position: static; display: flex; align-items: baseline; gap: 8px; text-align: left; padding: 8px 12px; box-shadow: none; background: transparent; }
            .datebadge .n { font-size: 20px; }
            .item { grid-template-columns: minmax(0, 1fr); gap: 4px; }
            .thumb { width: 100%; height: 160px; margin-top: 8px; }
            .topbar .contact { font-size: 12px; max-width: 60%; }
            .topbar img { height: 38px; }
        }
        @media print {
            body { background: #fff; }
            .daynav { display: none; }
            .hero::after { background: rgba(15,20,35,.45); }
            .fact, .daycard, .hotel, .contact-card, .datebadge { box-shadow: none; border: 1px solid var(--line); }
            .day, .hotel { break-inside: avoid; }
            .datebadge { position: static; }
        }
    </style>
</head>
<body>
    <div class="topbar">
        <div class="wrap">
            <img src="{{ asset('img/eets_logo.png') }}" alt="{{ $office['name'] ?? 'EETS' }}">
            @if($office)
                <div class="contact"><strong>{{ $office['name'] }}</strong>@if($office['phone'])<br>{{ $office['phone'] }}@endif</div>
            @endif
        </div>
    </div>

    <header class="hero {{ $heroImage ? 'has-image' : '' }}" @if($heroImage) style="background-image:url('{{ $heroImage }}')" @endif>
        <div class="wrap">
            <span class="eyebrow">Your tour</span>
            <h1>{{ $summary['name'] }}</h1>
            <div class="meta">
                <span>{{ $summary['start'] }} – {{ $summary['end'] }}</span>
                @if($summary['duration'])<span>{{ $summary['duration'] }}</span>@endif
                @if($summary['route'])<span>{{ $summary['route'] }}</span>@endif
            </div>
        </div>
    </header>

    <main class="wrap">
        <div class="facts">
            <div class="fact"><div class="label">Travel dates</div><div class="value">{{ $summary['start'] }}<br>{{ $summary['end'] }}</div></div>
            <div class="fact"><div class="label">Length</div><div class="value">{{ $summary['duration'] ?: '-' }}</div></div>
            @if($summary['route'])<div class="fact"><div class="label">Route</div><div class="value">{{ $summary['route'] }}</div></div>@endif
            <div class="fact"><div class="label">Group</div><div class="value">{{ $summary['group'] }}</div></div>
        </div>

        <section id="journey">
            <h2>Your journey, day by day</h2>
            <p class="lead">Everything planned for each day. Times are local and may be adjusted by your tour leader.</p>
            <nav class="daynav" aria-label="Jump to day">
                @foreach($days as $day)
                    <a href="#day-{{ $day['number'] }}">Day {{ $day['number'] }}</a>
                @endforeach
            </nav>

            @foreach($days as $day)
                @php $date = \Carbon\Carbon::parse($day['iso']); @endphp
                <article class="day" id="day-{{ $day['number'] }}">
                    <div class="datebadge" aria-hidden="true">
                        <div class="d">Day {{ $day['number'] }}</div>
                        <div class="n">{{ $date->format('j') }}</div>
                        <div class="m">{{ $date->format('M · D') }}</div>
                    </div>
                    <div class="daycard">
                        <header>
                            <h3>Day {{ $day['number'] }}</h3>
                            <span class="sub">{{ $day['date'] }}</span>
                        </header>

                        @forelse($day['items'] as $item)
                            <div class="item">
                                <div class="time">{{ $item['time'] ?: '' }}</div>
                                <div>
                                    <span class="type">{!! $icon($item['type']) !!}{{ $item['type'] }}</span>
                                    <p class="title">{{ $item['title'] }}</p>
                                    @foreach($item['lines'] as $line)
                                        <p class="detail">{{ $line }}</p>
                                    @endforeach
                                </div>
                                <div>
                                    @if(!empty($item['image']))
                                        <img class="thumb" src="{{ $item['image'] }}" alt="{{ $item['title'] }}" loading="lazy">
                                    @endif
                                </div>
                            </div>
                        @empty
                            @if(!count($day['notes']))
                                <div class="free">Free day - time to explore at your own pace.</div>
                            @endif
                        @endforelse

                        @foreach($day['notes'] as $note)
                            <div class="note">
                                @foreach(preg_split('/\R/', $note) as $line)
                                    <p>{{ $line }}</p>
                                @endforeach
                            </div>
                        @endforeach

                        @if($day['overnight'])
                            <div class="overnight">{!! $icon('Hotel') !!} Overnight: <strong>{{ $day['overnight'] }}</strong></div>
                        @endif
                    </div>
                </article>
            @endforeach
        </section>

        @if(count($hotels))
            <section id="hotels">
                <h2>Where you'll stay</h2>
                <p class="lead">{{ count($hotels) }} {{ count($hotels) == 1 ? 'hotel' : 'hotels' }} · {{ $nights }} {{ $nights == 1 ? 'night' : 'nights' }}</p>
                <div class="hotels">
                    @foreach($hotels as $hotel)
                        <div class="hotel">
                            <div class="img">
                                @if(!empty($hotel['image']))
                                    <img src="{{ $hotel['image'] }}" alt="{{ $hotel['name'] }}" loading="lazy">
                                @else
                                    <svg viewBox="0 0 24 24" width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">{!! $icons['Hotel'] !!}</svg>
                                @endif
                            </div>
                            <div class="body">
                                <h3>{{ $hotel['name'] }}</h3>
                                <div class="when">{{ $hotel['check_in'] }} – {{ $hotel['check_out'] }} · {{ $hotel['nights'] }} {{ $hotel['nights'] == 1 ? 'night' : 'nights' }}</div>
                                @if($hotel['address'])<div class="addr">{{ $hotel['address'] }}</div>@endif
                                @if($hotel['phone'])<div class="addr">{{ $hotel['phone'] }}</div>@endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <section id="contacts">
            <h2>Contacts</h2>
            <p class="lead">Who to call during your trip.</p>
            <div class="contacts">
                @if($summary['tour_leader'] || $summary['tour_leader_phone'])
                    <div class="contact-card">
                        <div class="label">Tour leader</div>
                        <div class="name">{{ $summary['tour_leader'] ?: '-' }}</div>
                        @if($summary['tour_leader_phone'])<div class="line">{{ $summary['tour_leader_phone'] }}</div>@endif
                    </div>
                @endif
                @if($office)
                    <div class="contact-card">
                        <div class="label">Organised by</div>
                        <div class="name">{{ $office['name'] }}</div>
                        @if($office['address'])<div class="line">{{ $office['address'] }}</div>@endif
                        @if($office['phone'])<div class="line">Tel {{ $office['phone'] }}</div>@endif
                    </div>
                @endif
                @if($summary['in_charge'])
                    <div class="contact-card">
                        <div class="label">Your travel consultant</div>
                        <div class="name">{{ $summary['in_charge'] }}</div>
                        @if($summary['in_charge_email'])<div class="line"><a href="mailto:{{ $summary['in_charge_email'] }}">{{ $summary['in_charge_email'] }}</a></div>@endif
                    </div>
                @endif
            </div>
        </section>
    </main>

    <footer>
        <div class="wrap">
            {{ $summary['name'] }}@if($summary['code']) · Ref. {{ $summary['code'] }}@endif · The programme may change locally; your tour leader will confirm daily details.
        </div>
    </footer>
</body>
</html>
