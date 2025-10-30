@extends('scaffold-interface.layouts.tabler-app')
@section('title','Tours')

@section('post_styles')
<link rel="stylesheet" href="{{ asset('css/tour-shopify.css') }}">
<style>
    .pagination-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 0;
        margin-top: 20px;
    }
    .pagination-info {
        color: #6c757d;
        font-size: 14px;
    }
    .pagination {
        display: flex;
        list-style: none;
        padding: 0;
        margin: 0;
        gap: 5px;
    }
    .pagination li {
        display: inline-block;
    }
    .pagination a, .pagination span {
        display: block;
        padding: 8px 12px;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        color: #007bff;
        text-decoration: none;
        transition: all 0.2s;
    }
    .pagination .active span {
        background-color: #007bff;
        color: white;
        border-color: #007bff;
    }
    .pagination a:hover {
        background-color: #e9ecef;
        border-color: #dee2e6;
    }
    .pagination .disabled span {
        color: #6c757d;
        pointer-events: none;
        background-color: #fff;
        border-color: #dee2e6;
    }
    
    /* Tab Navigation Styles */
    .shopify-tabs-nav {
        display: flex;
        gap: 0;
        border-bottom: 2px solid #dee2e6;
        margin-bottom: 24px;
    }
    .shopify-tabs-nav .tab-btn {
        padding: 12px 24px;
        background: none;
        border: none;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        color: #6c757d;
        border-bottom: 3px solid transparent;
        margin-bottom: -2px;
        transition: all 0.3s ease;
    }
    .shopify-tabs-nav .tab-btn:hover {
        color: #495057;
    }
    .shopify-tabs-nav .tab-btn.active {
        color: #007bff;
        border-bottom-color: #007bff;
    }
    
    .shopify-tab-content {
        display: none;
    }
    .shopify-tab-content.active {
        display: block;
    }
</style>
@endsection

