@extends('TMSClient.layouts.app')
@section('title', 'New tour — TMS Client')

@section('content')
<section class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <a href="{{ url('TMS-Client/home') }}" class="inline-flex items-center gap-1 text-sm text-slate-600 hover:text-slate-900">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><line x1="19" x2="5" y1="12" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Back to tours
        </a>
        <h1 class="mt-2 text-2xl font-semibold text-slate-900">Request a new tour</h1>
        <p class="mt-1 text-sm text-slate-500">Tell us about your trip and we'll put together a quote.</p>
    </div>

    <form id="tourCreateForm" action="{{ route('TMS-Client-tours.store') }}" method="POST" class="tab-wizard wizard-circle wizard clearfix space-y-4">
        {{ csrf_field() }}

        <h6 class="hidden">Basic Info</h6>
        <formstep>
            <div class="rounded-lg border border-slate-200 bg-white p-6 space-y-4">
                <h2 class="text-sm font-semibold text-slate-700">Basic info</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Tour name</label>
                        <input type="text" name="name" placeholder="e.g. Spring Tour 2026"
                               class="form-control block w-full h-10 rounded-md border border-slate-300 bg-white px-3 text-sm text-slate-900 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Pax</label>
                        <input type="number" name="pax" placeholder="0"
                               class="form-control block w-full h-10 rounded-md border border-slate-300 bg-white px-3 text-sm text-slate-900 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Pax free</label>
                        <input type="number" name="pax_free" placeholder="0"
                               class="form-control block w-full h-10 rounded-md border border-slate-300 bg-white px-3 text-sm text-slate-900 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                    </div>
                </div>
            </div>
        </formstep>

        <h6 class="hidden">Tour Basic</h6>
        <formstep>
            <div class="rounded-lg border border-slate-200 bg-white p-6 space-y-4">
                <h2 class="text-sm font-semibold text-slate-700">Travel route</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Country from</label>
                        <select name="country_begin" id="country_begin"
                                class="form-select form-control block w-full h-10 rounded-md border border-slate-300 bg-white px-3 text-sm text-slate-900 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
                            @foreach($countries as $country)
                                <option value="{{ $country->alias }}" {{ $country->name === 'United States' ? 'selected' : '' }}>{{ $country->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">City from</label>
                        <select name="city_begin" id="city_begin"
                                class="form-select form-control block w-full h-10 rounded-md border border-slate-300 bg-white px-3 text-sm text-slate-900 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}" {{ $city->name === 'Dallas' ? 'selected' : '' }}>{{ $city->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Country to</label>
                        <select name="country_end" id="country_end"
                                class="form-select form-control block w-full h-10 rounded-md border border-slate-300 bg-white px-3 text-sm text-slate-900 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
                            @foreach($countries as $country)
                                <option value="{{ $country->alias }}" {{ $country->name === 'United States' ? 'selected' : '' }}>{{ $country->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">City to</label>
                        <select name="city_end" id="city_end"
                                class="form-select form-control block w-full h-10 rounded-md border border-slate-300 bg-white px-3 text-sm text-slate-900 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
                            @foreach($cities as $city)
                                <option>{{ $city->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Best contact for us</label>
                    <input type="text" name="phone" placeholder="Phone, email, or WhatsApp"
                           class="form-control block w-full h-10 rounded-md border border-slate-300 bg-white px-3 text-sm text-slate-900 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                </div>
            </div>
        </formstep>

        <h6 class="hidden">Create Tour</h6>
        <formstep>
            <div class="rounded-lg border border-slate-200 bg-white p-6 space-y-4">
                <h2 class="text-sm font-semibold text-slate-700">Dates</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Departure date</label>
                        <input type="date" id="tourFrom" name="departure_date"
                               class="form-control block w-full h-10 rounded-md border border-slate-300 bg-white px-3 text-sm text-slate-900 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Return date</label>
                        <input type="date" id="tourTo" name="retirement_date"
                               class="form-control block w-full h-10 rounded-md border border-slate-300 bg-white px-3 text-sm text-slate-900 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                    </div>
                </div>

                {{-- room_types hidden picker preserved --}}
                <ul class="list_room_types hidden">
                    <ul class="list_room_types hidden">
                        @foreach($room_types as $room_type)
                            <li class="select_room_type">
                                <label>{{ $room_type->name }}</label>
                                <input type="text" data-info="{{ $room_type->id }}" hidden value="{{ $room_type }}">
                            </li>
                        @endforeach
                    </ul>
                </ul>
            </div>
        </formstep>
    </form>
</section>
@endsection

@push('scripts')
<script type="text/javascript" src='{{ asset('js/rooms.js') }}'></script>
@endpush
