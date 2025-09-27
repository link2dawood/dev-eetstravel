<!DOCTYPE html>
<html lang="en">
    @include('TMSClient.layout.head')

<body>
  <div class="main">
    <div class="main-content">
      <section class="tour-create" id="tourCreate">
        <div class="container">
          <div class="row">
            <div class="col-lg-7">
              <div class="card">
                <div class="card-body">
                  <form id="tourCreateForm" action="{{route('TMS-Client-tours.store')}}" method = "POST" class="tab-wizard wizard-circle wizard clearfix">


                {{csrf_field()}}

                    <h6>Basic Info</h6>
                    <formstep>
                      <div class="row">
                        <div class="col-md-6">
                          <div class="input-wrapper">
                            <input id="" type="text" class="form-control " placeholder="Tour Name" class="required error"  name = "name" >
                          </div>
                        </div>
                        <div class="col-md-6">
                          	<div class="input-wrapper">
                        		<input id="" type="number" class="form-control " placeholder="PAX"  name = "pax" >
                      		</div>
                        </div>
                      </div>
                      
                      <div class="input-wrapper mb-4">
                        <input id="" type="number" class="form-control " placeholder="PAX FREE" name = "pax_free"  >
                      </div>
                    </formstep>

                    <h6>Tour Basic</h6>
                    <formstep>
                      <div class="row">
                        <div class="col-lg-6">
                          <div class="input-wrapper">
                            <lable class="form-label" for="tourFrom">Country From</lable>
                            <select  class="form-select form-control"  name="country_begin" id="country_begin">
                              @foreach($countries as $country)
                              
                              @if( $country->name == "United States")
                             
                                <option  value = {{$country->alias}} selected>{{$country->name}}</option>
                              @else
                                <option value = {{$country->alias}}>{{$country->name}}</option>
                                @endif
                              @endforeach
                            </select>
                          </div>
                        </div>
                        <div class="col-lg-6">
                          <div class="input-wrapper">
                            <lable class="form-label" for="tourFrom">City From</lable>
                            <select class="form-select form-control" name="city_begin" id="city_begin">
                              @foreach($cities as $city)
                              
                              @if( $city->name == "Dallas")
                             
                                <option value = {{$city->id}} selected>{{$city->name}}</option>
                              @else
                                <option value = {{$city->id}} >{{$city->name}}</option>
                                @endif
                              @endforeach
                            </select>
                          </div>
                        </div>
                        <div class="col-lg-6"></div>
                      </div>
                      <div class="row">
                        <div class="col-lg-6">
                          <div class="input-wrapper">
                            <lable class="form-label" for="tourFrom">Country To</lable>
                            
                            <select name="country_end" id="country_end" class="form-select form-control" >
                              @foreach($countries as $country)
                              
                              @if( $country->name == "United States")
                             
                                <option  value = {{$country->alias}} selected>{{$country->name}}</option>
                              @else
                                <option  value = {{$country->alias}}>{{$country->name}}</option>
                                @endif
                              @endforeach
                            </select>
                            
                          </div>
                        </div>
                        
                        <div class="col-lg-6">
                          <div class="input-wrapper">
                            <lable class="form-label" for="tourFrom" >City To</lable>
                            <select name="city_end" id="city_end" class="form-select form-control" >
                             
                                @foreach($cities as $city)
                                
                                @if( $country->name == "United States")
                               
                                  <option selected>{{$city->name}}</option>
                                @else
                                  <option>{{$city->name}}</option>
                                  @endif
                                @endforeach
                              
                            </select>
                          </div>
                        </div>
                        <div class="col-lg-6"></div>
                      </div>
                      
                      <div class="input-wrapper mb-4">
                        <input type="text" class="form-control" name="phone" placeholder="What is the best way to contact you?">
                      </div>
                    </formstep>

                    <h6>Create Tour</h6>
                    <formstep>

                      <div class="row">
                        <div class="col-lg-6">
                          <div class="input-wrapper">
                            <label class="form-label" for="tourFrom">Dep Date:</label>
                            <input type="date" class="form-control" id="tourFrom" name="departure_date">
                          </div>
                        </div>
                        <div class="col-lg-6">
                          <div class="input-wrapper">
                            <label class="form-label" for="tourFrom">Ret Date:</label>
                            <input type="date" class="form-control" id="tourTo" name="retirement_date">
                          </div>
                        </div>
                        <div class="col-lg-6"></div>
                      </div>
                      <div class="input-wrapper">
                        {{--<button type="button" class="btn btn-success btn_for_select_room_type">Room Types</button>--}}
                        <ul class="list_room_types" style="display:none">
                          <ul class="list_room_types" style="z-index:999; position: absolute;
                          display: none;
                          width: 500px;
                          margin-top: 5px; padding: 0;
    margin: 0;
    list-style-type: none;
    background-color: #fff;
    border: 1px solid #d2d6de;display: block; ">
                              @foreach( $room_types as $room_type)
                                  <li class="select_room_type" style="display: block;
                                  border-bottom: 1px solid #d2d6de;
                                  cursor: pointer;">
                                      <label>{{ $room_type->name }}</label>
                                      <input type="text" data-info="{{ $room_type->id }}" hidden value="{{ $room_type }}">
                                  </li>
                              @endforeach
                          </ul>
                      </ul>
                      </div>
                    </br>
                    

                      

          
                    </formstep>
                    
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>


  @include('TMSClient.layout.footer')
  <script type="text/javascript" src='{{asset('js/rooms.js')}}'></script>
<script>

// $("#tourCreateForm").on("submit", function(event) {
//   event.preventDefault();
//   alert("ok");
//   var formData = $(this).serialize();

//     $.ajax({
//       url: $(this).attr("action"),
//       type: "POST",
//       data: formData,
//       success: function(response) {
//         // Handle success response
//       },
//       error: function(xhr, status, error) {
//         // Handle error
//       }
//     });
//   // Perform form validation or data processing
//   // ...
//   // Submit the form using AJAX if needed
// });
	
	
</script>
</body>

</html>