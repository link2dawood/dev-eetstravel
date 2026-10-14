@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Supplier reports')
@section('content')
<div class="container-xl">
    <div class="page-header mb-3"><h2 class="page-title">Supplier reports</h2></div>
    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-5"><label for="report-search" class="form-label">Supplier name</label><input id="report-search" name="q" value="{{ request('q') }}" class="form-control"></div>
        <div class="col-md-4"><label for="report-service" class="form-label">Supplier type</label><select id="report-service" name="service" class="form-select"><option value="">All</option>@foreach($options as $option)<option value="{{ $option }}" {{ request('service') === $option ? 'selected' : '' }}>{{ $option }}</option>@endforeach</select></div>
        <div class="col-md-3 align-self-end"><button class="btn btn-primary" type="submit">Filter</button><a class="btn btn-secondary" href="{{ route('reporting.index') }}">Clear</a></div>
    </form>
    <div class="card"><div class="card-header d-flex flex-wrap justify-content-between gap-2"><h3 class="card-title">{{ $servicesData->total() }} suppliers</h3><button type="button" class="btn btn-outline-secondary" onclick="exportTableToCSV('search-table', 'supplier_reports.csv')">Export this page as CSV</button></div>
        <div class="table-responsive"><table id="search-table" class="table table-striped table-bordered">
            <thead><tr><th>Name</th><th>Address</th><th>Country</th><th>City</th><th>Phone</th><th class="actions-button">Actions</th></tr></thead>
            <tbody>@forelse($servicesData as $service)
                <tr><td>{{ $service->nameService }}</td><td>{{ $service->address_first ?? '' }}</td><td>{{ $service->country }}</td><td>{{ $service->city }}</td><td>{{ $service->work_phone ?? '' }}</td><td><a class="btn btn-primary btn-sm" href="{{ $service->show_link }}" aria-label="View report for {{ $service->name }}">View report</a></td></tr>
            @empty<tr><td colspan="6" class="text-center text-muted">No services found</td></tr>@endforelse</tbody>
        </table></div>
    </div>
    <div class="mt-3">{{ $servicesData->links('pagination::bootstrap-4') }}</div>
</div>
@endsection
