@extends('scaffold-interface.layouts.app')
@section('title','Show')
@section('content')
<style>
	.select2-container {
 margin-top: -25px !important;
}
</style>
<section class="content">
	<h1>
        {!!trans('main.ShowTourPackage')!!}
    </h1>
	 <div class="box box-primary">
            <div class="box box-body">
				<div >
    
    <br>

    <br>
    <table class = 'table table-bordered'>
        <thead>
			<h1>Package Detail</h1>
        </thead>
        <tbody>
            <tr>
                <td>
                    <b><i>{!!trans('Name')!!}: </i></b>
                </td>
                <td>{!!$tour_package->name!!}</td>
            </tr>
            <tr>
                <td>
                    <b><i>{!!trans('Description')!!}: </i></b>
                </td>
                <td>{!!$tour_package->description!!}</td>
            </tr>
            
        </tbody>
    </table>

    <span id="default_reference_id" data-info="{{ $tour_package->id }}"></span>
    <span id="default_reference_type" data-info="{{ \App\Comment::$services['tour_package'] }}"></span>
    <span id="default_token_val" data-info="{{ csrf_token() }}"></span>
    <div id="commentContainer">

    </div>
				</div>	
			
			@if($tour_package->service()->service_type == 'Hotel')
				<tr>
                <td>
                    <b><i>{!!trans('Supplier Url')!!}: </i></b>
                </td>
				<td><a href ={!!$tour_package->supplier_url."/".$tour_package->id!!} >Go to  Supplier Page</a></td>
            	</tr>
				<div class = "row" style = "margin:10px">
				<div id="tour_create" class = "col-md-1">
                    {!! \App\Helper\PermissionHelper::getCreateButton(route('offers.create',$tour_package->id), \App\Tour::class) !!}
                </div>
					<div class = "col-md-1">
				<a class='btn btn-success' href= "{{route('offers.emails',$tour_package->id)}}" >{!! trans('Offer Emails') !!}</a>
								  </div>
				</div>
				<div class ="">
					<h1>Hotel Offers</h1>
					 @foreach ($tour_package->hotel_offers as $offer)
						@endforeach
                        @if(empty($offer) )
                        <p>We dont have any offers yes please send email to get offer from supplier</p>
                        @else
					<div class="table-responsive" style="max-height: 400px;">
                            <table id="offers-table" class="table table-striped table-bordered table-hover" style='background:#fff; width: 100%; table-layout: auto ;'>
                    <thead>
                    <th>ID</th>
					<th style="width: 104px;">{!!trans('Offer Status')!!}</th>
                    <th style="width: 104px;">{!!trans('Supplier Status')!!}</th>
						@php
											$printedRoomNames = [];
										@endphp

										@foreach ($selected_room_types as $selected_room_type)
											@if (!in_array($selected_room_type->name, $printedRoomNames))
												<th class="rooms-title">{{ $selected_room_type->name }}</th>
												@php
													$printedRoomNames[] = $selected_room_type->name;
												@endphp
											@endif
										@endforeach
                    <th>{!!trans('Currency')!!}</th>
					<th>{!!trans('City Tax')!!}</th>
                    <th>{!!trans('Halfboard Supp p.p')!!}</th>
						
					<th>{!!trans('foc')!!}</th>
                    <th>{!!trans('Max per group')!!}</th>
					<th>{!!trans('Portrage pp')!!}</th>
					<th>{!!trans('Hotel File')!!}</th>
					<th>{!!trans('Hotel Note')!!}</th>
                    <th class="actions-button" style="width: 140px!important">{!!trans('main.Actions')!!}</th>
                    </thead>
                    <tfoot>
                    <tr>
                        <th class="not"></th>
                   <th style="width: 104px;">{!!trans('Offer Status')!!}</th>
                    <th style="width: 104px;">{!!trans('Supplier Status')!!}</th>
						@php
											$printedRoomNames = [];
										@endphp

										@foreach ($selected_room_types as $selected_room_type)
											@if (!in_array($selected_room_type->name, $printedRoomNames))
												<th class="rooms-title">{{ $selected_room_type->name }}</th>
												@php
													$printedRoomNames[] = $selected_room_type->name;
												@endphp
											@endif
										@endforeach
                    <th>{!!trans('Currency')!!}</th>
					<th>{!!trans('City Tax')!!}</th>
                    <th>{!!trans('Halfboard Supp p.p')!!}</th>
					<th>{!!trans('foc')!!}</th>
                    <th>{!!trans('Max per group')!!}</th>
					<th>{!!trans('Portrage pp')!!}</th>
					<th>{!!trans('Hotel File')!!}</th>
					<th>{!!trans('Hotel Note')!!}</th>
                        <th class="not"></th>
                    </tr>
                    </tfoot>
                </table>
						</div>
					@endif	
		 		</div>
				@endif
		 </div>
	</div>
	
