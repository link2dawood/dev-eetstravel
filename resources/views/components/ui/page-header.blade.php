{{--
    <x-ui.page-header /> — Title block at the top of every page.

    Props
    -----
    title       (required string)
    description (string)         Sub-text under title.
    breadcrumbs (array)          [['label' => 'Home', 'href' => url('/')], ['label' => 'Tours']]
                                 The last entry is the current page (no link rendered).

    Slot
    ----
    actions     Right-aligned buttons / dropdowns.

    Example
    -------
        <x-ui.page-header
            title="Tours"
            description="All confirmed and draft tours."
            :breadcrumbs="[
                ['label' => 'Home', 'href' => url('/home')],
                ['label' => 'Tours'],
            ]"
        >
            <x-slot name="actions">
                <x-ui.button as="a" href="{{ route('tour.create') }}" icon="plus">New tour</x-ui.button>
            </x-slot>
        </x-ui.page-header>
--}}

@props([
    'title',
    'description' => null,
    'breadcrumbs' => [],
])

<header class="mb-6">
    @if(!empty($breadcrumbs))
        <nav aria-label="Breadcrumb" class="mb-2">
            <ol class="flex items-center gap-1 text-xs text-slate-500">
                @foreach($breadcrumbs as $i => $crumb)
                    <li class="flex items-center gap-1">
                        @if(isset($crumb['href']) && $i < count($breadcrumbs) - 1)
                            <a href="{{ $crumb['href'] }}" class="hover:text-slate-700">{{ $crumb['label'] }}</a>
                        @else
                            <span @if($i === count($breadcrumbs) - 1) class="text-slate-700" aria-current="page" @endif>{{ $crumb['label'] }}</span>
                        @endif
                        @if($i < count($breadcrumbs) - 1)
                            <x-ui.icon name="chevron-right" size="xs" class="text-slate-300" />
                        @endif
                    </li>
                @endforeach
            </ol>
        </nav>
    @endif

    <div class="flex items-start gap-4">
        <div class="flex-1 min-w-0">
            <h1 class="text-xl font-semibold text-slate-900 truncate">{{ $title }}</h1>
            @if($description)
                <p class="mt-1 text-sm text-slate-500">{{ $description }}</p>
            @endif
        </div>

        @if(isset($actions))
            <div class="flex items-center gap-2 shrink-0">{{ $actions }}</div>
        @endif
    </div>
</header>
