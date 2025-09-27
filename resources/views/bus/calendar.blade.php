@extends('scaffold-interface.layouts.app')
@section('title','Index')
@section('content')
    @include('layouts.title',
           ['title' => 'Buses', 'sub_title' => 'Buses Calendar',
           'breadcrumbs' => [
           ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
           ['title' => 'Bus', 'icon' => 'bus', 'route' => null]]])
		   
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

    <section class="content">
            <div class="box-body">
                @include('component.modal_add_bus')
                @include('scaffold-interface.dashboard.components.bus_calendar')
            </div>
    </section>
@endsection


