@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Guest Lists')
@section('content')
@include('layouts.title', ['title' => 'Guest Lists', 'sub_title' => 'Guest Lists', 'breadcrumbs' => [
    ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
    ['title' => 'Guest Lists', 'icon' => 'list-ul', 'route' => null]
]])
<div class="card">
    <div class="card-body">
        <div class="d-flex flex-wrap align-items-end gap-2 mb-3">
            <div>
                <label for="guest-list-search" class="form-label">Search guest lists</label>
                <input type="search" id="guest-list-search" class="form-control" oninput="filterTable('guest-list-table', this.value)">
            </div>
            <button type="button" class="btn btn-success" onclick="exportTableToCSV('guest-list-table', 'guest_lists_export.csv')">Export CSV</button>
        </div>
        <div class="table-responsive">
            <table id="guest-list-table" class="table table-striped table-bordered table-hover">
                <thead><tr>
                    <th>ID</th><th>Name</th><th>Tour</th><th>Author</th><th>Created</th><th class="actions-button">Actions</th>
                </tr></thead>
                <tbody>
                    @foreach($guestList as $item)
                        <tr data-guest-list-id="{{ $item->id }}">
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->tour ? $item->tour->name : 'Tour unavailable' }}</td>
                            <td>{{ $item->author_name ?: 'Author unavailable' }}</td>
                            <td>{{ $item->created_at }}</td>
                            <td><a class="btn btn-sm btn-primary" href="{{ route('guestList.showbyid', ['id' => $item->id]) }}" target="_blank" rel="noopener">View guest list</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
