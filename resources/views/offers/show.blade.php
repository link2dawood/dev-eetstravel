@extends('scaffold-interface.layouts.app')
@section('title','Show')
@section('content')
    @include('layouts.title',
   ['title' => 'Hotel Offer ', 'sub_title' => 'offer Show',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Show', 'route' => null]]])
<section class="content">
    <div class="box box-primary">
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="margin_button">
                        <a href="javascript:history.back()">
                            <button class='btn btn-primary'>{!!trans('main.Back')!!}</button>
                        </a>
                    </div>
                </div>
            </div>
            <div id="fixed-scroll" class="nav-tabs-custom">
                <ul class="nav nav-tabs" id="fixed-scroll" role='tablist'>
                    <li role='presentation' class="active"><a href="#info-tab" aria-controls='info-tab' role='tab' data-toggle='tab'>{!!trans('main.Info')!!}</a></li>
             
                 
                </ul>
            </div>
            <div class="tab-content">
                <div class="tab-pane fade in active" role='tabpanel' id='info-tab'>
					
					<input id="invoice_id" type="hidden" name="invoice_id" value ="{{$offer->id}}">
					<table class = 'table_show table table-bordered col-lg-6'>
                        <tbody>
                       
                        <tr>
                            <td>
                                <b><i>{!!trans('Hotel name')!!} : </i></b>
                            </td>
                            <td class="info_td_show">{!!$package->name??""!!}</td>
                        </tr>
                        <tr>
                            <td>
                                <b><i>{!!trans('City')!!} : </i></b>
                            </td>
                            <td class="info_td_show">{!!$city->name??"";!!}</td>
                        </tr>
                        </tbody>
                    </table>
                    <table class = 'table_show table table-bordered col-lg-6'>
                        <tbody>
                       
                        <tr>
                            <td>
                                <b><i>{!!trans('Tour Name')!!} : </i></b>
                            </td>
                            <td class="info_td_show">{!!$tour->name!!}</td>
                        </tr>
                        <tr>
                            <td>
                                <b><i>{!!trans('Status')!!} : </i></b>
                            </td>
                            <td class="info_td_show">{!!$offer->status;!!}</td>
                        </tr>
                        </tbody>
                    </table>
                    <table class = 'table_show table table-bordered col-lg-6'>
                        <tbody>
                        
                        
                        <tr>
                            <td>
                                <b><i>{!!trans('Supplier Status')!!} : </i></b>
                            </td>
                            <td class="info_td_show">{!!$offer->getStatusName($offer->tms_status)??""!!}</td>
                        </tr>
                        <tr>
                            <td>
                                <b><i>{!!trans('Option Date')!!} : </i></b>
                            </td>
                            <td class="info_td_show">{!!$offer->option_date!!}</td>
                        </tr>
                        </tbody>
                    </table>
					
					<table class = 'table_show table table-bordered col-lg-6'>
                        <tbody>
                        
                        
                        <tr>
                            <td>
                                <b><i>{!!trans('Offer Date')!!} : </i></b>
                            </td>
                            <td class="info_td_show">{!!$stay_date??""!!}</td>
                        </tr>
                        <tr>
                            <td>
                                <b><i>{!!trans('City Tax')!!} : </i></b>
                            </td>
                            <td class="info_td_show">{!!$offer->city_tax!!}</td>
                        </tr>
                        </tbody>
                    </table>
					
					<table class = 'table_show table table-bordered col-lg-6'>
                        <tbody>
                        
                        
                        <tr>
                            <td>
                                <b><i>{!!trans('Halfboard Supp p.p')!!} : </i></b>
                            </td>
                            <td class="info_td_show">{!!$offer->halfboardMax!!}</td>
                        </tr>
                        <tr>
                            <td>
                                <b><i>{!!trans('foc')!!} : </i></b>
                            </td>
                            <td class="info_td_show">{!!$offer->foc_after_every_pax!!}</td>
                        </tr>
                        </tbody>
                    </table>
					
					<table class = 'table_show table table-bordered col-lg-6'>
                        <tbody>
                        
                        
                        <tr>
                            <td>
                                <b><i>{!!trans('Portrage pp')!!} : </i></b>
                            </td>
                            <td class="info_td_show">{!!$stay_date??""!!}</td>
                        </tr>
                        <tr>
                            <td>
                                <b><i>{!!trans('Hotel File')!!} : </i></b>
                            </td>
                            <td class="info_td_show">{!!$offer->hotel_file!!}</td>
                        </tr>
                        </tbody>
                    </table>
					
					<table class = 'table_show table table-bordered col-lg-6'>
                        <tbody>
                        
                        
                        <tr>
                            <td>
                                <b><i>{!!trans('Hotel Note')!!} : </i></b>
                            </td>
                            <td class="info_td_show">{!!$offer->hotel_file??""!!}</td>
                        </tr>
						@php
							$printedRoomNames = [];
							@endphp

							@foreach ($selected_room_types as $selected_room_type)
							@if (!in_array($selected_room_type->name, $printedRoomNames))
							<tr>
								<td>
									<b>{{$selected_room_type->name }}</b>
								</td>
								<td class="info_td_show">{!!$offer->offersWithRoomPrice($selected_room_type)??""!!}</td>
							</tr>
							@php
							$printedRoomNames[] = $selected_room_type->name;
							@endphp
							@endif
							@endforeach
                       
                        </tbody>
                    </table>
                    <div style="clear: both"></div>
                   
                </div>
				<div class="">
					<h3 class="box-title">Cancellation Policies</h3>
					<table id="recent-offers-table" class="table table-striped table-bordered table-hover" style='background:#fff; width: 100%; table-layout: fixed'>
                    <thead>
                    <th>ID</th>
					<th>{!!trans('Policy')!!}</th>
                    <th>{!!trans('Hotel Name')!!}</th>
                    <th>{!!trans('City')!!}</th>
					<th>{!!trans('Status')!!}</th>
                    <th>{!!trans('Date of stay')!!}</th>
					<th>{!!trans('Offer Date')!!}</th>
					<th>{!!trans('Option Date')!!}</th>
                    <th>{!!trans('Tour Name')!!}</th>
                    <th class="actions-button" style="width: 140px!important">{!!trans('main.Actions')!!}</th>
                    </thead>
                    <tfoot>
                    <tr>
                        <th class="not"></th>
						<th>{!!trans('Policy')!!}</th>
                    <th>{!!trans('Hotel Name')!!}</th>
                    <th>{!!trans('City')!!}</th>
					<th>{!!trans('Status')!!}</th>
                    <th>{!!trans('Date of stay')!!}</th>
					<th>{!!trans('Offer Date')!!}</th>
					<th>{!!trans('Option Date')!!}</th>
                    <th>{!!trans('Tour Name')!!}</th>
                        <th class="not"></th>
                    </tr>
                    </tfoot>
                </table>
				</div>
           
                
					
            </div>
            </div>
</section>
@endsection

@section('post_scripts')
    <script src="{{ asset('js/comment.js') }}"></script>
	
@endsection
@push('scripts')
	<script type="text/javascript" src="{{asset('js/jspdf.min.js')}}"></script>
	 <script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/2.4.1/lodash.min.js"></script>
<script>
	
     $(document).ready(function() {
        let table = $('#recent-offers-table').DataTable({
            dom: 	"<'row'<'col-sm-5'l><'col-sm-2'B><'col-sm-5'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            buttons: [
                {
                    extend: 'csv',
                    title: 'Current Offers List',
                    exportOptions: {
                        columns: ':not(.actions-button)'
                    }
                },
                {
                    extend: 'excel',
                    title: 'Current Offers List',
                    exportOptions: {
                        columns: ':not(.actions-button)'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    title: 'Current Offer List',
					orientation: 'landscape',
                    exportOptions: {
                        columns: ':not(.actions-button)'
                    }
                }
            ],
            processing: true,
            serverSide: true,
            pageLength: 50,
            ajax: {
                url: "{{route('cancellation_policies_data',['offer_id'=> $offer->id])}}",
            },
            columns: [
                {data: 'id', name: 'id'},
				
				{data: 'cancel_policy', name: 'cancel_policy'},
                {data: 'hotel_name', name: 'hotel_name'},
                {data: 'city', name: 'city'},
				{data: 'status', name: 'status'},
                {data: 'stay_date', name: 'stay_date'},
               {data: 'stay_date', name: 'stay_date'},
                {data: 'option_date', name: 'option_date'},
				 {data: 'tour_name', name: 'tour_name'},
                {data: 'action', name: 'action', searchable: false, sorting: false, orderable: false}
            ],
        });
        $('#recent-offers-table tfoot th').each( function () {
            let column = this;
            if (column.className !== 'not') {
                let title = $(this).text();
                $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');
            }
        });
        table.columns().every( function () {
            let that = this;

            $('input', this.footer()).on('keyup change', function() {
                if(that.search() !== this.value) {
                    that.search(this.value).draw();
                }
            });
        });
        $('#recent-offers-table tfoot th').appendTo('#recent-offers-table thead');
    });
		</script>

@endpush