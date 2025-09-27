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