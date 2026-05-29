{{--
    <x-ui.td /> — Standard table body cell.

    Props
    -----
    align (string)  "left" (default) | "right" | "center"
--}}

@props(['align' => 'left'])

@php
    $alignClass = [
        'left' => 'text-left',
        'right' => 'text-right',
        'center' => 'text-center',
    ][$align] ?? 'text-left';
@endphp

<td {{ $attributes->class('px-3 text-sm text-slate-700 ' . $alignClass) }}>{{ $slot }}</td>