</section>
<div class="modal fade" id="TemplatesModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="false" style="padding-left: 17px;padding-right: 17px;">
    <div class="modal-dialog modal-lg" style="width: 90%;">
        <form class="modal-content" id="templateSendForm" enctype="multipart/form-data" action="/templates/api/send"
            method="POST">
            <input name="_token" type="hidden" value="{{ csrf_token() }}">
            <input name="id" id="id" type="hidden" value="">
            <input name="package_id" id="package_id" type="hidden" value="">
            <input name="tour_id" id="tour_id" type="hidden" value="">
			<input name="offer" id="offer" type="hidden" value="1">
			<input name="offer_id" id="offer_id" type="hidden" value="1">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">{!! trans('main.SendTemplate') !!}</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">

                    <div class="form-group">
                        <div class="input-group">
                            <input name="email" id="email" class="form-control" placeholder="E-mail:"
                                required="" value="">
                            <span class="input-group-addon"> {!! trans('main.Template') !!}</span>
                            <!-- insert this line -->
                            <span class="input-group-addon"
                                style="width:0px; padding-left:0px; padding-right:0px; border:none;"></span>

                            <select id="template_selector" name="template_selector" class="form-control">
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <input name="subject" id="subject" class="form-control" placeholder="Subject:"
                            value="" style="
    pointer-events: none;
">
                    </div>
                    <div class="form-group">
                        <textarea name="templatesContent" id="templatesContent" placeholder="Non required Field" class="form-control"
                            style="height: 400px; visibility: hidden; display: none;">
                            </textarea>
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

                <!-- /.box-body -->
                <div class="box-footer">
                    <div class="pull-right">
                        <button id="send" onclick="sendTemplate();" class="btn btn-primary"><i
                                class="fa fa-file-code-o"></i> {!! trans('main.Send') !!}</button>
                    </div>
                    <button type="reset" class="btn btn-default modal-close" data-dismiss="modal"><i
                            class="fa fa-times"></i> {!! trans('main.Discard') !!}</button>
                </div>
                <!-- /.box-footer -->
            </div>

        </form>
    </div>
</div>
@endsection

