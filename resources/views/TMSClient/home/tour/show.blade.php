<!DOCTYPE html>
<html lang="en">

@include('TMSClient.layout.head')
<style>
.checked {
  color: orange;
}
</style>
<body>
  <div class="main">
   @include('TMSClient.layout.nav')
    <div class="main-content" style="margin-top: 64px;">
      <section class="tours-single">
        <div class="container">
          <div class="d-flex justify-content-between align-items-start">
            <h1 class="title">{{$tour->name}}</h1>
            {{--<a href="" class="btn btn-primary">Edit</a>--}}
          </div>
          <div class="tour-packages"></div>
		<div class="row">
            <div class="col-lg-8">
              <div class="card card-s1">
                <div class="card-body">
                  <div class="accordion-item">
                    <div class="accordion-header">
                      <div class="d-flex justify-content-between align-items-start">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapsebus" aria-expanded="true" aria-controls="collapse1">
                          <h3 class="card-title">Bus Company</h3>
                        </button>
                        

                          <button id="serviceModel" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#tourServiceAddModal" data-url="https://dev.eetstravel.com/sevice_modal/show/215 " data-date="2023-06-15" data-tour_id="215" data-departure_date="2023-06-15" data-retirement_date="2023-06-16" data-tourdayid="1860" data-link="https://dev.eetstravel.com/client_tour_package" style="border:none"><i class="fas fa-plus"></i></button>

                        
                      </div>
                    </div>
                     
                     <div id="collapsebus" class="accordion-collapse collapse" data-bs-parent="#tourAccordion">
                      <div class="accordion-body">
                        @if(!empty($tour->transfers))
						  					 

                        @foreach($tour->transfers as $package)
                        <ol class="services-list">
                          <li class="list-item">
                            <div class="d-flex align-items-center justify-content-between">
                              <p class="text fw-bold"> {{ $package->name }}<br>(Bus Company)</p>
		
								@if($package->paid == "No")
                              <div class="icon not-paid">
                                <i class="fas fa-check"></i>
                                <i class="fas fa-money-bill"></i>
                                <div class="content paid-content">This service fees have been paid.</div>
                                <div class="content not-paid-content">This service fees not paid yet.</div>
                              </div>
								@else
								<div class="icon paid">
                                <i class="fas fa-check"></i>
                                <i class="fas fa-money-bill"></i>
                                <div class="content paid-content">This service fees have been paid.</div>
                                <div class="content not-paid-content">This service fees not paid yet.</div>
                              </div>
								@endif
                            </div>
                            <p class="text"><i class="fas fa-phone me-2"></i>{!! @$package->service()->work_phone!!}</p>
                            <p class="text"><i class="fas fa-map-marker-alt me-2">{{  @$package->service()->address_first}}</i></p>
                          </li>
                          
                        </ol>
                        @endforeach
                        @else
                        <ol class="services-list">
                            <li class="list-item">
                              <div class="d-flex align-items-center justify-content-between">
                                <p class="text fw-bold"> {{ $package->name }}<br>(Bus Company)</p>
                                <div class="icon not-paid">
                                  <i class="fas fa-check"></i>
                                  <i class="fas fa-money-bill"></i>
                                  <div class="content paid-content">This service fees have been paid.</div>
                                  <div class="content not-paid-content">This service fees not paid yet.</div>
                                </div>
                              </div>
                              <p class="text"><i class="fas fa-phone me-2"></i>{!! @$package->service()->work_phone!!}</p>
                              <p class="text"><i class="fas fa-map-marker-alt me-2"></i>{{  @$package->service()->address_first}}</p>
                            </li>
                            
                          </ol>
                        @endif
                      </div>
                    </div>               
                  </div>
                </div>
              </div>
            </div>
          </div>
          <?php $countDay = 0; ?>
          @foreach($tourDates as $tourDate)
              <?php $countDay++ ?>
          <div class="row">
            <div class="col-lg-8">
              <div class="card card-s1">
                <div class="card-body">
                  <div class="accordion-item">
                    <div class="accordion-header">
                      <div class="d-flex justify-content-between align-items-start">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                          data-bs-target="#collapse{{ $countDay }}" aria-expanded="false" aria-controls="collapse{{ $countDay }}">
                          <h3 class="card-title">{!!trans('Day')!!} {{ $countDay }}
                            - {{ (new \Carbon\Carbon($tourDate->date))->formatLocalized('%B %d, %Y (%A)') }}</h3>
                        </button>
                        {{-- <button class="btn btn-primary btn-sm pull-right add-service-quick" id = "service-button" data-bs-toggle="modal"
                          data-bs-target="#tourServiceAddModal"
                          data-tourDayId="{{$tourDate->id}}"
                          data-link="{{route('tour_package.store')}}" data-date="{!! $tourDate->date !!}"
                          data-tour_id='{{$tour->id}}'
                          data-departure_date='{{$tour->departure_date}}'
                          data-retirement_date="{{$tour->retirement_date}}"><i class="fas fa-plus"></i></button> --}}

                          <button id="serviceModel" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                data-bs-target="#tourServiceAddModal" data-url="{{route('service_modal.show',[$tour->id])}} "   data-date="{!! $tourDate->date !!}" data-tour_id='{{$tour->id}}'
                data-departure_date='{{$tour->departure_date}}'
                data-retirement_date="{{$tour->retirement_date}}" data-tourDayId="{{$tourDate->id}}" data-link="https://dev.eetstravel.com/client_tour_package" style = "border:none"><i class="fas fa-plus"></i></button>

                        
                      </div>
                    </div>
                   
                    <div id="collapse{{ $countDay }}" class="accordion-collapse collapse" data-bs-parent="#tourAccordion">
                      <div class="accordion-body">
                        @if(!empty($tourDate->packages))
                        @foreach($tourDate->packages as $package)
                        <ol class="services-list">
                          <li class="list-item">
                            <div class="d-flex align-items-center justify-content-between">
                              <p class="text fw-bold">{{$package->name}}</p>
		
								@if($package->paid == "No")
                              <div class="icon not-paid">
                                <i class="fas fa-check"></i>
                                <i class="fas fa-money-bill"></i>
                                <div class="content paid-content">This service fees have been paid.</div>
                                <div class="content not-paid-content">This service fees not paid yet.</div>
                              </div>
								@else
								<div class="icon paid">
                                <i class="fas fa-check"></i>
                                <i class="fas fa-money-bill"></i>
                                <div class="content paid-content">This service fees have been paid.</div>
                                <div class="content not-paid-content">This service fees not paid yet.</div>
                              </div>
								@endif
                            </div>
                            <p class="text"><i class="fas fa-phone me-2"></i>{!! @$package->service()->work_phone!!}</p>
                            <p class="text"><i class="fas fa-map-marker-alt me-2"></i>{{  @$package->service()->address_first}}</p>
                          </li>
                          
                        </ol>
                        @endforeach
                        @else
                        <ol class="services-list">
                            <li class="list-item">
                              <div class="d-flex align-items-center justify-content-between">
                                <p class="text fw-bold"></p>
                                <div class="icon not-paid">
                                  <i class="fas fa-check"></i>
                                  <i class="fas fa-money-bill"></i>
                                  <div class="content paid-content">This service fees have been paid.</div>
                                  <div class="content not-paid-content">This service fees not paid yet.</div>
                                </div>
                              </div>
                              <p class="text"><i class="fas fa-phone me-2"></i>+10 203808 900</p>
                              <p class="text"><i class="fas fa-map-marker-alt me-2"></i>New york, USA.</p>
                            </li>
                            
                          </ol>
                        @endif
                      </div>
                    </div>
                   
                  </div>
                </div>
              </div>
            </div>
          </div>
          @endforeach
        </div>
      </section>
    </div>
  </div>

  <!-- Modal -->
  <input type="text" id="default_reference_id" hidden name="reference_id"
  value="{{ $tour->id }}">
  
	<div class="modal fade" id="tourServiceAddModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" id = "serviceModelContent">
	 </div>

	
	</div>

	{{-- Transfer Modal Date--}}
