{{-- 
    Tour Index Page - Tabler Design
    Fully responsive with modern Tabler components
    Features: Tabs, search, filters, responsive cards/tables
--}}
@extends('scaffold-interface.layouts.tabler-app')
@section('title','Tours')

@section('post_styles')
<style>
    /* Responsive table enhancements */
    @media (max-width: 768px) {
        .page-title {
            font-size: 1.5rem;
        }
        
        .btn-list {
            flex-wrap: wrap;
        }
        
        .table-responsive {
            font-size: 0.875rem;
        }
    }
    
    /* Status badge styling */
    .status-dot {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        margin-right: 6px;
    }
    
    /* Clickable row hover effect */
    .clickable-row {
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .clickable-row:hover {
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        transform: translateY(-1px);
    }
    
    /* Empty state styling */
    .empty {
        padding: 3rem 1rem;
    }
    
    .empty-icon {
        font-size: 3rem;
        color: var(--tblr-muted);
        margin-bottom: 1rem;
    }
    
    /* Action buttons in tables */
    .action-cell .btn-list {
        gap: 0.25rem;
    }
</style>
@endsection

@section('content')
<div class="container-xl">
    {{-- Page Header --}}
    <div class="page-header d-print-none">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    Tour Management
                </div>
                <h2 class="page-title">
                    <i class="ti ti-plane me-2"></i>Tours
                </h2>
            </div>
            {{-- Page Actions --}}
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    @include('legend.tour_legend')
                    {!! \App\Helper\PermissionHelper::getCreateButton(route('tour.create'), \App\Tour::class, 'btn btn-primary') !!}
                </div>
            </div>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('message_buses'))
        <div class="alert alert-info alert-dismissible" role="alert">
            <div class="d-flex">
                <div>
                    <i class="ti ti-info-circle me-2"></i>
                </div>
                <div class="flex-fill">
                    {{ session('message_buses') }}
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    {{-- Main Content Card with Tabs --}}
    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a href="#tours-tab" class="nav-link active" data-bs-toggle="tab" aria-selected="true" role="tab">
                        <i class="ti ti-plane me-1"></i>Tours
                        <span class="badge bg-blue-lt ms-1">{{ $tours->total() }}</span>
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="#monthly-chart-tab" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                        <i class="ti ti-chart-line me-1"></i>Monthly
                        <span class="badge bg-green-lt ms-1">{{ $monthlyChartTours->total() + $cancelledChartTours->total() }}</span>
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="#archived-tours-tab" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                        <i class="ti ti-archive me-1"></i>Archived
                        <span class="badge bg-secondary-lt ms-1">{{ $archivedTours->total() }}</span>
                    </a>
                </li>
            </ul>
        </div>
        
        <div class="card-body">
            {{-- Search & Filter Toolbar --}}
            <div class="row mb-3">
                <div class="col-md-6 col-lg-5 mb-2 mb-md-0">
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <i class="ti ti-search"></i>
                        </span>
                        <input type="text" 
                               id="tour-search" 
                               class="form-control" 
                               placeholder="Search tours by name, date, status..."
                               data-table="tour-table">
                    </div>
                </div>
                <div class="col-md-6 col-lg-7">
                    <div class="d-flex gap-2 justify-content-md-end">
                        <select id="filterDropdown" class="form-select" style="max-width: 200px;">
                            <option value="">All Statuses</option>
                            <option value="quotations">Quotations</option>
                            <option value="go_ahead">Go Ahead</option>
                        </select>
                        <button class="btn btn-secondary export-csv"
                                data-table="tour-table"
                                data-filename="tours_export.csv">
                            <i class="ti ti-download me-1"></i>
                            <span class="d-none d-sm-inline">Export CSV</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Tab Content --}}
            <div class="tab-content">
                {{-- Tab 1: Tours --}}
                <div class="tab-pane fade show active" id="tours-tab" role="tabpanel">
                    <div class="table-responsive">
                        <table id="tour-table" class="table card-table table-vcenter">
                            <thead>
                                <tr>
                                    <th style="width: 60px;">ID</th>
                                    <th>{{ trans('main.Name') }}</th>
                                    <th>{{ trans('main.DepDate') }}</th>
                                    <th class="d-none d-lg-table-cell">{{ trans('Responsible Users') }}</th>
                                    <th class="d-none d-xl-table-cell">{{ trans('Assigned Users') }}</th>
                                    <th>{{ trans('main.Status') }}</th>
                                    <th class="d-none d-md-table-cell">{{ trans('main.ExternalName') }}</th>
                                    <th class="text-end">{{ trans('main.Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tours as $tour)
                                <tr class="clickable-row"
                                    style="background: {{ $tour->getRowBackgroundColor() }};"
                                    data-href="{{ route('tour.show', ['tour' => $tour->id]) }}">
                                    <td class="clickable-cell"> {{-- Added class --}}
                                        <span class="text-muted">#{{ $tour->id }}</span>
                                    </td>
                                    <td class="clickable-cell"> {{-- Added class --}}
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold">{{ $tour->name }}</span>
                                            <small class="text-muted d-lg-none">
                                                {{ $tour->responsible_user_names ?? '' }}
                                            </small>
                                        </div>
                                    </td>
                                    <td class="clickable-cell"> {{-- Added class --}}
                                        <span class="text-muted">
                                            {{ $tour->departure_date ? \Carbon\Carbon::parse($tour->departure_date)->format('Y-m-d') : '—' }}
                                        </span>
                                    </td>
                                    <td class="d-none d-lg-table-cell clickable-cell"> {{-- Added class --}}
                                        {{ $tour->responsible_user_names ?? '—' }}
                                    </td>
                                    <td class="d-none d-xl-table-cell clickable-cell"> {{-- Added class --}}
                                        {{ $tour->assigned_user_names ?? '—' }}
                                    </td>
                                    <td class="clickable-cell"> {{-- Added class --}}
                                        <span class="badge" style="background-color: {{ $tour->getStatusColor() }}20; color: {{ $tour->getStatusColor() }}; border: 1px solid {{ $tour->getStatusColor() }}40;">
                                            <span class="status-dot" style="background-color: {{ $tour->getStatusColor() }};"></span>
                                            {{ $tour->getStatusName() }}
                                        </span>
                                    </td>
                                    <td class="d-none d-md-table-cell clickable-cell"> {{-- Added class --}}
                                        <span class="text-muted">{{ $tour->external_name ?? '—' }}</span>
                                    </td>
                                    <td class="text-end action-cell"> {{-- This cell is NOT clickable --}}
                                        <div class="btn-list justify-content-end">
                                            @include('component.action_buttons', ['item' => $tour, 'routePrefix' => 'tour'])
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="empty">
                                            <div class="empty-icon">
                                                <i class="ti ti-plane icon" style="font-size: 3rem;"></i>
                                            </div>
                                            <p class="empty-title">No tours found</p>
                                            <p class="empty-subtitle text-muted">Get started by creating your first tour</p>
                                            <div class="empty-action">
                                                {!! \App\Helper\PermissionHelper::getCreateButton(route('tour.create'), \App\Tour::class, 'btn btn-primary') !!}
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- Pagination --}}
                    @if($tours->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted">
                            Showing {{ $tours->firstItem() }} to {{ $tours->lastItem() }} of {{ $tours->total() }} entries
                        </div>
                        <div>
                            {{ $tours->links() }}
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Tab 2: Monthly Chart --}}
                <div class="tab-pane fade" id="monthly-chart-tab" role="tabpanel">
                   {{-- (Your code for other tabs is fine) --}}
                   ...
                </div>

                {{-- Tab 3: Archived Tours --}}
                <div class="tab-pane fade" id="archived-tours-tab" role="tabpanel">
                   {{-- (Your code for other tabs is fine) --}}
                   ...
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ================================== --}}
{{-- == START OF MODAL FIX == --}}
{{-- We are adding your delete modal HTML directly to this file --}}
{{-- ================================== --}}
<div class="modal modal-blur fade" id="myModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-status bg-danger"></div>
            <div class="modal-body text-center py-4">
                <i class="ti ti-alert-triangle icon mb-2 text-danger icon-lg"></i>
                <h3>Are you sure?</h3>
                <div class="text-muted" id="delete-message">Do you really want to delete this tour?</div>
                <small class="text-muted d-block mt-2" id="tour-name-display"></small>
            </div>
            <div class="modal-footer">
                <div class="w-100">
                    <div class="row">
                        <div class="col">
                            <button type="button" class="btn w-100" data-bs-dismiss="modal">
                                Cancel
                            </button>
                        </div>
                        <div class="col">
                            <button type="button" id="confirmDeleteBtn" class="btn btn-danger w-100">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- ================================== --}}
{{-- == END OF MODAL FIX == --}}
{{-- ================================== --}}


