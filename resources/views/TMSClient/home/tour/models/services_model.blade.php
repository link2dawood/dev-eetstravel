
      <div class="modal-content">
        <form action="">
          <div class="modal-header border-0">
            <h1 class="modal-title fs-5" id="tourServiceAddModal">{{ (new \Carbon\Carbon($date))->formatLocalized('%B %d, %Y (%A)') }}</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-lg-6 order-lg-2">
                <h3 class="fs-3 fw-semibold">Services</h3>
                
                <ol class="services-list service_list_show" id="service_list_show">
                    @foreach($tourDates as $tourDate)
                    @if(!empty($tourDate->packages))
                        @foreach($tourDate->packages as $package)
                  <li class="list-item">
                    <div class="d-flex align-items-center justify-content-between">
                      <h6 class="fw-semibold">{{$package->name}}</h6>
                      {{--<button class="btn btn-primary btn-sm"><i class="fas fa-trash-alt"></i></button>--}}
                    </div>
					 
                    <p class="text"><i class="fas fa-phone me-2"></i>+10 203808 900</p>
                    <p class="text"><i class="fas fa-map-marker-alt me-2"></i>New york, USA.</p>
                  </li>
                    @endforeach
                    @endif
                     @endforeach
                  
                </ol>
              </div>
              <div class="col-lg-6">
				  <select class="form-select mb-5 d-none"  id="selection_dropdown" aria-label="Default select example">
						<option value="1" selected>Deluxe 5 star</option>
						<option value="2">standard 5 star</option>
						<option value="3">better 4 star</option>
						<option value="4">standard 4 star</option>
						<option value="5">3-4 star</option>
					</select>
            
                <div class="input-group mb-3">
					
                  <input type="search" class="form-control search_input" placeholder="Search services" id = "search_input" >
				<button  class="btn btn-primary dropdown-toggle" id = "search"  data-bs-toggle="dropdown" aria-expanded="false"><img src="{{asset('clientassets/img/svg/filter-light.svg')}}" src="{{asset('clientassets/img/svg/filter-light.svg')}}"  alt=""></button>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li><button class="dropdown-item" type="button">Hotel</button></li>
                    <li><button class="dropdown-item" type="button">Event</button></li>
                    <li><button class="dropdown-item" type="button">Guide</button></li>
                    <li><button class="dropdown-item" type="button">Restaurant</button></li>
					 <li><button class="dropdown-item" type="button">Transfer</button></li>
                  </ul>
                </div>
                <div class="services-wrapper">
               
                  <ol class="services-list">
                    @foreach($hotels as $hotel)
                    <li class="list-item">
                      <div class="d-flex align-items-center justify-content-between">
                        <h6 class="fw-semibold">{{$hotel->name}}{{"(hotel)"}}</h6>
                      
                        <button class="btn btn-primary btn-sm add-service-button pre-loader-func" data-link="https://dev.eetstravel.com/client_tour_package" data-service_type="hotel" data-service_id="{{$hotel->id}}" data-service_name="{{$hotel->name}}" data-tourDayId="{{$tourDayId}}"><i class="fas fa-plus"></i></button>
                      </div>
						
                        @for($i=1 ; $i <=5 ; $i ++)
							@if($hotel->rate == 2)
								@if($i<=2)
									<span class="fa fa-star checked"></span>
								@else
									<span class="fa fa-star "></span>
								@endif
							@elseif($hotel->rate == 3)
								@if($i<=2)
									<span class="fa fa-star checked"></span>
								@else
									<span class="fa fa-star "></span>
								@endif

							@elseif($hotel->rate == 4)
								@if($i<=3)
									<span class="fa fa-star checked"></span>
								@else
									<span class="fa fa-star "></span>
								@endif
				  			@elseif($hotel->rate == 5)
								@if($i<=3)
									<span class="fa fa-star checked"></span>
								@else
									<span class="fa fa-star "></span>	
								@endif
					
							@elseif($hotel->rate == 6)
								@if($i<=4)
									<span class="fa fa-star checked"></span>
								@else
									<span class="fa fa-star "></span>
								@endif
			  				@elseif($hotel->rate == 7)
								@if($i<=4)
									<span class="fa fa-star checked"></span>
								@else
									<span class="fa fa-star "></span>
								@endif
							
							@elseif($hotel->rate == 8)
								@if($i<=5)
									<span class="fa fa-star checked"></span>
								@else
									<span class="fa fa-star "></span>
								@endif
		  					@elseif($hotel->rate == 9)
								@if($i<=5)
									<span class="fa fa-star checked"></span>
								@else
									<span class="fa fa-star "></span>
								@endif
							
							@else	
								<span class="fa fa-star"></span>
							@endif
                        @endfor
						@if($hotel->rate % 2 != 0)
                        <span >plus</span>
						@endif
                      
                    </li>
                    @endforeach
                    @foreach($events as $event)
                    <li class="list-item">
                      <div class="d-flex align-items-center justify-content-between">
                        <h6 class="fw-semibold">{{$event->name}}{{"(Event)"}}</h6>
                        <button class="btn btn-primary btn-sm add-service-button pre-loader-func" data-link="https://dev.eetstravel.com/tour_package" data-service_type="event" data-service_id="{{$event->id}}" data-service_name="{{$event->name}}" data-tourDayId="{{$tourDayId}}"><i class="fas fa-plus"></i></button>
                      </div>
                      <p class="text"><i class="fas fa-phone me-2"></i>+10 203808 900</p>
                      <p class="text"><i class="fas fa-map-marker-alt me-2"></i>New york, USA.</p>
                    </li>
                    @endforeach
                    @foreach($guides as $guide)
                    <li class="list-item">
                      <div class="d-flex align-items-center justify-content-between">
                        <h6 class="fw-semibold">{{$guide->name}}{{"(Guide)"}}</h6>
                        <button class="btn btn-primary btn-sm add-service-button pre-loader-func" data-link="https://dev.eetstravel.com/tour_package" data-service_type="guide" data-service_id="{{$guide->id}}" data-service_name="{{$guide->name}}" data-tourDayId="{{$tourDayId}}"><i class="fas fa-plus"></i></button>
                      </div>
                      <p class="text"><i class="fas fa-phone me-2"></i>+10 203808 900</p>
                      <p class="text"><i class="fas fa-map-marker-alt me-2"></i>New york, USA.</p>
                    </li>
                    @endforeach
                    @foreach($restaurants as $restaurant)
                    <li class="list-item">
                      <div class="d-flex align-items-center justify-content-between">
                        <h6 class="fw-semibold">{{$restaurant->name}}{{"(Restaurant)"}}</h6>
                        <button class="btn btn-primary btn-sm add-service-button pre-loader-func" data-link="https://dev.eetstravel.com/tour_package" data-service_type="restaurant" data-service_id="{{$restaurant->id}}" data-service_name="{{$restaurant->name}}" data-tourDayId="{{$tourDayId}}"><i class="fas fa-plus"></i></button>
                      </div>
                      <p class="text"><i class="fas fa-phone me-2"></i>+10 203808 900</p>
                      <p class="text"><i class="fas fa-map-marker-alt me-2"></i>New york, USA.</p>
                    </li>
                    @endforeach
                  </ol>
                </div>
                
              </div>
  
            </div>
          </div>
          <div class="modal-footer border-0">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  {{--Hotel Modal Date--}}