<div class="modal fade" id="selectDateForTransferPackage" aria-labelledby='selectDateForTransferPackageLabel'>
    <div class="modal-dialog modal-lg" role='document'>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <h4 class="modal-title">{!!trans('main.SelectDate')!!}</h4>
            </div>
            <div class="modal-body">

                <div class="alert alert-info error_date" style="text-align: center; display: none;">

                </div>

				<div class="row">
                        <div class="col-lg-6">
                          <div class="input-wrapper">
                            <label class="form-label" for="tourFrom">{!!trans('main.DateFrom')!!}</label>
                           {!! Form::date('date_service_package', '', ['class' => 'form-control pull-right datepickerDisabledTransferPackage',
                         'id' => 'date_service_transfer_package']) !!}
                          </div>
                        </div>
                        <div class="col-lg-6">
                          <div class="input-wrapper">
                            <label class="form-label" for="tourFrom">{!!trans('main.DateTo')!!}</label>
                           {!! Form::date('date_service_retirement_package', '', [
                        'class' => 'form-control pull-right datepickerDisabledTransferPackage',
                        'id' => 'date_service_transfer_retirement_package'
                        ]) !!}
                          </div>
                        </div>
                      </div>
                
                <div style="overflow: hidden; display: block">
                    <button class="addTransferPackageWithDate btn btn-success pull-right"
                            type="button">{!!trans('main.Next')!!}</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" id="select-driver-and-bus">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="form_transfer_buses_drivers">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss='modal' aria-label="Close"><span
                                aria-hidden='true'>&times;</span></button>
                    <h4 class="modal-title">{!!trans('main.Selectdriversandbuses')!!}</h4>
                </div>
                <div class="box box-body" style="border-top: none">
                    <div class="list-driver-and-buses"></div>

                    <div class="modal-footer">
                        <div class="btn-send-driver">
                            <button type="button"
                                    class="btn btn-success btn-send-transfer_add pre-loader-func">{!!trans('main.Add')!!}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
  <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
 @include('TMSClient.layout.footer')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/2.4.1/lodash.min.js"></script>
  <script type="text/javascript" src="{{asset('js/lib/moment.js')}}"></script>
  <script type="text/javascript" src="{{asset('js/bootstrap-datepicker.min.js')}}"></script>
  <script type="text/javascript" src="{{asset('js/bootstrap-datetimepicker.min.js')}}"></script>
  <script type="text/javascript" src="{{asset('js/bootstrap-tables.js')}}"></script>
  <script type="text/javascript" src="{{URL::asset('js/select2.min.js') }}"></script>

  <script type="text/javascript" src='{{asset('js/supplier-search.js')}}'></script>
  <script type="text/javascript" src='{{asset('js/hide-elements.js')}}'></script>
  <script type="text/javascript" src='{{asset('js/roomlist.js')}}'></script>
 
  <script>
 $(document).ready(function(){
 
 
$(document).on('click', '#serviceModel', function(e){
 
    e.preventDefault();

    var url = $(this).data('url');
    var tourDayId = $(this).data('tourdayid');
    var date = $(this).data('date');
    var tour_id = $(this).data('tour_id');
    var departure_date = $(this).data('departure_date');
    var retirement_date = $(this).data('retirement_date');
    addService.tourDayId = $(this).data('tourdayid');
    addService.route = $(this).data('link');
    var data = {
      date: date,
      tourDayId: tourDayId,
      tour_id: tour_id,
      departure_date: departure_date,
      retirement_date: retirement_date,
      
        };

    $('#serviceModelContent').html(''); // leave it blank before ajax call
    $('#modal-loader').show();      // load ajax loader
    $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    $.ajax({
        url: url,
        type: 'POST',
        dataType: 'html',
        data: data,
       
    })
    .done(function(data){
        $('#serviceModelContent').html('');    
        $('#serviceModelContent').html(data); // load response 
        $('#modal-loader').hide();        // hide ajax loader   
		
    })
    .fail(function(){
        $('#serviceModelContent').html('<i class="glyphicon glyphicon-info-sign"></i> Something went wrong, Please try again...');
        $('#modal-loader').hide();
    });

});

});
let addService = {
  run: () => {
        addService.config();
       
    },
  config: () => {
        addService.tourDayId = '';
        addService.route = '';
        addService.tour_id = $('#default_reference_id').val();
        addService.service_id = '';
        addService.service_type = '';
        addService.tourTransfer = '';
        addService.service_name = '';
        addService.service_old_time = null;
        addService.departure_date = $('#tour_dates').data('departure_date');
        addService.retirement_date = $('#tour_dates').data('retirement_date');
        addService.departure_date_transfer = null;
        addService.retirement_date_transfer = null;
        addService.package_id = '';
        addService.page_service = null;
        addService.link_redirect_edit = null;
        addService.drivers_id = [];
        addService.bus_id = null;
        addService.tourDayIdRetirement = null;
        addService.click_day = null;
    },
	getDriverAndBusTransfer: () => {
        $.ajax({
            method: 'GET',
            url: `/driver_bus_transfer/api/${addService.service_id}`,
            data: {}
        }).done((res) => {
            $('.list-driver-and-buses').html(res);
        });
    },
    getDriverAndBusTransferPackage: () => {
        $.ajax({
            method: 'GET',
            url: `/driver_bus_transfer/api/${addService.service_id}`,
            data: {}
        }).done((res) => {
            $('.list-driver-and-buses_transfer_package').html(res);
        });
    },
	setGetDate : (res) => {
        let date2 = $('#date_service_package').datepicker('getDate');
        let tempStartDate = new Date(date2);
        let default_end = new Date(tempStartDate.getFullYear(), tempStartDate.getMonth(), tempStartDate.getDate()+1);
        let nextDayDateformated = moment(default_end).format('YYYY-MM-DD');

        $('#date_service_retirement_package').val('').datepicker("remove");
        $('#date_service_retirement_package').datepicker({
            format: 'yyyy-mm-dd',
            autoclose : true,
            startDate: res.startDate,
            endDate: res.endDate
        });
        $('#date_service_retirement_package').datepicker('update', nextDayDateformated);

        console.log(res);



    },
	addPackages: () => {
	
        if(addService.tour_id){
            $.ajax({
                method: "POST",
                url: "/tour/" + addService.tour_id + "/generatePackages",
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    departureDate: addService.departure_date,
                    retirementDate: addService.retirement_date,
                    tourId: addService.tour_id
                }
            }).done((res) => {
               
        });
            addService.checkTransferIsExist();

        }

    },
	changeServiceTime: () => {
        $('.service-time').datetimepicker({
                format: 'HH:mm', 'sideBySide' : true,
                tooltips: {
                    incrementHour: '',
                    pickHour: '',
                    decrementHour:'',
                    incrementMinute: '',
                    pickMinute: '',
                    decrementMinute:'',
                    incrementSecond: '',
                    pickSecond: '',
                    decrementSecond:'',
                }
            }).on("dp.hide", function (e) {

                 let timeKey = $(this).attr('name');
                 let type = $(this).data('type');
                 let is_main =  $(this).data('is_main');

            // $(this).datetimepicker('hide');
                 $.ajax({
                    url: '/tour_package/' + $(this).data('package_id') + '/change_time',
                    method: 'GET',
                    data: {
                        timeKey: timeKey,
                        timeValue: $(this).val(),
                        type : type,
                        is_main : is_main
                    }
                 }).done( (res) => {
                    addService.addPackages();
                 })
                 // return false;
            });
    },
  storeService: (change_service = null) => {
        $.ajax({
            method: 'POST',
            url: addService.route,
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                tourDayId: addService.tourDayId,
                tourId: addService.tour_id,
                serviceType: addService.service_type,
                serviceId: addService.service_id,
                serviceName: addService.service_name,
                tourTransfer: addService.tourTransfer,
                serviceOldTime: addService.service_old_time,
                pageService: addService.page_service,
                drivers_id: addService.drivers_id,
                bus_id: addService.bus_id,
                tourDayIdRetirement: addService.tourDayIdRetirement,
                dep_date_transfer: addService.departure_date_transfer,
                ret_date_transfer: addService.retirement_date_transfer,
                package_id: addService.package_id,
            }
        }).done((res) => {
			
            if(res.bus_busy){
                $('.block-error-driver').text('');
                $('.block-error-driver').append('<span>'+res.bus_busy_message+'</span>');
                $('.block-error-driver').css({'display': 'block'});
            }
            else if(res.transfer_add_date){
                $('.block-error-driver').text('');
                $('.block-error-driver').append('<span>'+res.transfer_message+'</span>');
                $('.block-error-driver').css({'display': 'block'});
                $('#selectDateForTransferPackage').modal('hide');
                $('#select-driver-and-bus').modal('hide');
            }
            else{
				
                if(change_service === 'true'){
                    $.ajax({
                        method: 'GET',
                        url: `/tour_package/${addService.package_id}/delete`,
                        data: {}
                    }).done((data_delete) => {
                        if(addService.page_service !== 'change_edit_service'){
                            addService.addPackages();
                        }else{
                            window.location.replace(res);
                        }
                    });
                }else{
                    if(addService.page_service !== 'change_edit_service'){
						var data = `<li  class="list-item">
						<div class="d-flex align-items-center justify-content-between">
                      		<h6 class="fw-semibold">${addService.service_name}</h6>
                   
                    </div>
                    <p class="text"><i class="fas fa-phone me-2"></i>+10 203808 900</p>
                    <p class="text"><i class="fas fa-map-marker-alt me-2"></i>New york, USA.</p>
                   </li>`;
						 $(".service_list_show").append(data);
               
                    }else{
						
                        window.location.replace(res);
                    }
                }
            }
            addService.service_old_time = null;
            $('#service-modal').modal('hide');
            $('#select-driver-and-bus').modal('hide');
            $('#select-driver-and-bus_transfer_package').modal('hide');
            $('#overlay_delete').remove();
            $('.modal-backdrop').hide();
        })
    },
}
$(document).ready(addService.run());
$('body').on('click', '.add-service-button', function(e){
	
            e.preventDefault();
            addService.service_type = $(this).data('service_type');
            addService.service_id = $(this).data('service_id');
            addService.service_name = $(this).data('service_name');
            var _this = $(this);
            var tour_id = addService.tour_id;

            if(addService.service_type === 'transfer' || addService.tourTransfer === "true"){

$(function(){
$('#selectDateForTransferPackage').modal({
   show:true,
   backdrop:'static'
});
 //now on button click
  $('#selectDateForTransferPackage').modal('show');
});
                setTimeout(function (e) {
                    $.ajax({
                        method: "POST",
                        url: "/get_tour_days",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            tour_id: tour_id,
                        }
                    }).done((res) => {
                        $(document).find('.datepickerDisabledTransferPackage').val('').datepicker("remove");
                        $(document).find('#date_service_transfer_package').val(res.startDate);
                        $(document).find('#date_service_transfer_retirement_package').val(res.endDate);
                        $(document).find('.datepickerDisabledTransferPackage').datepicker({
                            format: 'yyyy-mm-dd',
                            autoclose : true,
                            startDate: res.startDate,
                            endDate: res.endDate
                        });
                        $('.addTransferPackageWithDate').attr('data-link', '');
                        $('.addTransferPackageWithDate').attr('data-tour_id', '');
                        $('.addTransferPackageWithDate').attr('data-link', $(_this).attr('data-link'));
                        $('.addTransferPackageWithDate').attr('data-tour_id', tour_id);

                    })
                }, 200);

            }
            else if(addService.service_type === 'hotels'){


             //   $('#selectDateForHotelPackage').modal();

            
                $.ajax({
                    method: "GET",
                    url: `/get_date_day/${addService.tourDayId}`,
                    data: {}
                }).done((res) => {
                    addService.click_day = res.date;
                });

                setTimeout(function (e) {
                    $.ajax({
                        method: "POST",
                        url: "/get_tour_days",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            tour_id: tour_id,
                        }
                    }).done((res) => {
                        $(document).find('.datepickerDisabledHotelPackage').val('').datepicker("remove");
                        $(document).find('.datepickerDisabledHotelPackage').val(addService.click_day );
                        $(document).find('.datepickerDisabledHotelPackage').datepicker({
                            format: 'yyyy-mm-dd',
                            autoclose : true,
                            startDate: res.startDate,
                            endDate: res.endDate
                        });
                        $('.addHotelPackageWithDate').attr('data-link', '');
                        $('.addHotelPackageWithDate').attr('data-tour_id', '');
                        $('.addHotelPackageWithDate').attr('data-link', $(_this).attr('data-link'));
                        $('.addHotelPackageWithDate').attr('data-tour_id', tour_id);

                        $(document).find('#date_service_package').datepicker({
                            format: 'yyyy-mm-dd',
                            autoclose : true,
                            startDate: res.startDate,
                            endDate: res.endDate
                        });


                        addService.setGetDate(res);


                        $('#date_service_package').datepicker().on('changeDate', function(e) {
                            addService.setGetDate(res);



                        });

                    })
                }, 200);
            }
            else{
                addService.storeService();
            }
        });
    </script>
     <script>
 
 
  $('body').on('keyup', '#search_input', function(e){
         
         $(".search_input").each(function() {
          
    var search = $(this).val();
    if ( search .trim() !== '') {

      
      $.ajax({
           method: "POST",
           url: "/searchPackageData",
           data:{
             _token: $('meta[name="csrf-token"]').attr('content'),
             search: search,
			 tourDayId: addService.tourDayId,
			 tourId: addService.tour_id,
           },
            success: function(result){
             $('.services-wrapper').html(result);
           }});
    }
});  
       });
	
		 
		     $('body').on('click', '.dropdown-item', function(e){
  
				var dropdown_value = $(this).text();

				$.ajax({
				   method: "POST",
				   url: "/dropdownPackageData",
				   data:{
					 _token: $('meta[name="csrf-token"]').attr('content'),
					 dropdown_value: dropdown_value,
					 tourDayId: addService.tourDayId,
					 tourId: addService.tour_id,
				   },
					success: function(result){
					 $('.services-wrapper').html(result);
				   }});
    		});
     
		 
		 $(document).on('click', '.addTransferPackageWithDate', function () {
            $('#selectDateForTransferPackage').find('.error_date').html(' ');
            $('#selectDateForTransferPackage').find('.error_date').css({'display':'none'});

            if($('#date_service_transfer_package').val() === '' || $('#date_service_transfer_retirement_package').val() === ''){
                $('#selectDateForTransferPackage').find('.error_date').css({'display':'block'});
                $('#selectDateForTransferPackage').find('.error_date').append('Enter the date');
                return false;
            }

            if($('#date_service_transfer_package').val() > $('#date_service_transfer_retirement_package').val()){
                $('#selectDateForTransferPackage').find('.error_date').css({'display':'block'});
                $('#selectDateForTransferPackage').find('.error_date').append('Date from can not be less than the date to');
                return false;
            }

            addService.departure_date_transfer = $('#date_service_transfer_package').val();
            addService.retirement_date_transfer = $('#date_service_transfer_retirement_package').val();

            $('#selectDateForTransferPackage').modal('hide');


            setTimeout(function (e) {
                addService.getDriverAndBusTransfer();
				$(function(){
					$('#select-driver-and-bus').modal({
					   show:true,
					   backdrop:'static'
					});
					 //now on button click
					  $('#select-driver-and-bus').modal('show');
					});
                $('#select-driver-and-bus').modal();
            }, 200)

        });
		 
		 
		 $(document).on('click', '.btn-send-transfer_add', function () {
            let oldForm = document.forms.form_transfer_buses_drivers;
            let form = new FormData(oldForm);
            addService.bus_id = form.get('bus_transfer');
            addService.drivers_id = form.getAll('driver_transfer');

            addService.storeService();
        });
		 
		 		     $('body').on('change', '#selection_dropdown', function(e){

				var dropdown_value = $(this).val();

				$.ajax({
				   method: "POST",
				   url: "/selectionPackageData",
				   data:{
					 _token: $('meta[name="csrf-token"]').attr('content'),
					 dropdown_value: dropdown_value,
					 tourDayId: addService.tourDayId,
					 tourId: addService.tour_id,
				   },
					success: function(result){
					 $('.services-wrapper').html(result);
				   }});
    		});
</script>
</body>

</html>