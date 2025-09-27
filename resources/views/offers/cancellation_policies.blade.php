@extends('scaffold-interface.layouts.app')
@section('title','Index')
@section('content')
    @include('layouts.title',
           ['title' => 'Cancellation Polices', 'sub_title' => 'Policies Offer List',
           'breadcrumbs' => [
           ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
           ['title' => 'Currencies', 'icon' => null, 'route' => null]]])
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" id="cancellation-search" class="form-control" placeholder="Search cancellation policies..." onkeyup="filterTable('cancellation-policies-table', this.value)">
                        </div>
                        <div class="col-md-6 text-right">
                            <button class="btn btn-success btn-sm" onclick="exportTableToCSV('cancellation-policies-table', 'cancellation_policies_export.csv')">
                                <i class="fa fa-download"></i> Export CSV
                            </button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="cancellation-policies-table" class="table table-striped table-bordered table-hover bootstrap-table" style='background:#fff; width: 100%;'>
                        <thead>
                            <tr>
                                <th onclick="sortTable(0, 'cancellation-policies-table')">ID <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(1, 'cancellation-policies-table')">{!!trans('Policy')!!} <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(2, 'cancellation-policies-table')">{!!trans('Hotel Name')!!} <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(3, 'cancellation-policies-table')">{!!trans('City')!!} <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(4, 'cancellation-policies-table')">{!!trans('Status')!!} <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(5, 'cancellation-policies-table')">{!!trans('Date of stay')!!} <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(6, 'cancellation-policies-table')">SIN <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(7, 'cancellation-policies-table')">DOU <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(8, 'cancellation-policies-table')">TRI <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(9, 'cancellation-policies-table')">{!!trans('Offer Date')!!} <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(10, 'cancellation-policies-table')">{!!trans('Option Date')!!} <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(11, 'cancellation-policies-table')">{!!trans('Tour Name')!!} <i class="fa fa-sort"></i></th>
                                <th class="actions-button" style="width: 140px!important">{!!trans('main.Actions')!!}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($processedOffers as $offer)
                            <tr>
                                <td>{{ $offer->id }}</td>
                                <td>{{ $offer->cancel_policy }}</td>
                                <td>{{ $offer->hotel_name }}</td>
                                <td>{{ $offer->city_name }}</td>
                                <td>{{ $offer->status }}</td>
                                <td>{{ $offer->stay_date }}</td>
                                <td>{{ $offer->SIN }}</td>
                                <td>{{ $offer->DOU }}</td>
                                <td>{{ $offer->TRI }}</td>
                                <td>{{ $offer->offer_date ? \Carbon\Carbon::parse($offer->offer_date)->format('Y-m-d') : '' }}</td>
                                <td>{{ $offer->option_date ? \Carbon\Carbon::parse($offer->option_date)->format('Y-m-d') : '' }}</td>
                                <td>{{ $offer->tour_name }}</td>
                                <td>
                                    <!-- Action buttons would go here -->
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-info btn-sm" title="View">
                                            <i class="fa fa-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="13" class="text-center">No cancellation policies found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Clone Modal -->
        <div class="modal fade" id="tour-clone-modal" tabindex="-1" role='dialog' aria-labelledby='tour-clone-label'>
            <div class="modal-dialog" role='document'>
                <div class="modal-content">
                    <div class="box box-body" style="border-top: none">
                        <div class="alert alert-info block-error" style="text-align: center; display: none;"></div>
                        <form id="tour-clone-modal-form">
                            <div class="form-group">
                                <label for="tour_id">{{ trans('main.Tour') }}</label>
                                <input name="offer_date" id="offer_date" type="hidden" value="">
                                <input name="option_date" id="option_date" type="hidden" value="">
                                <select name="tour_id" id="tour_id" class="form-control tour_dropdown" required>
                                    @foreach ($tours as $tour)
                                        <option value="{{ $tour->id }}">{{ $tour->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group" id="services" style="display:none"></div>
                            <div class="form-group" id="service_div"></div>
                            <button type="submit" class="btn btn-success pre-loader-func" id="clone_tour_send">{!!trans('main.Submit')!!}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Templates Modal -->
    <div class="modal fade" id="TemplatesModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false" style="padding-left: 17px;padding-right: 17px;">
        <div class="modal-dialog modal-lg" style="width: 90%;">
            <form class="modal-content" id="templateSendForm" enctype="multipart/form-data" action="/templates/api/send" method="POST">
                <input name="_token" type="hidden" value="{{ csrf_token() }}">
                <input name="id" id="id" type="hidden" value="">
                <input name="package_id" id="package_id" type="hidden" value="">
                <input name="tour_id" id="tour_id" type="hidden" value="">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">{!! trans('main.SendTemplate') !!}</h3>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <div class="input-group">
                                <input name="email" id="email" class="form-control" placeholder="E-mail:" required="" value="">
                                <span class="input-group-addon"> {!! trans('main.Template') !!}</span>
                                <span class="input-group-addon" style="width:0px; padding-left:0px; padding-right:0px; border:none;"></span>
                                <select id="template_selector" name="template_selector" class="form-control"></select>
                            </div>
                        </div>
                        <div class="form-group">
                            <input name="subject" id="subject" class="form-control" placeholder="Subject:" value="" style="pointer-events: none;">
                        </div>
                        <div class="form-group">
                            <textarea name="templatesContent" id="templatesContent" placeholder="Non required Field" class="form-control" style="height: 400px; visibility: hidden; display: none;"></textarea>
                        </div>
                        <div class="form-group">
                            <div class="btn btn-default btn-file">
                                <i class="fa fa-paperclip"></i> {!! trans('main.Attachment') !!}
                                <input type="file" name="attachment[]" multiple="" name="file" id="file">
                            </div>
                            <div id="file_name"></div>
                            <script>
                                document.getElementById('file').onchange = function() {
                                    $('#file_name').html('Selected files: <br/>');
                                    $.each(this.files, function(i, file) {
                                        $('#file_name').append(file.name + ' <br/>');
                                    });
                                };
                            </script>
                            <p class="help-block">Max. 32MB</p>
                        </div>
                    </div>
                    <div class="box-footer">
                        <div class="pull-right">
                            <button id="send" onclick="sendTemplate();" class="btn btn-primary"><i class="fa fa-file-code-o"></i> {!! trans('main.Send') !!}</button>
                        </div>
                        <button type="reset" class="btn btn-default modal-close" data-dismiss="modal"><i class="fa fa-times"></i> {!! trans('main.Discard') !!}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script src="{{ asset('js/loadtemplate.js') }}"></script>
<script src="{{ asset('js/bootstrap-tables.js') }}"></script>
<script>
    $(document).ready(function () {
        // Initialize Bootstrap table
        initializeBootstrapTable('cancellation-policies-table');

        $('#tour-clone-modal-form').submit(function (e) {
            var userConfirmed = confirm('Are you sure? Do you really want to submit the form?');
            if (!userConfirmed) {
                e.preventDefault();
                location.reload();
            }
        });
    });

    function dropdown_ajax(tour_id, offer_date, option_date) {
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content') }
        });
        $.ajax({
            type: "POST",
            url: `/offer/${tour_id}/days_dropdown`,
            data: {
                offer_date: offer_date,
                option_date: option_date,
            },
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
            error: function(result) {
                console.log(result);
            }
        });
    }

    setTimeout(function () {
        $('.tour_dropdown').on('change', function(){
            let offer_date = $('#offer_date').val();
            let option_date = $('#option_date').val();
            dropdown_ajax($(this).val(), offer_date, option_date);
        });

        $('.change-tour-button').show();
        $('.change-tour-button').on('click', function(){
            let id = $(this).data('id');
            let tour_id = $(this).data('tour');
            let offer_date = $(this).data('offer_date');
            let option_date = $(this).data('option_date');

            $('#offer_date').val(offer_date);
            $('#option_date').val(option_date);

            dropdown_ajax(tour_id, offer_date, option_date);
            $('#tour_id').val(tour_id).trigger('change');

            $('#tour-clone-modal-form').attr('action', '/offer/' + id + '/assign_to_tour');
        });
    }, 3000);
</script>
@endpush