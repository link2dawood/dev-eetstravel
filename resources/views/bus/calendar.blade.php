@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Buses Calendar')

@section('content')
<x-ui.page-header
    title="Buses Calendar"
    description="Fleet availability and scheduling."
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Buses', 'href' => route('bus.index')],
        ['label' => 'Calendar'],
    ]"
>
    <x-slot name="actions">
        <x-ui.button as="a" href="{{ route('bus.index') }}" variant="ghost" icon="arrow-left">
            {{ trans('main.Back') ?? 'Back' }}
        </x-ui.button>
    </x-slot>
</x-ui.page-header>

{{-- amCharts / Gantt library scripts — kept verbatim --}}
<script type="text/javascript" src="{{asset('js/lib/amcharts.js')}}"></script>
<script type="text/javascript" src="{{asset('js/lib/serial.js')}}"></script>
<script type="text/javascript" src="{{asset('js/lib/gantt.js')}}"></script>
<script type="text/javascript" src="{{asset('js/lib/themes/light.js')}}"></script>
<script type="text/javascript" src="{{asset('js/lib/themes/dark.js')}}"></script>
<script type="text/javascript" src="{{asset('js/lib/themes/black.js')}}"></script>
<script type="text/javascript" src="{{asset('js/lib/themes/patterns.js')}}"></script>
<script type="text/javascript" src="{{asset('js/lib/plugins/dataloader/dataloader.min.js')}}"></script>
<script src="https://www.amcharts.com/lib/3/plugins/export/export.min.js"></script>
<script type="text/javascript" src="{{asset('js/lib/chart_bus.js')}}"></script>

{{-- Calendar card wrapper --}}
<div class="rounded border border-slate-200 bg-white">
    {{-- Calendar widget + modals — component kept completely intact --}}
    @include('component.modal_add_bus')
    @include('scaffold-interface.dashboard.components.bus_calendar')
</div>

@endsection
