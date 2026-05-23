{{--
    <x-ui.table /> — Static styled table (use for short, server-paginated data).

    For larger / sortable / searchable data sets, use <x-ui.data-table />.

    Props
    -----
    striped (bool)  Apply zebra striping. Off by default — strips clutter the
                    eye on narrow tables. Turn on for >5 columns.
    compact (bool)  Tighter row padding (32px vs 48px).

    Slots
    -----
    head            <thead> contents (one or more <tr>).
    default         <tbody> contents (one or more <tr>).
    foot            <tfoot> contents (totals row, etc.). Optional.

    Conventions
    -----------
    * Use <th class="px-3 py-2 text-left text-xs font-medium text-slate-500 uppercase tracking-wide">
      for header cells, or the helper component <x-ui.th>.
    * Use <td class="px-3 py-2 text-sm text-slate-700"> for body cells, or
      <x-ui.td>.

    Example
    -------
        <x-ui.table>
            <x-slot name="head">
                <tr>
                    <x-ui.th>Name</x-ui.th>
                    <x-ui.th>Dates</x-ui.th>
                    <x-ui.th align="right">Pax</x-ui.th>
                </tr>
            </x-slot>
            @foreach($tours as $tour)
                <tr class="hover:bg-slate-50">
                    <x-ui.td>{{ $tour->name }}</x-ui.td>
                    <x-ui.td class="text-slate-500">{{ $tour->departure_date }}</x-ui.td>
                    <x-ui.td align="right">{{ $tour->pax }}</x-ui.td>
                </tr>
            @endforeach
        </x-ui.table>
--}}

@props([
    'striped' => false,
    'compact' => false,
])

@php
    $rowPad = $compact ? '[&_td]:py-2' : '[&_td]:py-3';
@endphp

<div class="overflow-x-auto rounded border border-slate-200">
    <table {{ $attributes->class('min-w-full divide-y divide-slate-200 bg-white ' . $rowPad . ' ' . ($striped ? '[&_tbody_tr:nth-child(odd)]:bg-slate-50/50' : '')) }}>
        @if(isset($head))
            <thead class="bg-slate-50 border-b border-slate-200">
                {{ $head }}
            </thead>
        @endif
        <tbody class="divide-y divide-slate-100">
            {{ $slot }}
        </tbody>
        @if(isset($foot))
            <tfoot class="bg-slate-50 border-t border-slate-200">
                {{ $foot }}
            </tfoot>
        @endif
    </table>
</div>
