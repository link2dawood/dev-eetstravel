@extends('TMSSupplier.layouts.app')
@section('title', 'Your offers — TMS Supplier')

@section('content')
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Page header --}}
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Your offers</h1>
            <p class="mt-1 text-sm text-slate-500">Tour packages assigned to you, with current status and payment progress.</p>
        </div>
        @if(!empty($offers))
            <div class="inline-flex items-center gap-2 rounded-md border border-slate-200 bg-white px-3 h-9 text-sm text-slate-600">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 text-slate-400"><path d="M9 14l2 2 4-4"/><rect x="3" y="4" width="18" height="18" rx="2"/></svg>
                {{ count($offers) }} {{ count($offers) === 1 ? 'offer' : 'offers' }}
            </div>
        @endif
    </div>

    {{-- Empty state --}}
    @if(empty($offers))
        <div class="rounded-lg border border-slate-200 bg-white px-6 py-16 text-center">
            <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            </div>
            <h2 class="text-base font-medium text-slate-900">No offers yet</h2>
            <p class="mt-1 text-sm text-slate-500">When the operations team sends you an offer, it'll show up here.</p>
        </div>
    @else
        {{-- Mobile: card list --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 lg:hidden">
            @foreach($offers as $offer)
                <div class="rounded-lg border border-slate-200 bg-white p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="text-xs font-mono text-slate-400">#{{ $offer->id }}</div>
                            <div class="mt-0.5 truncate text-sm font-medium text-slate-900">{{ $offer->tourName ?? 'Untitled tour' }}</div>
                            @if(!empty($offer->reference))
                                <div class="mt-0.5 text-xs text-slate-500 truncate">Ref: {{ $offer->reference }}</div>
                            @endif
                        </div>
                        @if(!empty($offer->statusName))
                            <span class="shrink-0 inline-flex items-center rounded bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-700">{{ $offer->statusName }}</span>
                        @endif
                    </div>
                    <dl class="mt-3 grid grid-cols-2 gap-x-3 gap-y-2 text-xs">
                        <div>
                            <dt class="font-medium uppercase tracking-wide text-slate-400">Expired</dt>
                            <dd class="mt-0.5">
                                @if($offer->is_expired)
                                    <span class="inline-flex items-center rounded bg-warning-100 px-1.5 py-0.5 text-warning-700">Yes</span>
                                @else
                                    <span class="inline-flex items-center rounded bg-slate-100 px-1.5 py-0.5 text-slate-600">No</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="font-medium uppercase tracking-wide text-slate-400">Paid</dt>
                            <dd class="mt-0.5">
                                @if($offer->paid)
                                    <span class="inline-flex items-center rounded bg-success-100 px-1.5 py-0.5 text-success-700">Yes</span>
                                @else
                                    <span class="inline-flex items-center rounded bg-slate-100 px-1.5 py-0.5 text-slate-600">No</span>
                                @endif
                            </dd>
                        </div>
                        <div class="col-span-2">
                            <dt class="font-medium uppercase tracking-wide text-slate-400">Total amount</dt>
                            <dd class="mt-0.5 font-mono text-slate-800">{{ $offer->total_amount ?? '—' }}</dd>
                        </div>
                    </dl>
                    @if(!empty($offer->supplier_url))
                        <a href="{{ $offer->supplier_url }}"
                           class="mt-3 inline-flex w-full items-center justify-center gap-1.5 rounded-md bg-primary-600 px-3 h-9 text-sm text-white hover:bg-primary-700">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            View offer
                        </a>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- Desktop: table --}}
        <div class="hidden lg:block rounded-lg border border-slate-200 bg-white">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50">
                        <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                            <th class="px-4 py-3">ID</th>
                            <th class="px-4 py-3">{!! trans('Tour Name') !!}</th>
                            <th class="px-4 py-3">{!! trans('Offer') !!}</th>
                            <th class="px-4 py-3">{!! trans('Paid') !!}</th>
                            <th class="px-4 py-3">{!! trans('Total Amount') !!}</th>
                            <th class="px-4 py-3">{!! trans('Status') !!}</th>
                            <th class="px-4 py-3">{!! trans('Reference') !!}</th>
                            <th class="px-4 py-3 text-right" style="width:140px">{!! trans('main.Actions') !!}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($offers as $offer)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 font-mono text-xs text-slate-500">#{{ $offer->id }}</td>
                                <td class="px-4 py-3 font-medium text-slate-900">{{ $offer->tourName ?? '' }}</td>
                                <td class="px-4 py-3">
                                    @if($offer->is_expired)
                                        <span class="inline-flex items-center rounded bg-warning-100 px-2 py-0.5 text-xs font-medium text-warning-700">Yes</span>
                                    @else
                                        <span class="inline-flex items-center rounded bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600">No</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($offer->paid)
                                        <span class="inline-flex items-center rounded bg-success-100 px-2 py-0.5 text-xs font-medium text-success-700">Yes</span>
                                    @else
                                        <span class="inline-flex items-center rounded bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600">No</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 font-mono text-slate-800">{{ $offer->total_amount ?? '—' }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $offer->statusName ?? '' }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $offer->reference ?? '' }}</td>
                                <td class="px-4 py-3">
                                    @if(!empty($offer->supplier_url))
                                        <div class="flex items-center justify-end">
                                            <a href="{{ $offer->supplier_url }}"
                                               class="inline-flex h-8 w-8 items-center justify-center rounded border border-slate-300 bg-white text-primary-600 hover:bg-primary-50"
                                               title="View offer">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                            </a>
                                        </div>
                                    @else
                                        <span class="block text-right text-xs text-slate-400">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</section>
@endsection
