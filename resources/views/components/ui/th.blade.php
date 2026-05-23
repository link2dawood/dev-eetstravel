{{--
    <x-ui.th /> — Standard table header cell. Use inside <x-ui.table /> <slot:head>.

    Props
    -----
    align (string)   "left" (default) | "right" (for numeric columns) | "center"
    sort  (string)   Field name to sort by. If set, renders a sort button.
    sortDir (string) Current sort direction for this column: "asc" | "desc" | null.
    sortUrl (string) URL that toggles sort for this column on click.
--}}

@props([
    'align' => 'left',
    'sort' => null,
    'sortDir' => null,
    'sortUrl' => null,
])

@php
    $alignClass = [
        'left' => 'text-left',
        'right' => 'text-right',
        'center' => 'text-center',
    ][$align] ?? 'text-left';
@endphp

<th {{ $attributes->class('px-3 py-2 text-xs font-medium text-slate-500 uppercase tracking-wide ' . $alignClass) }}>
    @if($sort && $sortUrl)
        <a href="{{ $sortUrl }}" class="inline-flex items-center gap-1 hover:text-slate-700">
            <span>{{ $slot }}</span>
            @if($sortDir === 'asc')
                <x-ui.icon name="arrow-up" size="xs" />
            @elseif($sortDir === 'desc')
                <x-ui.icon name="arrow-down" size="xs" />
            @else
                <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-300" />
            @endif
        </a>
    @else
        {{ $slot }}
    @endif
</th>
