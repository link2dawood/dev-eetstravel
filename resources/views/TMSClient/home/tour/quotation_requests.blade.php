@extends('TMSClient.layouts.app')
@section('title', 'Your Requests — TMS Client')

@section('content')
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between flex-wrap gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Your requests</h1>
            <p class="mt-1 text-sm text-slate-500">Quotations you've submitted to your travel partner.</p>
        </div>
        <a href="{{ url('TMS-Client-tours/create') }}"
           class="inline-flex h-9 items-center gap-2 rounded-md bg-primary-600 px-3 text-sm font-medium text-white hover:bg-primary-700">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
            Add new
        </a>
    </div>

    <div class="rounded-lg border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table id="tour-table" class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                        <th class="px-4 py-3">ID</th>
                        <th class="px-4 py-3">{!! trans('main.Name') !!}</th>
                        <th class="px-4 py-3">{!! trans('main.Depdate') !!}</th>
                        <th class="px-4 py-3">{!! trans('main.Status') !!}</th>
                        <th class="px-4 py-3">{!! trans('main.ExternalName') !!}</th>
                        <th class="actions-button px-4 py-3 text-right">{!! trans('main.Actions') !!}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($toursData as $tour)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-mono text-xs text-slate-500">#{{ $tour->id }}</td>
                            <td class="px-4 py-3">
                                <div class="font-semibold text-slate-900">{{ $tour->name }}</div>
                                <p class="text-xs text-slate-500 mt-0.5">{{ $tour->formatted_departure_date }}{{ $tour->retirement_date ? ' — ' . \Carbon\Carbon::parse($tour->retirement_date)->format('Y-m-d') : '' }}</p>
                            </td>
                            <td class="px-4 py-3 text-slate-700">{{ $tour->pax }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded px-2 py-0.5 text-xs font-medium
                                    @if($tour->status_name === 'Requested') bg-warning-50 text-warning-700
                                    @elseif($tour->status_name === 'Cancelled') bg-danger-50 text-danger-700
                                    @elseif($tour->status_name === 'Confirmed' || $tour->status_name === 'Active') bg-success-50 text-success-700
                                    @else bg-slate-100 text-slate-700
                                    @endif">
                                    {{ $tour->status_name }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-700">{{ $tour->external_name }}</td>
                            <td class="px-4 py-3 text-right">{!! $tour->action_buttons !!}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-12 text-center text-sm text-slate-500">No quotation requests found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        console.log('TMSClient Tour Quotation Requests table loaded with direct controller data');
    });
</script>
@endpush
