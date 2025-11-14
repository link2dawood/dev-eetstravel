@extends('scaffold-interface.layouts.tabler-app')
@section('title','Index')
@section('content')
    @include('layouts.title',
        ['title' => 'Current Offers', 'sub_title' => 'Offer List',
        'breadcrumbs' => [
            ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
            ['title' => 'Currencies', 'icon' => null, 'route' => null]
        ]])
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" id="current-offers-search" class="form-control" placeholder="Search current offers..." onkeyup="filterTable('current-offers-table', this.value)">
                        </div>
                        <div class="col-md-6 text-right">
                            <button class="btn btn-success btn-sm" onclick="exportTableToCSV('current-offers-table', 'current_offers_export.csv')">
                                <i class="fa fa-download"></i> Export CSV
                            </button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="current-offers-table" class="table table-striped table-bordered table-hover bootstrap-table" style='background:#fff; width: 98%; table-layout: fixed'>
                        <thead>
                            <tr>
                                <th onclick="sortTable(0, 'current-offers-table')">ID <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(1, 'current-offers-table')">{!!trans('Tour Name')!!} <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(2, 'current-offers-table')">{!!trans('City')!!} <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(3, 'current-offers-table')">{!!trans('Status')!!} <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(4, 'current-offers-table')">{!!trans('Departure Date')!!} <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(5, 'current-offers-table')">{!!trans('Return Date')!!} <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(6, 'current-offers-table')">{!!trans('PAX')!!} <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(7, 'current-offers-table')">{!!trans('Created At')!!} <i class="fa fa-sort"></i></th>
                                <th class="actions-button" style="width: 140px!important">{!!trans('main.Actions')!!}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tours as $tour)
                            <tr>
                                <td>{{ $tour->id }}</td>
                                <td>{{ $tour->name }}</td>
                                <td>{{ $tour->city ? $tour->city->name : '' }}</td>
                                <td>
                                    <span class="badge badge-primary" style="background-color: {{ $tour->getStatusColor() }}">
                                        {{ $tour->getStatusName() }}
                                    </span>
                                </td>
                                <td>{{ $tour->departure_date ? \Carbon\Carbon::parse($tour->departure_date)->format('Y-m-d') : '' }}</td>
                                <td>{{ $tour->retirement_date ? \Carbon\Carbon::parse($tour->retirement_date)->format('Y-m-d') : '' }}</td>
                                <td>{{ $tour->pax }}</td>
                                <td>{{ $tour->created_at ? $tour->created_at->format('Y-m-d H:i') : '' }}</td>
                                <td onclick="event.stopPropagation();">
                                    @include('component.action_buttons', [
                                        'item' => $tour,
                                        'routePrefix' => 'tour'
                                    ])
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center">No current offers found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    {{-- ================================== --}}
    {{-- == START OF MODAL HTML FIX == --}}
    {{-- This adds your delete modal HTML to the page --}}
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
    {{-- == END OF MODAL HTML FIX == --}}
    {{-- ================================== --}}

@endsection

@push('scripts')
<script src="{{ asset('js/bootstrap-tables.js') }}"></script>
<script src="{{ asset('js/loadtemplate.js') }}"></script>

<script>
$(document).ready(function () {
    // Initialize Bootstrap table
    initializeBootstrapTable('current-offers-table');

    // Tour Clone Modal Submission Confirmation
    $('#tour-clone-modal-form').submit(function (e) {
        if (!confirm('Are you sure? Do you really want to submit the form?')) {
            e.preventDefault();
            location.reload();
        }
    });

    // AJAX for dropdown (existing)
    function dropdown_ajax(tour_id, offer_date, option_date) {
        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content')}
        });

        $.ajax({
            type: "POST",
            url: `/offer/${tour_id}/days_dropdown`,
            data: { offer_date: offer_date, option_date: option_date },
            success: function(result) {
                if (result[0] === "") {
                    $("#service_div").show();
                    $("#services").hide();
                    $("#service_div").html(`<h3> Please Add Service in the tour </h3>`);
                } else {
                    $("#service_div").hide();
                    $("#services").show();
                    $("#services").html(result);
                }
            },
            error: function(result) { console.log(result); }
        });
    }

    setTimeout(function () {
        $('.tour_dropdown').on('change', function(){
            dropdown_ajax($(this).val(), $('#offer_date').val(), $('#option_date').val());
        });

        $('.change-tour-button').show().on('click', function(){ 
            let id = $(this).data('id');
            let tour_id = $(this).data('tour');
            let offer_date = $(this).data('offer_date');
            let option_date = $(this).data('option_date');

            dropdown_ajax(tour_id, offer_date, option_date);
            $('#offer_date').val(offer_date);
            $('#option_date').val(option_date);
            $('#tour_id').trigger('change');
            $('#tour-clone-modal-form').attr('action', '/offer/' + id + '/assign_to_tour');
        });
    }, 5000);

    
    {{-- ================================== --}}
    {{-- == START OF DELETE SCRIPT FIX == --}}
    {{-- This script makes your delete modal work --}}
    {{-- ================================== --}}
    let deleteUrl = null;
    let tourModal = new bootstrap.Modal(document.getElementById('myModal'), {
        backdrop: 'static',
        keyboard: false
    });

    // Handle delete button click for tours
    $(document).on('click', '.delete', function(e) {
        e.preventDefault();
        e.stopPropagation(); // Stop click from bubbling

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
    {{-- ================================== --}}
    {{-- == END OF DELETE SCRIPT FIX == --}}
    {{-- ================================== --}}

});
</script>
@endpush