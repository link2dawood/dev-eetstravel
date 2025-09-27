<ol class="services-list">
    @foreach($hotels as $hotel)
    <li class="list-item">
      <div class="d-flex align-items-center justify-content-between">
        <h6 class="fw-semibold">{{$hotel->name}}{{"(hotel)"}}</h6>
        <button class="btn btn-primary btn-sm add-service-button pre-loader-func" data-link="https://dev.eetstravel.com/tour_package" data-service_type="hotel" data-service_id="{{$hotel->id}}" data-service_name="{{$hotel->name}}" data-tourDayId="{{$tourDayId}}"><i class="fas fa-plus"></i></button>
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
        <button class="btn btn-primary btn-sm add-service-button pre-loader-func" data-link="http://127.0.0.1:8000/tour_package" data-service_type="event" data-service_id="{{$event->id}}" data-service_name="{{$event->name}}" data-tourDayId="{{$tourDayId}}"><i class="fas fa-plus"></i></button>
      </div>
      <p class="text"><i class="fas fa-phone me-2"></i>+10 203808 900</p>
      <p class="text"><i class="fas fa-map-marker-alt me-2"></i>New york, USA.</p>
    </li>
    @endforeach
    @foreach($guides as $guide)
    <li class="list-item">
      <div class="d-flex align-items-center justify-content-between">
        <h6 class="fw-semibold">{{$guide->name}}{{"(Guide)"}}</h6>
        <button class="btn btn-primary btn-sm add-service-button pre-loader-func" data-link="http://127.0.0.1:8000/tour_package" data-service_type="guide" data-service_id="{{$guide->id}}" data-service_name="{{$guide->name}}" data-tourDayId="{{$tourDayId}}"><i class="fas fa-plus"></i></button>
      </div>
      <p class="text"><i class="fas fa-phone me-2"></i>+10 203808 900</p>
      <p class="text"><i class="fas fa-map-marker-alt me-2"></i>New york, USA.</p>
    </li>
    @endforeach
    @foreach($restaurants as $restaurant)
    <li class="list-item">
      <div class="d-flex align-items-center justify-content-between">
        <h6 class="fw-semibold">{{$restaurant->name}}{{"(Restaurant)"}}</h6>
        <button class="btn btn-primary btn-sm add-service-button pre-loader-func" data-link="http://127.0.0.1:8000/tour_package" data-service_type="restaurant" data-service_id="{{$restaurant->id}}" data-service_name="{{$restaurant->name}}" data-tourDayId="{{$tourDayId}}"><i class="fas fa-plus"></i></button>
      </div>
      <p class="text"><i class="fas fa-phone me-2"></i>+10 203808 900</p>
      <p class="text"><i class="fas fa-map-marker-alt me-2"></i>New york, USA.</p>
    </li>
    @endforeach
	@foreach($transfers as $transfers)
    <li class="list-item">
      <div class="d-flex align-items-center justify-content-between">
        <h6 class="fw-semibold">{{$transfers->name}}{{"(Bus Company)"}}</h6>
        <button class="btn btn-primary btn-sm add-service-button pre-loader-func" data-link="http://127.0.0.1:8000/tour_package" data-service_type="transfer" data-service_id="{{$transfers->id}}" data-service_name="{{$transfers->name}}" data-tourDayId="{{$tourDayId}}"><i class="fas fa-plus"></i></button>
      </div>
      <p class="text"><i class="fas fa-phone me-2"></i>+10 203808 900</p>
      <p class="text"><i class="fas fa-map-marker-alt me-2"></i>New york, USA.</p>
    </li>
    @endforeach
  </ol>