{{--
    Public client-facing tour landing page.
    Route:  GET /tour/{id}/landingpage  (TourController@landingPage)
    Layout: layouts.public (no auth, no sidebar, no jQuery, no Bootstrap)

    Vars passed from controller:
        $tour           App\Tour
        $serviceTypes   array<string>  index→name map used to label packages
        $tourDays       Collection<App\TourDay> sorted by date, each with eager-loaded packages
        $tourDateFrom   Carbon|null
        $tourDateTo     Carbon|null
        $listRoomsHotel Collection<TourRoomTypeHotel>
        $exclude        int[] package ids to skip (capped, sanitised)
--}}
@extends('layouts.public')

@section('title', trans('main.Itinerary') . ' — ' . $tour->name)

@php
    // Aggregate the rooming summary (e.g. "2DBL + 1SGL") once instead of
    // recomputing inside loops.
    $roomsCodes = '';
    foreach ($listRoomsHotel as $item) {
        $code = optional($item->room_types)->code;
        if (!$code) { continue; }
        $roomsCodes .= ($roomsCodes === '' ? '' : ' + ') . $item->count . $code;
    }

    $logoUrl = asset('img/eets_logo.png');
@endphp

@section('content')
<div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8 print:max-w-none print:p-0">

    {{-- Top bar: logo + (print-only-visible-on-screen) print button --}}
    <header class="mb-8 flex items-center justify-between print:hidden">
        <img src="{{ $logoUrl }}" alt="{{ config('app.name') }}" class="h-12 w-auto" />
        <button
            type="button"
            onclick="window.print()"
            class="inline-flex h-9 items-center gap-2 rounded border border-slate-300 bg-white px-3 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50"
        >
            <x-ui.icon name="printer" />
            {{ trans('main.Print') ?? 'Print' }}
        </button>
    </header>

    {{-- Hero card: tour name + date range --}}
    <section class="overflow-hidden rounded-md bg-white shadow-card">
        <div class="border-b border-slate-200 px-6 py-5 sm:px-8 sm:py-6">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <p class="text-xs italic text-slate-500">{{ trans('main.Tour') }}</p>
                    <h1 class="mt-1 text-2xl font-semibold text-slate-900">{{ $tour->name }}</h1>
                </div>
                <div>
                    <p class="text-xs italic text-slate-500">{{ trans('main.Dates') }}</p>
                    <p class="mt-1 text-2xl font-semibold text-slate-900">
                        @if ($tourDateFrom && $tourDateTo)
                            {{ $tourDateFrom->translatedFormat('d M') }}
                            —
                            {{ $tourDateTo->translatedFormat('d M Y') }}
                        @else
                            <span class="text-slate-400">—</span>
                        @endif
                    </p>
                </div>
            </div>

            @if ($roomsCodes !== '')
                <div class="mt-4 text-sm text-slate-500">
                    <span class="font-medium text-slate-700">{{ trans('main.Rooms') ?? 'Rooms' }}:</span>
                    {{ $roomsCodes }}
                </div>
            @endif
        </div>

        @php
            $hero = $tour->attachments()->first();
        @endphp
        @if ($hero && !empty($hero->url))
            <figure class="bg-slate-100">
                <img
                    src="{{ $hero->url }}"
                    alt="{{ $tour->name }}"
                    class="h-72 w-full object-cover sm:h-96"
                    loading="lazy"
                />
            </figure>
        @endif
    </section>

    {{-- Day-by-day itinerary --}}
    @foreach ($tourDays as $dayIndex => $tourDay)
        @php
            // The `description_package` flag marks a package whose only
            // job is to carry rich-text description applied to the NEXT
            // non-description package. We collect them as we walk and
            // flush onto the next real entry.
            $pendingDescription = '';
            $dayNumber = $loop->iteration;

            try {
                $dayLabel = (new \Carbon\Carbon($tourDay->date))->translatedFormat('F j, Y (l)');
            } catch (\Throwable $e) {
                $dayLabel = '';
            }
        @endphp

        <section class="mt-10 print:mt-6">
            <header class="mb-4 border-b border-slate-200 pb-2">
                <h2 class="text-lg font-semibold text-slate-900">
                    {{ trans('main.Day') ?? 'Day' }} {{ $dayNumber }}
                    @if ($dayLabel)
                        <span class="text-slate-500"> — {{ $dayLabel }}</span>
                    @endif
                </h2>
            </header>

            <ol class="space-y-4">
                @foreach ($tourDay->packages as $package)
                    @php
                        if ($package->description_package) {
                            $pendingDescription = (string) $package->description;
                            continue;
                        }

                        if (in_array((int) $package->id, $exclude, true)) {
                            // Caller explicitly hid this package from
                            // the share link. Skip but still flush any
                            // pending description so the NEXT real
                            // package picks it up (matches old behaviour).
                            continue;
                        }

                        $srv = $package->service();
                        $packageImages = [];
                        if ($srv && !empty($srv->files)) {
                            foreach ($srv->files as $file) {
                                if (in_array($file->attach_content_type, ['image/png', 'image/jpeg', 'image/webp'], true)) {
                                    $packageImages[] = [
                                        'id' => $file->id,
                                        'file_name' => $file->attach_file_name,
                                    ];
                                }
                            }
                        }

                        $timeFrom = $package->time_from
                            ? \Carbon\Carbon::parse($package->time_from)->format('H:i')
                            : null;
                        $timeTo = $package->time_to
                            ? \Carbon\Carbon::parse($package->time_to)->format('H:i')
                            : null;

                        $packageTypeLabel = ($package->type !== null && isset($serviceTypes[$package->type]))
                            ? ucfirst($serviceTypes[$package->type])
                            : null;

                        $alternate = $loop->iteration % 2 === 0;

                        $descriptionForThis = $pendingDescription;
                        $pendingDescription = '';
                    @endphp

                    <li class="grid grid-cols-1 gap-6 md:grid-cols-2 md:items-start
                               {{ $alternate ? 'md:[&>*:first-child]:order-2' : '' }}">

                        {{-- Image column (hidden on mobile if no image) --}}
                        @if (!empty($packageImages))
                            <figure class="overflow-hidden rounded-md bg-slate-100 shadow-subtle">
                                @php $firstImg = $packageImages[0]; @endphp
                                <img
                                    src="{{ asset('system/App/File/attaches/000/000/' . str_pad($firstImg['id'], 3, '0', STR_PAD_LEFT) . '/original/' . $firstImg['file_name']) }}"
                                    alt="{{ $package->name }}"
                                    class="h-56 w-full object-cover md:h-64"
                                    loading="lazy"
                                />
                            </figure>
                        @else
                            <div class="hidden md:block" aria-hidden="true"></div>
                        @endif

                        {{-- Text column --}}
                        <article class="space-y-2">
                            <h3 class="text-xl font-semibold text-slate-900">
                                {{ $package->name }}
                            </h3>

                            <p class="text-sm text-slate-500">
                                @if ($timeFrom){{ $timeFrom }}@endif
                                @if ($timeFrom && $timeTo) — {{ $timeTo }}@endif
                                @if ($packageTypeLabel)
                                    <span class="mx-2 text-slate-300">·</span>
                                    {{ $packageTypeLabel }}
                                @endif
                            </p>

                            @if ($descriptionForThis !== '')
                                <div class="prose-sm max-w-prose text-sm leading-relaxed text-slate-600">
                                    {!! purify_html($descriptionForThis) !!}
                                </div>
                            @endif
                        </article>
                    </li>
                @endforeach
            </ol>
        </section>
    @endforeach

    {{-- Footer: agency credit --}}
    <footer class="mt-12 border-t border-slate-200 pt-6 text-center text-xs text-slate-400 print:mt-8">
        {{ config('app.name', 'TMS') }}
    </footer>
</div>
@endsection