@section('content')
<div class="shopify-tours-page">
    {{-- Page Header --}}
    <div class="shopify-page-header">
        <div class="shopify-header-content">
            <div class="shopify-header-top">
                <div>
                    <h1 class="shopify-page-title">Tours</h1>
                    <p class="shopify-page-subtitle">Manage and organize all your travel tours</p>
                </div>
                <div class="shopify-header-actions">
                    @include('legend.tour_legend')
                    {!! \App\Helper\PermissionHelper::getCreateButton(route('tour.create'), \App\Tour::class, 'shopify-btn shopify-btn-primary') !!}
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="shopify-page-content">
        <div class="shopify-container">
            {{-- Alert Messages --}}
            @if(session('message_buses'))
                <div class="shopify-alert shopify-alert-info">
                    <i class="fa fa-info-circle"></i>
                    {{ session('message_buses') }}
                </div>
            @endif

            {{-- Tab Navigation --}}
            <div class="shopify-tabs-nav">
                <button class="tab-btn active" data-tab="tours">
                    Tours ({{ $tours->total() }})
                </button>
                <button class="tab-btn" data-tab="client-tours">
                    Requested Tours ({{ $clientTours->total() }})
                </button>
                <button class="tab-btn" data-tab="monthly-chart">
                    Monthly Chart ({{ $monthlyChartTours->total() + $cancelledChartTours->total() }})
                </button>
                <button class="tab-btn" data-tab="archived-tours">
                    Archived Tours ({{ $archivedTours->total() }})
                </button>
            </div>

            {{-- Toolbar: Search & Filters --}}
            <div class="shopify-toolbar">
                <div class="shopify-search-box">
                    <i class="fa fa-search shopify-search-icon"></i>
                    <input type="text"
                               id="tour-search"
                               class="shopify-search-input"
                               placeholder="Search tours by name, date, status..."
                               data-table="tour-table">
                </div>
                <div class="shopify-toolbar-actions">
                    <select id="filterDropdown" class="shopify-filter-select">
                        <option value="">All Statuses</option>
                        <option value="quotations">Quotations</option>
                        <option value="go_ahead">Go Ahead</option>
                    </select>
                    <button class="shopify-btn shopify-btn-secondary export-csv"
                                 data-table="tour-table"
                                 data-filename="tours_export.csv">
                        <i class="fa fa-download"></i>
                        Export CSV
                    </button>
                </div>
            </div>

            {{-- Tab 1: Tours --}}
            <div class="shopify-tab-content active" id="tours-tab">
                <div class="shopify-card">
                    <div class="shopify-table-wrapper">
                        <table id="tour-table" class="shopify-table">
                            <thead>
                                <tr>
                                    <th class="shopify-table-header" style="width: 60px;">ID</th>
                                    <th class="shopify-table-header">{{ trans('main.Name') }}</th>
                                    <th class="shopify-table-header">{{ trans('main.DepDate') }}</th>
                                    <th class="shopify-table-header">{{ trans('Responsible Users') }}</th>
                                    <th class="shopify-table-header">{{ trans('Assigned Users') }}</th>
                                    <th class="shopify-table-header">{{ trans('main.Status') }}</th>
                                    <th class="shopify-table-header">{{ trans('main.ExternalName') }}</th>
                                    <th class="shopify-table-header shopify-table-header-actions">{{ trans('main.Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tours as $tour)
                                <tr class="shopify-table-row clickable-row"
                                    style="background: {{ $tour->getRowBackgroundColor() }};"
                                    data-href="{{ route('tour.show', ['tour' => $tour->id]) }}">
                                    <td class="shopify-table-cell">
                                        <span class="shopify-text-muted">#{{ $tour->id }}</span>
                                    </td>
                                    <td class="shopify-table-cell">
                                        <span class="shopify-text-strong">{{ $tour->name }}</span>
                                    </td>
                                    <td class="shopify-table-cell">
                                        <span class="shopify-text-muted">
                                            {{ $tour->departure_date ? \Carbon\Carbon::parse($tour->departure_date)->format('Y-m-d') : '—' }}
                                        </span>
                                    </td>
                                    <td class="shopify-table-cell">
                                        {{ $tour->responsible_user_names ?? '—' }}
                                    </td>
                                    <td class="shopify-table-cell">
                                        {{ $tour->assigned_user_names ?? '—' }}
                                    </td>
                                    <td class="shopify-table-cell">
                                        <span class="shopify-status-badge" style="background-color: {{ $tour->getStatusColor() }}20; color: {{ $tour->getStatusColor() }}; border: 1px solid {{ $tour->getStatusColor() }}40;">
                                            <span class="shopify-status-dot" style="background-color: {{ $tour->getStatusColor() }};"></span>
                                            {{ $tour->getStatusName() }}
                                        </span>
                                    </td>
                                    <td class="shopify-table-cell">
                                        <span class="shopify-text-muted">{{ $tour->external_name ?? '—' }}</span>
                                    </td>
                                    <td class="shopify-table-cell shopify-table-cell-actions action-cell">
                                        <div class="shopify-action-buttons">
                                            @include('component.action_buttons', ['item' => $tour, 'routePrefix' => 'tour'])
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="shopify-empty-state">
                                        <div class="shopify-empty-state-content">
                                            <i class="fa fa-suitcase shopify-empty-state-icon"></i>
                                            <h3 class="shopify-empty-state-title">No tours found</h3>
                                            <p class="shopify-empty-state-description">Get started by creating your first tour</p>
                                            {!! \App\Helper\PermissionHelper::getCreateButton(route('tour.create'), \App\Tour::class, 'shopify-btn shopify-btn-primary') !!}
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- Pagination --}}
                    @if($tours->hasPages())
                    <div class="pagination-wrapper">
                        <div class="pagination-info">
                            Showing {{ $tours->firstItem() }} to {{ $tours->lastItem() }} of {{ $tours->total() }} entries
                        </div>
                        {{ $tours->links() }}
                    </div>
                    @endif
                </div>
            </div>

            {{-- Tab 2: Requested Tours --}}
            <div class="shopify-tab-content" id="client-tours-tab">
                <div class="shopify-card">
                    <div class="shopify-table-wrapper">
                        <table id="client-tour-table" class="shopify-table">
                            <thead>
                                <tr>
                                    <th class="shopify-table-header" style="width: 60px;">ID</th>
                                    <th class="shopify-table-header">{{ trans('main.Name') }}</th>
                                    <th class="shopify-table-header">{{ trans('Client Name') }}</th>
                                    <th class="shopify-table-header">{{ trans('main.DepDate') }}</th>
                                    <th class="shopify-table-header">{{ trans('main.Status') }}</th>
                                    <th class="shopify-table-header">{{ trans('main.ExternalName') }}</th>
                                    <th class="shopify-table-header shopify-table-header-actions">{{ trans('main.Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($clientTours as $tour)
                                <tr class="shopify-table-row clickable-row"
                                    style="background: {{ $tour->getRowBackgroundColor() }};"
                                    data-href="{{ route('tour.show', ['tour' => $tour->id]) }}">
                                    <td class="shopify-table-cell">
                                        <span class="shopify-text-muted">#{{ $tour->id }}</span>
                                    </td>
                                    <td class="shopify-table-cell">
                                        <span class="shopify-text-strong">{{ $tour->name }}</span>
                                    </td>
                                    <td class="shopify-table-cell">{{ $tour->client_name ?? '—' }}</td>
                                    <td class="shopify-table-cell">
                                        <span class="shopify-text-muted">
                                            {{ $tour->departure_date ? \Carbon\Carbon::parse($tour->departure_date)->format('Y-m-d') : '—' }}
                                        </span>
                                    </td>
                                    <td class="shopify-table-cell">
                                        <span class="shopify-status-badge" style="background-color: {{ $tour->getStatusColor() }}20; color: {{ $tour->getStatusColor() }}; border: 1px solid {{ $tour->getStatusColor() }}40;">
                                            <span class="shopify-status-dot" style="background-color: {{ $tour->getStatusColor() }};"></span>
                                            {{ $tour->getStatusName() }}
                                        </span>
                                    </td>
                                    <td class="shopify-table-cell">
                                        <span class="shopify-text-muted">{{ $tour->external_name ?? '—' }}</span>
                                    </td>
                                    <td class="shopify-table-cell shopify-table-cell-actions action-cell">
                                        <div class="shopify-action-buttons">
                                            @include('component.action_buttons', ['item' => $tour, 'routePrefix' => 'tour'])
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="shopify-empty-state">
                                        <div class="shopify-empty-state-content">
                                            <i class="fa fa-users shopify-empty-state-icon"></i>
                                            <h3 class="shopify-empty-state-title">No requested tours</h3>
                                            <p class="shopify-empty-state-description">Client tour requests will appear here</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- Pagination --}}
                    @if($clientTours->hasPages())
                    <div class="pagination-wrapper">
                        <div class="pagination-info">
                            Showing {{ $clientTours->firstItem() }} to {{ $clientTours->lastItem() }} of {{ $clientTours->total() }} entries
                        </div>
                        {{ $clientTours->appends(['client_page' => $clientTours->currentPage()])->links() }}
                    </div>
                    @endif
                </div>
            </div>

            {{-- Tab 3: Monthly Chart --}}
            <div class="shopify-tab-content" id="monthly-chart-tab">
                <div class="shopify-card">
                    <div class="shopify-card-header">
                        <h3 class="shopify-card-title">On Going Projects</h3>
                        <div class="shopify-toolbar-actions">
                            <select id="year-filter" class="shopify-filter-select">
                                <option value="">All Years</option>
                                @foreach ($years as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endforeach
                            </select>
                            <select id="month-filter" class="shopify-filter-select">
                                <option value="">All Months</option>
                                @foreach ($months as $key => $month)
                                    <option value="{{ $key }}">{{ $month }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="shopify-table-wrapper">
                        <table id="monthly-chart-table" class="shopify-table">
                            <thead>
                                <tr>
                                    <th class="shopify-table-header" style="width: 60px;">ID</th>
                                    <th class="shopify-table-header">{{ trans('main.Name') }}</th>
                                    <th class="shopify-table-header">{{ trans('Responsible Users') }}</th>
                                    <th class="shopify-table-header">{{ trans('main.Status') }}</th>
                                    <th class="shopify-table-header">{{ trans('main.ExternalName') }}</th>
                                    <th class="shopify-table-header shopify-table-header-actions">{{ trans('main.Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($monthlyChartTours as $tour)
                                <tr class="shopify-table-row clickable-row"
                                    style="background: {{ $tour->getRowBackgroundColor() }};"
                                    data-href="{{ route('tour.show', ['tour' => $tour->id]) }}">
                                    <td class="shopify-table-cell">
                                        <span class="shopify-text-muted">#{{ $tour->id }}</span>
                                    </td>
                                    <td class="shopify-table-cell">
                                        <span class="shopify-text-strong">{{ $tour->name }}</span>
                                    </td>
                                    <td class="shopify-table-cell">{{ $tour->responsible_user_names ?? '—' }}</td>
                                    <td class="shopify-table-cell">
                                        <span class="shopify-status-badge" style="background-color: {{ $tour->getStatusColor() }}20; color: {{ $tour->getStatusColor() }}; border: 1px solid {{ $tour->getStatusColor() }}40;">
                                            <span class="shopify-status-dot" style="background-color: {{ $tour->getStatusColor() }};"></span>
                                            {{ $tour->getStatusName() }}
                                        </span>
                                    </td>
                                    <td class="shopify-table-cell">
                                        <span class="shopify-text-muted">{{ $tour->external_name ?? '—' }}</span>
                                    </td>
                                    <td class="shopify-table-cell shopify-table-cell-actions action-cell">
                                        <div class="shopify-action-buttons">
                                            @include('component.action_buttons', ['item' => $tour, 'routePrefix' => 'tour'])
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="shopify-empty-state">
                                        <div class="shopify-empty-state-content">
                                            <i class="fa fa-calendar shopify-empty-state-icon"></i>
                                            <h3 class="shopify-empty-state-title">No ongoing projects</h3>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- Pagination --}}
                    @if($monthlyChartTours->hasPages())
                    <div class="pagination-wrapper">
                        <div class="pagination-info">
                            Showing {{ $monthlyChartTours->firstItem() }} to {{ $monthlyChartTours->lastItem() }} of {{ $monthlyChartTours->total() }} entries
                        </div>
                        {{ $monthlyChartTours->appends(['monthly_page' => $monthlyChartTours->currentPage()])->links() }}
                    </div>
                    @endif
                </div>

                <div class="shopify-card" style="margin-top: 24px;">
                    <div class="shopify-card-header">
                        <h3 class="shopify-card-title">Cancelled Projects</h3>
                    </div>
                    <div class="shopify-table-wrapper">
                        <table id="cancelled-chart-table" class="shopify-table">
                            <thead>
                                <tr>
                                    <th class="shopify-table-header" style="width: 60px;">ID</th>
                                    <th class="shopify-table-header">{{ trans('main.Name') }}</th>
                                    <th class="shopify-table-header">{{ trans('Responsible Users') }}</th>
                                    <th class="shopify-table-header">{{ trans('main.Status') }}</th>
                                    <th class="shopify-table-header">{{ trans('main.ExternalName') }}</th>
                                    <th class="shopify-table-header shopify-table-header-actions">{{ trans('main.Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cancelledChartTours as $tour)
                                <tr class="shopify-table-row clickable-row"
                                    style="background: {{ $tour->getRowBackgroundColor() }};"
                                    data-href="{{ route('tour.show', ['tour' => $tour->id]) }}">
                                    <td class="shopify-table-cell">
                                        <span class="shopify-text-muted">#{{ $tour->id }}</span>
                                    </td>
                                    <td class="shopify-table-cell">
                                        <span class="shopify-text-strong">{{ $tour->name }}</span>
                                    </td>
                                    <td class="shopify-table-cell">{{ $tour->responsible_user_names ?? '—' }}</td>
                                    <td class="shopify-table-cell">
                                        <span class="shopify-status-badge" style="background-color: {{ $tour->getStatusColor() }}20; color: {{ $tour->getStatusColor() }}; border: 1px solid {{ $tour->getStatusColor() }}40;">
                                            <span class="shopify-status-dot" style="background-color: {{ $tour->getStatusColor() }};"></span>
                                            {{ $tour->getStatusName() }}
                                        </span>
                                    </td>
                                    <td class="shopify-table-cell">
                                        <span class="shopify-text-muted">{{ $tour->external_name ?? '—' }}</span>
                                    </td>
                                    <td class="shopify-table-cell shopify-table-cell-actions action-cell">
                                        <div class="shopify-action-buttons">
                                            @include('component.action_buttons', ['item' => $tour, 'routePrefix' => 'tour'])
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="shopify-empty-state">
                                        <div class="shopify-empty-state-content">
                                            <i class="fa fa-ban shopify-empty-state-icon"></i>
                                            <h3 class="shopify-empty-state-title">No cancelled projects</h3>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- Pagination --}}
                    @if($cancelledChartTours->hasPages())
                    <div class="pagination-wrapper">
                        <div class="pagination-info">
                            Showing {{ $cancelledChartTours->firstItem() }} to {{ $cancelledChartTours->lastItem() }} of {{ $cancelledChartTours->total() }} entries
                        </div>
                        {{ $cancelledChartTours->appends(['cancelled_page' => $cancelledChartTours->currentPage()])->links() }}
                    </div>
                    @endif
                </div>
            </div>

            {{-- Tab 4: Archived Tours --}}
            <div class="shopify-tab-content" id="archived-tours-tab">
                <div class="shopify-card">
                    <div class="shopify-table-wrapper">
                        <table id="archive-tour-table" class="shopify-table">
                            <thead>
                                <tr>
                                    <th class="shopify-table-header" style="width: 60px;">ID</th>
                                    <th class="shopify-table-header">{{ trans('main.Name') }}</th>
                                    <th class="shopify-table-header">{{ trans('Responsible Users') }}</th>
                                    <th class="shopify-table-header">{{ trans('main.DepDate') }}</th>
                                    <th class="shopify-table-header">{{ trans('main.Status') }}</th>
                                    <th class="shopify-table-header">{{ trans('main.ExternalName') }}</th>
                                    <th class="shopify-table-header shopify-table-header-actions">{{ trans('main.Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($archivedTours as $tour)
                                <tr class="shopify-table-row clickable-row"
                                    style="background: {{ $tour->getRowBackgroundColor() }};"
                                    data-href="{{ route('tour.show', ['tour' => $tour->id]) }}">
                                    <td class="shopify-table-cell">
                                        <span class="shopify-text-muted">#{{ $tour->id }}</span>
                                    </td>
                                    <td class="shopify-table-cell">
                                        <span class="shopify-text-strong">{{ $tour->name }}</span>
                                    </td>
                                    <td class="shopify-table-cell">{{ $tour->responsible_user_names ?? '—' }}</td>
                                    <td class="shopify-table-cell">
                                        <span class="shopify-text-muted">
                                            {{ $tour->departure_date ? \Carbon\Carbon::parse($tour->departure_date)->format('Y-m-d') : '—' }}
                                        </span>
                                    </td>
                                    <td class="shopify-table-cell">
                                        <span class="shopify-status-badge" style="background-color: {{ $tour->getStatusColor() }}20; color: {{ $tour->getStatusColor() }}; border: 1px solid {{ $tour->getStatusColor() }}40;">
                                            <span class="shopify-status-dot" style="background-color: {{ $tour->getStatusColor() }};"></span>
                                            {{ $tour->getStatusName() }}
                                        </span>
                                    </td>
                                    <td class="shopify-table-cell">
                                        <span class="shopify-text-muted">{{ $tour->external_name ?? '—' }}</span>
                                    </td>
                                    <td class="shopify-table-cell shopify-table-cell-actions action-cell">
                                        <div class="shopify-action-buttons">
                                            @include('component.action_buttons', ['item' => $tour, 'routePrefix' => 'tour'])
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="shopify-empty-state">
                                        <div class="shopify-empty-state-content">
                                            <i class="fa fa-archive shopify-empty-state-icon"></i>
                                            <h3 class="shopify-empty-state-title">No archived tours</h3>
                                            <p class="shopify-empty-state-description">Archived tours will appear here</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- Pagination --}}
                    @if($archivedTours->hasPages())
                    <div class="pagination-wrapper">
                        <div class="pagination-info">
                            Showing {{ $archivedTours->firstItem() }} to {{ $archivedTours->lastItem() }} of {{ $archivedTours->total() }} entries
                        </div>
                        {{ $archivedTours->appends(['archived_page' => $archivedTours->currentPage()])->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tour Clone Modal --}}
<div class="modal fade" id="tour-clone-modal" tabindex="-1" role="dialog" aria-labelledby="tour-clone-label">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="box box-body" style="border-top: none">
                <div class="alert alert-info block-error" style="text-align: center; display: none;"></div>
                <form id="tour-clone-modal-form">
                    <div class="form-group">
                        <label for="departure_date">{{ trans('main.DepartureDate') }}</label>
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            {!! Form::text('departure_date', '', ['class' => 'form-control pull-right datepicker', 'id' => 'departure_date', 'autocomplete' => 'off']) !!}
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success pre-loader-func" id="clone_tour_send">{{ trans('main.Submit') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Tour Status Error Modal --}}
<div class="modal fade" tabindex="-1" id="error_tour">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="form_confirmed_hotel">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">{{ trans('main.Warning') }}!</h4>
                </div>
                <div class="modal-body">
                    <h3 class="error_tour_message"></h3>
                </div>
                <div class="modal-footer">
                    <div class="btn-send-confirmed_hotel">
                        <button type="reset" class="btn btn-success modal-close" data-dismiss="modal">Ok</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<span id="permission" data-permission="{{ \App\Helper\PermissionHelper::checkPermission('tour.edit') }}"></span>

@endsection

@section('post_scripts')
<script src="{{ asset('js/tour-interactions.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabButtons = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.shopify-tab-content');

        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const tabName = this.getAttribute('data-tab');
                
                // Remove active class from all buttons and contents
                tabButtons.forEach(btn => btn.classList.remove('active'));
                tabContents.forEach(content => content.classList.remove('active'));
                
                // Add active class to clicked button
                this.classList.add('active');
                
                // Show corresponding tab content
                const tabId = tabName + '-tab';
                const tabElement = document.getElementById(tabId);
                if (tabElement) {
                    tabElement.classList.add('active');
                }
            });
        });
    });
</script>
@endsection