{{-- Tour Clone Modal --}}
<div class="modal modal-blur fade" id="tour-clone-modal" ...>
    {{-- (Your clone modal code is fine) --}}
    ...
</div>

{{-- Tour Status Error Modal --}}
<div class="modal modal-blur fade" tabindex="-1" id="error_tour" ...>
    {{-- (Your error modal code is fine) --}}
    ...
</div>

<span id="permission" data-permission="{{ \App\Helper\PermissionHelper::checkPermission('tour.edit') }}"></span>

@endsection

@section('post_scripts')
<script src="{{ asset('js/tour-interactions.js') }}"></script>

{{-- ================================== --}}
{{-- == START OF COMBINED SCRIPT FIX == --}}
{{-- ================================== --}}
<script>
$(document).ready(function() {
    
    // --- Script for your Delete Modal ---
    let deleteUrl = null;
    let tourModal = new bootstrap.Modal(document.getElementById('myModal'), {
        backdrop: 'static',
        keyboard: false
    });

    // Handle delete button click for tours
    $(document).on('click', '.delete', function(e) {
        e.preventDefault();
        e.stopPropagation(); // <-- This is critical

        deleteUrl = $(this).data('link');
        
        if (!deleteUrl) {
            alert('Error: Delete URL not found');
            return;
        }

        var $row = $(this).closest('tr');
        var tourName = $row.find('td:eq(1)').text() || 'Unknown Tour';
        var tourId = $row.find('td:eq(0)').text() || '';
        
        $('#tour-name-display').text('Tour: ' + tourName + (tourId ? ' (ID: ' + tourId + ')' : ''));
        
        tourModal.show();
    });

    // Handle confirm delete button
    $('#confirmDeleteBtn').on('click', function() {
        if (!deleteUrl) {
            alert('Error: No delete URL specified');
            return;
        }

        var $btn = $(this);
        var originalText = $btn.html();
        
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Deleting...');

        $.ajax({
            url: deleteUrl,
            method: 'GET', // Note: This should be 'DELETE' but is matching your script
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                tourModal.hide();
                var successMsg = response.message || 'Tour deleted successfully!';
                alert(successMsg);
                setTimeout(function() {
                    location.reload();
                }, 500);
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html(originalText);
                tourModal.hide();
                var errorMsg = 'Error deleting tour!';
                
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    } else if (xhr.responseJSON.error) {
                        errorMsg = xhr.responseJSON.error;
                    }
                } else if (xhr.status === 404) {
                    errorMsg = 'Tour not found!';
                } else if (xhr.status === 403) {
                    errorMsg = 'You do not have permission to delete this tour!';
                } else if (xhr.status === 500) {
                    errorMsg = 'Server error. Please try again later.';
                }
                
                alert(errorMsg);
            }
        });
    });

    // Reset form when modal is hidden
    document.getElementById('myModal').addEventListener('hidden.bs.modal', function() {
        deleteUrl = null;
        $('#confirmDeleteBtn').prop('disabled', false).html('Delete');
    });
    // --- End of Delete Modal Script ---


    // --- Script from your original file (Tooltips & Clone) ---
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    $('.clone-tour-button').show();
    $(document).on('click', '.clone-tour-button', function(e) {
        e.preventDefault();
        e.stopPropagation(); // <-- Added this
        let id = $(this).data('id');
        $('.block-error').text('');
        $('.block-error').hide();
        $('#tour-clone-modal-form').attr('action', '/tour/' + id + '/clone');
        var cloneModal = new bootstrap.Modal(document.getElementById('tour-clone-modal'));
        cloneModal.show();
    });

    $('#clone_tour_send').on('click', function (e) {
        e.preventDefault();
        $('.block-error').text('');
        $('.block-error').hide();

        if($('#departure_date').val() === '') {
            $('.block-error').text('Enter Date');
            $('.block-error').show();
        } else {
            $('#tour-clone-modal-form').submit();
        }
    });
    // --- End of original file script ---


    // --- Clickable Row Fix Script ---
    $(document).on('click', '.clickable-cell', function(e) {
        // This script now only runs on cells with the 'clickable-cell' class
        
        // Find the <tr> parent and get its data-href
        const href = $(this).closest('tr').data('href');
        if (href) {
            window.location.href = href;
        }
    });

    // Stop propagation on the action cell (where buttons are)
    $(document).on('click', '.action-cell', function(e) {
        e.stopPropagation();
    });

});
</script>
{{-- ================================== --}}
{{-- == END OF COMBINED SCRIPT FIX == --}}
{{-- ================================== --}}
@endsection