@section('post_scripts')
<script src="{{ asset('js/loadtemplate.js') }}"></script>
<script>
		    $(document).ready(function() {
        let table = $('#offers-table').DataTable({
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
                    title: 'Offers List',
                    exportOptions: {
                        columns: ':not(.actions-button)'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    title: 'Offer List',
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
                url: "{{route('offers_data',[$tour_package->id,0])}}",
            },
            columns: [
				{data: 'id', name: 'id'},
				{data: 'status_tms', name: 'status_tms', className: 'offer-status'},
				{data: 'status', name: 'status'},
				@foreach ($selected_room_types as $selected_room_type)
                { "data": "{{ $selected_room_type->code }}" },
            	@endforeach
                {data: 'currency', name: 'currency'},
				{data: 'city_tax', name: 'city_tax'},
				{data: 'halfboard', name: 'halfboard'},
				{data: 'foc_after_every_pax', name: 'foc_after_every_pax'},
				{data: 'halfboardMax', name: 'halfboardMax'},
				{data: 'portrage_perperson', name: 'portrage_perperson'},
				{data: 'hotel_file', name: 'hotel_file'},
                {data: 'hotel_note', name: 'hotel_note'},
               
                {data: 'action', name: 'action', searchable: false, sorting: false, orderable: false}
               
            ],
			'columnDefs': [
			{
				'targets': 7,
				'visible': false
			  },
            {
                'targets': 1,
                'createdCell':  function (td, cellData, rowData, row, col) {
					console.log(td);
                    var url = "{{ url('offer/updatestatus') }}/" + rowData.id;
                    $(td).attr('data-status-link', url); 
                }
            }
        ],
			"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
			if(aData.supplier_delete == 1){
			$(nRow).css('background', '#ffbbb2');
			}
               
            }
		 });
        $('#offers-table tfoot th').each( function () {
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
        $('#offers-table tfoot th').appendTo('#offers-table thead');
    });
	  $(document).ready(function () {
        $('.table-row').click(function () {
            var url = $(this).data('href');
            if (url) {
                window.open(url, '_blank');
            }
        });
    });
	
	
		
	let offerChanger = {
		init: () => {
        offerChanger.getStatuses();
	},
		settings: () => {
		offerChanger.fieldName = '';
		offerChanger.fieldValue = '';
		offerChanger.updateLink = '';
		offerChanger.cityName = '';
		offerChanger.oldValue = '';
		offerChanger.element;
		offerChanger.countryList;
		offerChanger.statuses;
	},
		updateTour: () => {
        $.ajax({
			url: offerChanger.updateLink,
			method: 'PUT',
			data: {
				_token: $('meta[name="csrf-token"]').attr('content'),
				fieldName: offerChanger.fieldName,
				fieldValue: offerChanger.fieldValue,
			}
		}).done( (res) => {
			if (res == 'wrong date') {
				$(tourChanger.element).text(tourChanger.oldValue);
			}
			
			if(res.status_error) {
                $('#error_tour').find('.error_tour_message').html(res.status_error);
                $('#error_tour').modal();
                $('#error_tour').on('hidden.bs.modal', function () {
                    //location.reload();
                });
            }

			tourChanger.fieldName = '';
			tourChanger.fieldValue = '';
			tourChanger.updateLink = '';
			tourChanger.cityName = '';
			tourChanger.oldValue = '';
		})
	},
		
		handleChange: (that) => {
		$('table').off('click', 'tr td:not(:last-child):not(.fc-event-container)');
		offerChanger.fieldName = that.className.split('-')[1];
        offerChanger.updateLink = $(that).closest('tr').find('a.show-button').data('link');
		var value = $(that).text();
		var valueStatus = $(that).attr('data-name-status');	
			if (offerChanger.fieldName == 'status'){
			$(that).attr('class', '');
			$(that).attr('class', 'touredit-process');


			$(that).text('');
			$(that).append('<form><select name="status_offer" class="offer-status form-control"></select></form>');
			$(offerChanger.statuses).each(function(key, status){
				let selected = (status.name == valueStatus) ? 'selected="selected"' : '';
                $(that).find('.offer-status').append('<option value="' + status.id + '"' + ' ' + selected + '>' + status.name + '</option>');
			});

            $(that).find('.offer-status').on('change', function(){
                $(that).attr('data-name-status', '');
				offerChanger.fieldValue = $(this).val();

				$(this).remove();
				let statusName = $.grep(offerChanger.statuses, function(e){
					return e.id == offerChanger.fieldValue;
				});

                $(that).text(statusName[0].name);
                $(that).attr('data-name-status', statusName[0].name);
                offerChanger.updateLink = $(that).attr('data-status-link');
                offerChanger.updateTour();
                $('table').on('click', 'tr td:not(:last-child):not(.fc-event-container)', eventHandlerForoffer);
                $(that).attr('class', '');
                $(that).attr('class', 'offer-status');
			});
            $(document).keyup(function(e) {
                if (e.keyCode === ESCAPE_CODE) {
                    $(that).find('.offer-status').change();
                }
            });
            $(that).find('.offer-status').on('blur', function(){
                $(that).find('.offer-status').change();
			});
		}
		},
		
		getStatuses: () => {
			$.ajax({
				url: '/offer/api/status_list',
			}).done( (res) => {
				offerChanger.statuses = JSON.parse(res);
			})
		}
		
		
	}
	
	function eventHandlerForoffer(){

	let elem = this;
	if(this.className.split('-')[1].trim() === 'process'){
		return false;
	}

	if(this.className.split('-')[1].trim() === 'status'){
		
        return offerChanger.handleChange(elem);
    }else{
        finder.class = elem.className.trim();
        finder.finder(elem);
	}
}
	offerChanger.init();
	$(document).on('click', 'table:not(.finder-disable) tr td:not(:last-child):not(.fc-event-container)', eventHandlerForoffer);
	</script>
    <script src="{{ asset('js/comment.js') }}"></script>
@endsection