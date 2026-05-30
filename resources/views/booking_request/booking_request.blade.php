{{-- Public supplier booking-request page. Standalone HTML, no @extends —
     served at /booking/{generatedlink}/{id}. External suppliers submit
     their offer here without authenticating.

     Critical JS hooks preserved (all wired by inline scripts at the bottom):
     - #hoteloffers_add_form  — main offer form, posts to offer_update
     - #tags-input            — tagify field for the reply-cc email(s)
     - #status / #option_with_date — status select toggles option-date field
     - #cancellationDays / #cancellationPercentage / #cancellationType
       / #addCancellation / #cancellationRequirements
     - #commentForm / #comment_list — supplier comment AJAX
     - #offers-table          — DataTables with server-side ajax to offers_data
     - #confirmDeleteModal / #confirmDelete / .delete[data-link] — delete flow
     - #additionalEmails (populated by /tp/getaddEmails) + .item-contact
       + #delete_contact_item
     - myFunction(val, val2) — per-room "per person" calc
--}}
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Booking request — TMS</title>

    <link href="{{ asset('css/tailwind.css') }}?v={{ file_exists(public_path('css/tailwind.css')) ? filemtime(public_path('css/tailwind.css')) : time() }}" rel="stylesheet">
    {{-- Tagify CSS --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tagify/4.17.9/tagify.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    {{-- DataTables Bootstrap 5 — kept because the offers table init below uses it --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    {{-- Legacy bootstrap5 still needed because the Bootstrap-5 modals + Tagify rely on bootstrap.bundle.js below --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" crossorigin="anonymous">

    <style>
        @import url('https://rsms.me/inter/inter.css');
        html, body { margin: 0; padding: 0; }
        body {
            font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, sans-serif;
            background: #f8fafc;
            color: #0f172a;
            -webkit-font-smoothing: antialiased;
        }
        *, *::before, *::after { box-sizing: border-box; }

        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none; appearance: none; margin: 0;
        }

        /* Tagify w-full override */
        .tagify { width: 100% !important; }
    </style>
</head>
<body class="min-h-screen">

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

        {{-- Top banner --}}
        <header class="rounded-lg border border-slate-200 bg-white px-5 sm:px-8 py-6">
            <div class="flex flex-col-reverse sm:flex-row sm:items-start sm:justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-xs font-medium uppercase tracking-wide text-primary-700">Booking request</p>
                    <h1 class="mt-1 text-2xl sm:text-3xl font-semibold tracking-tight text-slate-900">Request a quote</h1>
                    <p class="mt-1 text-sm text-slate-500">Tour: <span class="font-medium text-slate-700">{{ $tour_package->getTour()->name }}</span></p>
                </div>
                <div class="shrink-0">
                    <img src="{{ asset('/img/eets_logo_small.jpg') }}" alt="eetstravel" class="h-16 w-auto rounded">
                </div>
            </div>

            @if(Session::has('success'))
                <div class="mt-4 flex items-start gap-2 rounded border border-success-600/20 bg-success-50 px-4 py-3 text-sm text-success-700">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5 mt-0.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    <div>{{ Session::get('success') }}</div>
                </div>
            @endif
            @if(Session::has('error'))
                <div class="mt-4 flex items-start gap-2 rounded border border-danger-600/20 bg-danger-50 px-4 py-3 text-sm text-danger-700">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5 mt-0.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <div>{{ Session::get('error') }}</div>
                </div>
            @endif
        </header>

        {{-- Booking summary --}}
        <section class="rounded-lg border border-slate-200 bg-white">
            <div class="border-b border-slate-200 px-5 sm:px-8 py-4">
                <h2 class="text-sm font-semibold text-slate-900">Tour package & stay</h2>
            </div>
            <div class="px-5 sm:px-8 py-5 space-y-6">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 rounded border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Package</p>
                        <p class="mt-0.5 font-medium text-slate-800">{{ $tour_package->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Start</p>
                        <p class="mt-0.5 font-medium text-slate-800">{{ date('F j Y, h:i a', strtotime($tour_package->time_from)) }}</p>
                    </div>
                </div>

                <p class="text-sm text-slate-600">Please quote from the following:</p>

                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-3 text-sm">
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Supplier</dt>
                        <dd class="mt-0.5 font-semibold text-slate-900">{{ $tour_package->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Arrival time</dt>
                        <dd class="mt-0.5 font-semibold text-slate-900">{{ date('h:m', strtotime($tour_package->time_from)) }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Check-in</dt>
                        <dd class="mt-0.5 font-semibold text-slate-900">{{ date('F j, Y', strtotime($tour_package->time_from)) }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Check-out</dt>
                        <dd class="mt-0.5 font-semibold text-slate-900">{{ date('F j, Y', strtotime($tour_package->time_to)) }}</dd>
                    </div>
                </dl>

                @if(count($selected_room_types))
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-500 mb-2">Room types requested</p>
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2">
                            @foreach($selected_room_types as $selected_room_type)
                                <div class="rounded border border-slate-200 bg-white px-3 py-2 text-sm">
                                    <p class="text-xs text-slate-500">{{ $selected_room_type->name }}</p>
                                    <p class="mt-0.5 font-semibold text-slate-900">{{ $selected_room_type->count_room }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </section>

        {{-- Create offer form --}}
        <section class="rounded-lg border border-slate-200 bg-white">
            <div class="border-b border-slate-200 px-5 sm:px-8 py-4">
                <h2 class="text-sm font-semibold text-slate-900">Create your offer</h2>
                <p class="mt-0.5 text-xs text-slate-500">Fill in the fields below and submit.</p>
            </div>

            <div class="px-5 sm:px-8 py-5">
                @if(count($errors) > 0)
                    <div class="mb-4 rounded border border-danger-600/20 bg-danger-50 px-4 py-3 text-sm text-danger-700">
                        <ul class="list-disc pl-5 space-y-0.5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                @endif

                <form method="POST" id="hoteloffers_add_form" class="form-light space-y-6"
                      action='{!! url('tour_package') !!}/{!! $tour_package->id !!}/offer_update'
                      enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ Session::token() }}">

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                        {{-- LEFT column --}}
                        <div class="space-y-5">

                            {{-- Reply-cc emails (tagify input) --}}
                            <div>
                                <label for="tags-input" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Send copy of reply to this email address</label>
                                <input type="text" id="tags-input" name="emails" value="{{ $tour_package->service()->work_email }}"
                                       class="form-control w-full">
                            </div>

                            {{-- Reference --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="reference" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('Ref No:') !!}</label>
                                    <input type="text" id="reference" name="reference" placeholder="Your booking reference"
                                           class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
                                    <input type="hidden" value="{{ $tour_package->pax }}" id="pax">
                                    <input type="hidden" value="{{ $tour_package->id }}" name="package_id">
                                </div>
                                <div>
                                    <label for="status" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.Status') !!}</label>
                                    <select name="status" id="status"
                                            class="form-select form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
                                        <option value="Offered No rooms blocked">Offered No rooms blocked</option>
                                        <option value="Offered with Option" selected>Offered with Option</option>
                                        <option value="Waiting List">Waiting List</option>
                                        <option value="Unavailable">Unavailable</option>
                                    </select>
                                </div>
                                <div class="sm:col-span-2" id="option_with_date">
                                    <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('Option Date') !!}</label>
                                    <input type="date" name="option_with_date" required
                                           class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
                                </div>
                            </div>

                            {{-- Room rates + breakfast checkbox per type --}}
                            <div class="rounded border border-slate-200 bg-slate-50 px-4 py-4 space-y-3">
                                <h3 class="text-sm font-medium text-slate-700">Room rates</h3>
                                @php $printedRoomNames = []; @endphp
                                @foreach($selected_room_types as $selected_room_type)
                                    <input type="hidden" name="room_type_id[]" value="{{ $selected_room_type->id }}">
                                    @if(!in_array($selected_room_type->name, $printedRoomNames))
                                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                                            <div class="sm:col-span-5">
                                                <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ $selected_room_type->name }}</label>
                                                <input class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600"
                                                       type="text" id="singleRate"
                                                       name="room_rate_{{ $selected_room_type->id }}" placeholder="Rate"
                                                       onkeyup="myFunction(this.value, '{{ $selected_room_type->name }}pp')">
                                            </div>
                                            <div class="sm:col-span-7 flex items-center gap-2 pb-1">
                                                <input type="checkbox" id="singleBreakfastIncluded_{{ $selected_room_type->id }}"
                                                       name="is_breakfast_{{ $selected_room_type->id }}" checked
                                                       class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                                                <label for="singleBreakfastIncluded_{{ $selected_room_type->id }}" class="text-sm text-slate-700">Breakfast included</label>
                                            </div>
                                        </div>
                                        @php $printedRoomNames[] = $selected_room_type->name; @endphp
                                    @endif
                                @endforeach
                            </div>

                            {{-- Per-person preview tables (one per room type) --}}
                            @php $printedRoomNames = []; @endphp
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @foreach($selected_room_types as $selected_room_type)
                                    @if(!in_array($selected_room_type->name, $printedRoomNames))
                                        <div class="rounded border border-slate-200 bg-white px-4 py-3 text-sm">
                                            <p class="text-xs font-medium uppercase tracking-wide text-slate-500">{{ $selected_room_type->name }} per person</p>
                                            <p id="{{ $selected_room_type->name }}pp" class="mt-0.5 font-semibold text-slate-900 font-mono">0</p>
                                        </div>
                                        @php $printedRoomNames[] = $selected_room_type->name; @endphp
                                    @endif
                                @endforeach
                            </div>

                            {{-- Extras --}}
                            <div class="rounded border border-slate-200 bg-slate-50 px-4 py-4">
                                <h3 class="text-sm font-medium text-slate-700 mb-3">Extras</h3>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                    <div>
                                        <label for="city_tax" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">City Tax</label>
                                        <input class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600"
                                               type="number" id="city_tax" name="city_tax" min="0">
                                    </div>
                                    <div>
                                        <label for="portrage_perperson" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Porterage P.P</label>
                                        <input class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600"
                                               type="number" id="portrage_perperson" name="portrage_perperson" min="0">
                                    </div>
                                    <div>
                                        <label for="halfboard" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Halfboard Supp P.P</label>
                                        <input class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600"
                                               type="number" id="halfboard" name="halfboard" min="0" max="999999">
                                    </div>
                                    <div>
                                        <label for="children_cost" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Children Cost</label>
                                        <input class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600"
                                               type="number" id="children_cost" name="children_cost" min="0">
                                    </div>
                                    <div>
                                        <label for="foc_after_every_pax" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">F.O.C</label>
                                        <input class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600"
                                               type="number" id="foc_after_every_pax" name="foc_after_every_pax" min="0">
                                    </div>
                                    <div>
                                        <label for="halfboardMax" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Max per group</label>
                                        <input class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600"
                                               type="number" id="halfboardMax" name="halfboardMax" min="0">
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <label for="currency" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Currency</label>
                                    <select name="currency" id="currency"
                                            class="form-select form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
                                        @foreach($currencies as $currency)
                                            <option value="{{ $currency->id }}" {{ $currency->id == $tour_package->currency ? 'selected' : '' }}>{{ $currency->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- Supplier file + hotel note --}}
                            <div class="space-y-3">
                                <input class="form-control block w-full rounded border border-slate-300 bg-white px-3 py-1.5 text-sm text-slate-700 shadow-subtle"
                                       type="file" id="formFileDisabled" name="supplier_file">
                                <textarea name="hotel_note" rows="4" placeholder="Note"
                                          class="form-control block w-full rounded border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600"></textarea>
                            </div>
                        </div>

                        {{-- RIGHT column --}}
                        <div class="space-y-5">

                            <div>
                                <h3 class="text-sm font-medium text-slate-700 mb-2">Other conditions</h3>
                                <textarea id="otherConditions" name="otherConditions" rows="4"
                                          class="form-control block w-full rounded border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600"></textarea>
                            </div>

                            <div class="rounded border border-slate-200 bg-slate-50 px-4 py-4 space-y-3">
                                <h3 class="text-sm font-medium text-slate-700">Cancellation policies</h3>
                                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                                    <div class="sm:col-span-3">
                                        <label for="cancellationDays" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Days before arrival</label>
                                        <input type="number" id="cancellationDays" min="0"
                                               class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
                                    </div>
                                    <div class="sm:col-span-3">
                                        <label for="cancellationPercentage" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Amount / %</label>
                                        <input type="number" id="cancellationPercentage" min="0"
                                               class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
                                    </div>
                                    <div class="sm:col-span-4">
                                        <label for="cancellationType" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Type</label>
                                        <select id="cancellationType"
                                                class="form-select form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
                                            <option value="percentage">Percentage</option>
                                            <option value="amount">Amount</option>
                                        </select>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <button type="button" id="addCancellation"
                                                class="inline-flex w-full h-9 items-center justify-center gap-1 rounded bg-primary-600 px-3 text-sm text-white hover:bg-primary-700">
                                            + Add
                                        </button>
                                    </div>
                                </div>
                                <p class="text-xs text-slate-500">…of rooms can be cancelled free of charge.</p>
                                <div id="cancellationRequirements" class="space-y-2 text-sm text-slate-700"></div>

                                <div>
                                    <label for="cancellationNote" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Additional cancellation policies</label>
                                    <textarea id="cancellationNote" name="cancellationNote" rows="4"
                                              class="form-control block w-full rounded border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600"></textarea>
                                </div>
                            </div>

                            {{-- Container populated by /tp/getaddEmails AJAX --}}
                            <div id="additionalEmails" class="space-y-2"></div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-200">
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 rounded bg-primary-600 px-5 h-10 text-sm font-medium text-white hover:bg-primary-700">
                            Submit offer
                        </button>
                    </div>
                </form>
            </div>
        </section>

        {{-- Hotel offers table --}}
        <section class="rounded-lg border border-slate-200 bg-white">
            <div class="border-b border-slate-200 px-5 sm:px-8 py-4">
                <h2 class="text-sm font-semibold text-slate-900">Hotel offers</h2>
            </div>
            <div class="px-5 sm:px-8 py-5">
                @php $offer = $tour_package->hotel_offers->last(); @endphp
                @if(empty($offer))
                    <p class="text-sm text-slate-500">No offers yet — please submit your offer above so we can match it to this booking.</p>
                @else
                    <div class="overflow-x-auto" style="max-height: 400px;">
                        <table id="offers-table" class="min-w-full divide-y divide-slate-200 text-sm" style="background:#fff; width: 100%; table-layout: auto;">
                            <thead class="bg-slate-50">
                                <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                                    <th class="px-3 py-2">ID</th>
                                    <th class="px-3 py-2" style="width: 104px;">{!! trans('Status') !!}</th>
                                    <th class="px-3 py-2" style="width: 104px;">{!! trans('Delete') !!}</th>
                                    @php $printedRoomNames = []; @endphp
                                    @foreach($selected_room_types as $selected_room_type)
                                        @if(!in_array($selected_room_type->name, $printedRoomNames))
                                            <th class="px-3 py-2 rooms-title">{{ $selected_room_type->name }}</th>
                                            @php $printedRoomNames[] = $selected_room_type->name; @endphp
                                        @endif
                                    @endforeach
                                    <th class="px-3 py-2">{!! trans('Currency') !!}</th>
                                    <th class="px-3 py-2">{!! trans('City Tax') !!}</th>
                                    <th class="px-3 py-2">{!! trans('Halfboard Supp p.p') !!}</th>
                                    <th class="px-3 py-2">{!! trans('foc') !!}</th>
                                    <th class="px-3 py-2">{!! trans('Max per group') !!}</th>
                                    <th class="px-3 py-2">{!! trans('Portrage pp') !!}</th>
                                    <th class="px-3 py-2">{!! trans('Hotel File') !!}</th>
                                    <th class="px-3 py-2">{!! trans('Hotel Note') !!}</th>
                                    <th class="px-3 py-2 text-right actions-button" style="width: 140px!important">{!! trans('main.Actions') !!}</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th class="not"></th>
                                    <th>{!! trans('Status') !!}</th>
                                    @php $printedRoomNames = []; @endphp
                                    @foreach($selected_room_types as $selected_room_type)
                                        @if(!in_array($selected_room_type->name, $printedRoomNames))
                                            <th class="rooms-title">{{ $selected_room_type->name }}</th>
                                            @php $printedRoomNames[] = $selected_room_type->name; @endphp
                                        @endif
                                    @endforeach
                                    <th>{!! trans('Currency') !!}</th>
                                    <th>{!! trans('City Tax') !!}</th>
                                    <th>{!! trans('Halfboard Supp p.p') !!}</th>
                                    <th>{!! trans('foc') !!}</th>
                                    <th>{!! trans('Max per group') !!}</th>
                                    <th>{!! trans('Portrage pp') !!}</th>
                                    <th>{!! trans('Hotel File') !!}</th>
                                    <th>{!! trans('Hotel Note') !!}</th>
                                    <th class="not"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endif
            </div>
        </section>

        {{-- Comments from suppliers --}}
        <section class="rounded-lg border border-slate-200 bg-white">
            <div class="border-b border-slate-200 px-5 sm:px-8 py-4">
                <h2 class="text-sm font-semibold text-slate-900">Comments from suppliers</h2>
            </div>
            <div class="px-5 sm:px-8 py-5 space-y-4">
                <ul id="comment_list" class="space-y-3 list-none m-0 p-0 text-sm">
                    @if(!empty($offer))
                        @foreach($comments as $comment)
                            @if($comment->supplier_name == $tour_package->name)
                                <li class="rounded border border-slate-200 bg-slate-50 px-4 py-3">
                                    <strong class="text-slate-900">{{ $comment->supplier_name ?? '' }}</strong>
                                    <span class="text-slate-500">said:</span>
                                    <p class="mt-1 text-slate-700">{{ $comment->content ?? '' }}</p>
                                </li>
                            @endif
                        @endforeach
                    @else
                        <li class="text-sm text-slate-500">No comments yet.</li>
                    @endif
                </ul>

                <form id="commentForm" method="POST" class="form-light space-y-3"
                      action="{{ route('add_comment', [$tour_package->id]) }}">
                    <input type="hidden" name="_token" value="{{ Session::token() }}">
                    <textarea name="comment" rows="4" placeholder="Write your comment"
                              class="form-control block w-full rounded border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600"></textarea>
                    <div class="flex justify-end">
                        <button class="inline-flex items-center gap-1.5 rounded bg-primary-600 px-4 h-9 text-sm text-white hover:bg-primary-700">Add comment</button>
                    </div>
                </form>
            </div>
        </section>

        {{-- TMS / supplier email timelines --}}
        @php
            $renderEmail = function ($email, $supplierReply = true) {
                $dateString = $email->header->date ?? '';
                $dateTime   = new DateTime($dateString);
                return [
                    'date'    => $dateTime->format('D m.Y'),
                    'time'    => $dateTime->format('H:i:s'),
                    'from'    => $email->header->from ?? '',
                    'subject' => $email->header->subject ?? '',
                    'html'    => $email->message->html ?? '',
                ];
            };
        @endphp

        <section class="rounded-lg border border-slate-200 bg-white">
            <div class="border-b border-slate-200 px-5 sm:px-8 py-4">
                <h2 class="text-sm font-semibold text-slate-900">Emails from TMS</h2>
            </div>
            <div class="px-5 sm:px-8 py-5">
                @if(!empty($tms_emails))
                    <ul class="timeline space-y-4 list-none p-0 m-0">
                        @foreach($tms_emails as $tmsemail)
                            @php $e = $renderEmail($tmsemail, false); @endphp
                            <li class="time-label">
                                <span class="inline-flex items-center rounded bg-danger-100 px-2 py-1 text-xs font-semibold text-danger-700">{{ $e['date'] }}</span>
                            </li>
                            <li class="rounded border border-slate-200 bg-slate-50">
                                <div class="flex items-center gap-2 px-4 py-2 border-b border-slate-200 bg-white">
                                    <span class="flex h-7 w-7 items-center justify-center rounded-full bg-info-100 text-info-700">
                                        <i class="fa fa-envelope"></i>
                                    </span>
                                    <span class="time inline-flex items-center gap-1 text-xs text-slate-500">
                                        <i class="fa fa-clock-o"></i> {{ $e['time'] }}
                                    </span>
                                </div>
                                <div class="timeline-item px-4 py-3">
                                    <h3 class="timeline-header text-sm text-slate-700">
                                        <a href="#" class="text-primary-600 hover:text-primary-700">{{ $e['from'] }}</a>
                                        <span class="text-slate-500"> sent email to supplier</span>
                                        <b class="block sm:inline mt-0.5 text-slate-900">: {{ $e['subject'] }}</b>
                                    </h3>
                                    <div class="timeline-body mt-3 text-sm text-slate-700 prose prose-sm max-w-none">
                                        {!! $e['html'] !!}
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-slate-500">No TMS messages on file yet.</p>
                @endif
            </div>
        </section>

        <section class="rounded-lg border border-slate-200 bg-white">
            <div class="border-b border-slate-200 px-5 sm:px-8 py-4">
                <h2 class="text-sm font-semibold text-slate-900">Emails from supplier</h2>
            </div>
            <div class="px-5 sm:px-8 py-5">
                @if(!empty($emails))
                    <ul class="timeline space-y-4 list-none p-0 m-0">
                        @foreach($emails as $email)
                            @php $e = $renderEmail($email, true); @endphp
                            <li class="time-label">
                                <span class="inline-flex items-center rounded bg-danger-100 px-2 py-1 text-xs font-semibold text-danger-700">{{ $e['date'] }}</span>
                            </li>
                            <li class="rounded border border-slate-200 bg-slate-50">
                                <div class="flex items-center gap-2 px-4 py-2 border-b border-slate-200 bg-white">
                                    <span class="flex h-7 w-7 items-center justify-center rounded-full bg-info-100 text-info-700">
                                        <i class="fa fa-envelope"></i>
                                    </span>
                                    <span class="time inline-flex items-center gap-1 text-xs text-slate-500">
                                        <i class="fa fa-clock-o"></i> {{ $e['time'] }}
                                    </span>
                                </div>
                                <div class="timeline-item px-4 py-3">
                                    <h3 class="timeline-header text-sm text-slate-700">
                                        <a href="#" class="text-primary-600 hover:text-primary-700">{{ $e['from'] }}</a>
                                        <span class="text-slate-500"> reply to your email</span>
                                        <b class="block sm:inline mt-0.5 text-slate-900">: {{ $e['subject'] }}</b>
                                    </h3>
                                    <div class="timeline-body mt-3 text-sm text-slate-700 prose prose-sm max-w-none">
                                        {!! $e['html'] !!}
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-slate-500">The supplier hasn't replied yet. Contact them for further inquiry, or check that a work email is configured in the TMS dashboard.</p>
                @endif
            </div>
        </section>

        <footer class="text-center text-xs text-slate-400 py-4">© {{ date('Y') }} eetstravel.com</footer>
    </div>

    {{-- Reply modal (legacy #exampleModal) — kept for any inline reply buttons --}}
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content rounded border border-slate-200 bg-white shadow-lg">
                <div class="modal-header border-b border-slate-200 px-5 py-3 flex items-center justify-between">
                    <h5 class="modal-title text-sm font-medium text-slate-700" id="exampleModalLabel">New message</h5>
                    <button type="button" class="btn-close text-slate-400 hover:text-slate-600" data-bs-dismiss="modal" aria-label="Close">&times;</button>
                </div>
                <div class="modal-body px-5 py-4">
                    <form>
                        <label for="message-text" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Message</label>
                        <textarea id="message-text" rows="6"
                                  class="form-control block w-full rounded border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600"></textarea>
                    </form>
                </div>
                <div class="modal-footer border-t border-slate-200 bg-slate-50 px-5 py-3 flex items-center justify-end gap-2">
                    <button type="button" data-bs-dismiss="modal"
                            class="inline-flex items-center gap-1.5 rounded border border-slate-300 bg-white px-3 h-9 text-sm text-slate-700 hover:bg-slate-50">Close</button>
                    <button type="button"
                            class="inline-flex items-center gap-1.5 rounded bg-primary-600 px-4 h-9 text-sm text-white hover:bg-primary-700">Send message</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Confirm-delete modal (#confirmDeleteModal / #confirmDelete) --}}
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content rounded border border-slate-200 bg-white shadow-lg">
                <div class="modal-header border-b border-slate-200 px-5 py-3 flex items-center justify-between">
                    <h5 class="modal-title text-sm font-medium text-slate-700" id="confirmDeleteModalLabel">Confirm delete</h5>
                    <button type="button" class="btn-close text-slate-400 hover:text-slate-600" data-bs-dismiss="modal" aria-label="Close">&times;</button>
                </div>
                <div class="modal-body px-5 py-4 text-sm text-slate-700">Are you sure you want to delete this record?</div>
                <div class="modal-footer border-t border-slate-200 bg-slate-50 px-5 py-3 flex items-center justify-end gap-2">
                    <button type="button" data-bs-dismiss="modal"
                            class="inline-flex items-center gap-1.5 rounded border border-slate-300 bg-white px-3 h-9 text-sm text-slate-700 hover:bg-slate-50">Close</button>
                    <button type="button" id="confirmDelete"
                            class="inline-flex items-center gap-1.5 rounded bg-danger-600 px-4 h-9 text-sm text-white hover:bg-danger-700">Delete</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Scripts (loaded in body — unchanged from legacy except cleaned up). --}}
    <script src="{{ asset('js/lib/jquery.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tagify/4.17.9/tagify.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap-tables.js') }}"></script>

    <script>
        const input = document.querySelector('#tags-input');
        const tagify = new Tagify(input, {
            duplicates: false,
            whitelist: [{
                value: '{{ $tour_package->service()->work_email }}',
                readonly: true
            }],
        });

        $("#status").change(function () {
            let val = $(this).val();
            if (val === "Offered with Option") {
                $("#option_with_date").show();
                $("#option_with_date input").prop("required", true);
            } else {
                $("#option_with_date").hide();
                $("#option_with_date input").prop("required", false);
            }
        });

        $('#commentForm').on('submit', function (e) {
            e.preventDefault();
            var formData = $(this).serialize();
            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: formData,
                success: function (response) { $("#comment_list").append(response); },
                error: function (xhr) {}
            });
        });

        document.getElementById('addCancellation').addEventListener('click', function () {
            const daysBeforeArrival = document.getElementById('cancellationDays').value;
            const percentageOrAmount = document.getElementById('cancellationPercentage').value;
            const cancellationType = document.getElementById('cancellationType').value;

            if (daysBeforeArrival && percentageOrAmount) {
                const daysInput = document.createElement('input');
                daysInput.type = 'hidden';
                daysInput.name = `cancellation_days[]`;
                daysInput.value = percentageOrAmount;

                const amountInput = document.createElement('input');
                amountInput.type = 'hidden';
                amountInput.name = `cancellation_percentage[]`;
                amountInput.value = daysBeforeArrival;

                const cancellationTypeInput = document.createElement('input');
                cancellationTypeInput.type = 'hidden';
                cancellationTypeInput.name = `cancellation_type[]`;
                cancellationTypeInput.value = cancellationType;

                const form = document.getElementById('hoteloffers_add_form');
                form.appendChild(daysInput);
                form.appendChild(amountInput);
                form.appendChild(cancellationTypeInput);

                const cancellationRequirements = document.getElementById('cancellationRequirements');
                const newRequirement = document.createElement('div');
                newRequirement.className = 'rounded border border-slate-200 bg-white px-3 py-2';
                newRequirement.innerHTML = `<p class="m-0 text-sm text-slate-700">${daysBeforeArrival} days before arrival: ${percentageOrAmount} ${cancellationType} can be cancelled free of charge</p>`;
                cancellationRequirements.appendChild(newRequirement);
            }
        });

        // Initial fetch of any saved additional-email rows
        invoice_items();
        function invoice_items() {
            let package_id = $("#package_id").val();
            $.ajax({
                url: '/tp/getaddEmails',
                method: 'GET',
                data: { package_id: package_id }
            }).done((res) => {
                $('#additionalEmails').append(res);
                $('input[name="_token"]').each(function () { $(this).val("{{ csrf_token() }}"); });
            });
        }

        $(document).on('click', '#delete_contact_item', function () {
            $(this).closest('.item-contact').remove();
        });

        $(document).ready(function () {
            if (!$.fn.DataTable) return;
            let table = $('#offers-table').DataTable({
                dom: "<'row'<'col-sm-5'l><'col-sm-2'B><'col-sm-5'f>>" +
                     "<'row'<'col-sm-12'tr>>" +
                     "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                buttons: [
                    { extend: 'csv',      title: 'Current Offers List', exportOptions: { columns: ':not(.actions-button)' } },
                    { extend: 'excel',    title: 'Offers List',         exportOptions: { columns: ':not(.actions-button)' } },
                    { extend: 'pdfHtml5', title: 'Offer List', orientation: 'landscape', exportOptions: { columns: ':not(.actions-button)' } }
                ],
                processing: true,
                serverSide: true,
                pageLength: 50,
                ajax: { url: "{{ route('offers_data', [$tour_package->id, 1]) }}" },
                columns: [
                    { data: 'id',              name: 'id' },
                    { data: 'status',          name: 'status' },
                    { data: 'supplier_delete', name: 'supplier_delete' },
                    @foreach($selected_room_types as $selected_room_type)
                        { "data": "{{ $selected_room_type->code }}" },
                    @endforeach
                    { data: 'currency',            name: 'currency' },
                    { data: 'city_tax',            name: 'city_tax' },
                    { data: 'halfboard',           name: 'halfboard' },
                    { data: 'foc_after_every_pax', name: 'foc_after_every_pax' },
                    { data: 'halfboardMax',        name: 'halfboardMax' },
                    { data: 'portrage_perperson',  name: 'portrage_perperson' },
                    { data: 'hotel_file',          name: 'hotel_file' },
                    { data: 'hotel_note',          name: 'hotel_note' },
                    { data: 'action',              name: 'action', searchable: false, sorting: false, orderable: false }
                ],
                "fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                    if (aData.supplier_delete == 1) { $(nRow).css('background', '#ffbbb2'); }
                }
            });
            $('#offers-table tfoot th').each(function () {
                let column = this;
                if (column.className !== 'not') {
                    let title = $(this).text();
                    $(this).html('<input type="text" class="form-control block w-full h-8 rounded border border-slate-300 bg-white px-2 text-sm" placeholder="Search ' + title + '" />');
                }
            });
            table.columns().every(function () {
                let that = this;
                $('input', this.footer()).on('keyup change', function () {
                    if (that.search() !== this.value) { that.search(this.value).draw(); }
                });
            });
            $('#offers-table tfoot th').appendTo('#offers-table thead');
        });

        $(document).ready(function () {
            var recordToDeleteId;
            setTimeout(function () {
                $('.delete').on('click', function () {
                    recordToDeleteId = $(this).data('link');
                    $('#confirmDeleteModal').modal('show');
                });

                $('#confirmDelete').on('click', function () {
                    $.ajax({
                        url: '' + recordToDeleteId,
                        type: 'get',
                        success: function (data) {
                            if (data.success) {
                                $('tr[data-id="' + recordToDeleteId + '"]').remove();
                            } else {
                                alert('Failed to delete record.');
                            }
                        },
                        error: function () { alert('Error occurred during deletion.'); },
                        complete: function () { $('#confirmDeleteModal').modal('hide'); }
                    });
                });
            }, 3000);
        });

        function myFunction(val, val2) {
            let x = document.getElementById(val2);
            x.innerHTML = val / {{ $tour_package->pax }};
        }
    </script>
</body>
</html>
