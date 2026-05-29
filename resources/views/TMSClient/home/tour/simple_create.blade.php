@extends('TMSClient.layouts.app')
@section('title', 'Simple tour — TMS Client')

@section('content')
<section class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <a href="{{ url('TMS-Client/home') }}" class="inline-flex items-center gap-1 text-sm text-slate-600 hover:text-slate-900">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><line x1="19" x2="5" y1="12" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Back to tours
        </a>
        <h1 class="mt-2 text-2xl font-semibold text-slate-900">Create a simple tour</h1>
        <p class="mt-1 text-sm text-slate-500">Quick request — just the basics. We'll follow up for any details we need.</p>
    </div>

    <form id="tourCreateForm" action="{{ route('TMS-Client-tours.store') }}" method="POST" class="tab-wizard wizard-circle wizard clearfix space-y-4">
        {{ csrf_field() }}

        <h6 class="hidden">Basic Info</h6>
        <formstep>
            <div class="rounded-lg border border-slate-200 bg-white p-6 space-y-4">
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
                </div>
            </div>
        </formstep>

        <h6 class="hidden">Create Tour</h6>
        <formstep>
            <div class="rounded-lg border border-slate-200 bg-white p-6 space-y-4">
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

                {{-- room_types hidden picker preserved (rooms.js binds to .list_room_types + .select_room_type) --}}
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