<div class="modal fade" id="selectDateForHotelPackage" tabindex="-1" aria-labelledby='selectDateForHotelPackageLabel'>
  <div class="modal-dialog modal-lg" role='document'>
      <div class="modal-content">
          <div class="modal-header">
              <button type="button" class="close" data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span>
              </button>
              <h4 class="modal-title">{!!trans('main.SelectDate')!!}</h4>
          </div>
          <div class="box box-body" style="border-top: none">

              <div class="alert alert-info error_date" style="text-align: center; display: none;">

              </div>

              <div class="form-group">

                  <label for="departure_date">{!!trans('main.DateFrom')!!}</label>

                  <div class="input-group date">
                      <div class="input-group-addon">
                          <i class="fa fa-calendar"></i>
                      </div>
                      
                        <input type="text" name="date_service_package" id="date_service_package" class="form-control pull-right datepickerDisabledHotelPackage">
                  </div>

              </div>

              <div class="form-group">

                  <label for="departure_date">{!!trans('main.Dateto')!!}</label>

                  <div class="input-group date">
                      <div class="input-group-addon">
                          <i class="fa fa-calendar"></i>
                      </div>
                      
                      <input type="text" name="date_service_retirement_package" id="date_service_retirement_package" class="form-control pull-right datepickerDisabledHotelPackage">
                  </div>

              </div>
              <button class="addHotelPackageWithDate pre-loader-func btn btn-success"
                      type="button">{!!trans('main.Add')!!}</button>
          </div>
      </div>
 

