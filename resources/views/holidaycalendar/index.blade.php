@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Holidays')
@section('content')
@include('layouts.title', ['title' => 'Holidays', 'sub_title' => 'Holidays List', 'breadcrumbs' => [
    ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
    ['title' => 'Holidays', 'icon' => null, 'route' => null]
]])
<div class="card">
    <div class="card-body">
        <div class="d-flex flex-wrap align-items-end gap-2 mb-3">
            {!! \App\Helper\PermissionHelper::getCreateButton(route('holiday.create'), \App\Holidaycalendarday::class) !!}
            <div>
                <label class="form-label" for="holiday-search">Search holidays</label>
                <input type="search" id="holiday-search" class="form-control" oninput="filterTable('holidaycalendar-table', this.value)">
            </div>
        </div>
        <div class="alert alert-danger" id="errors_message" role="alert" style="display:none"></div>
        <div class="table-responsive">
            <table id="holidaycalendar-table" class="table table-striped table-bordered table-hover">
                <thead><tr><th>ID</th><th>Name</th><th>Date</th><th>Color</th><th class="actions-button">Actions</th></tr></thead>
                <tbody>
                    @foreach($holidaycalendarday as $holiday)
                        <tr data-holiday-id="{{ $holiday->id }}">
                            <td>{{ $holiday->id }}</td>
                            <td>{{ $holiday->name }}</td>
                            <td>{{ $holiday->start_time ? \Carbon\Carbon::parse($holiday->start_time)->format('Y-m-d') : '—' }}</td>
                            <td>{{ $holiday->backgroundcolor ?: '—' }}</td>
                            <td>{!! $holiday->action_buttons !!}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
