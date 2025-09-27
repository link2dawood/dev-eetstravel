<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Template</title>
	    <style>
        body {
            background-color: #ecf0f5 !important;
			
        }

        .card {
            border-radius: 0px;
        }
		
		 input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            appearance: none;
            margin: 0;
			
        }
		input[type="number"]{
			width: 60px;
			height:35px;
		}
		input[type="text"],input[type="date"]{
			height:35px;
		}
		.form-control{
			
			border-radius:5px !important;
		}
		.form-select{
			font-size:12px;
		}
		.td1{
			 width :350px;
			 height 10px;
			padding: 24px;
		}
		.keys{
			font-size:20px;
			font-weight:550;
			 width :150px;
		}
		.values{
			font-size:20px;
			font-weight:500;
		}
		h1{
			font-size:60px;
		}
		.custom-file-input::-webkit-file-upload-button {
  visibility: hidden;
			width:20px;
}
.custom-file-input::before {
  content: 'Select file';
  display: inline-block;
  background: linear-gradient(top, #f9f9f9, #e3e3e3);
  border: 1px solid #999;
  border-radius: 3px;
  padding: 5px 8px;
  outline: none;
  white-space: nowrap;
  -webkit-user-select: none;
  cursor: pointer;
  text-shadow: 1px 1px #fff;
  font-weight: 700;
  font-size: 10pt;
}
.custom-file-input:hover::before {
  border-color: black;
}
.custom-file-input:active::before {
  background: -webkit-linear-gradient(top, #e3e3e3, #f9f9f9);
}


    </style>
</head>

<body>
   <div class = "ps-lg-5">
								
                            <div class="d-flex justify-content-between">
                                <div>
									 <h1> Offer Request</h1>

                                    <p class="card-title">Tour:{{ $tour_package->getTour()->name }}</p>
                                   
                                </div>
								<div>
                                     <img style="height:160px" src="{{asset('/img/eets_logo_small.jpg' )}}" />
                                    <p class="date text-end"></p>
                                </div>
                           
                            </div>
							<table class="mt-3" border= "1">
								<tr>
									<td class="td1">{{ $tour_package->name }}</td>
									<td class="td1"><b>{{ date('F j Y, h:i a', strtotime($tour_package->time_from)) }}</b></td>
								</tr>
							</table>
                            
							<table class="mt-3">
								<tr>
									<td class="keys">Supplier : </td>
									<td class="values"><b>{{ $tour_package->name }}</b></td>
								</tr>
								<tr>
									<td class="keys">Check in :</td>
									<td class="values"><b>{{ date('F j, Y', strtotime($tour_package->time_from)) }} </b></td>
								</tr>
								<tr>
									<td class="keys">Check out : </td>
									<td class="values"><b>{{ date('F j, Y', strtotime($tour_package->time_to)) }}</b></td>
								</tr>
								<tr>
									<td class="keys">Arrival :</td>
									<td class="values"><b>{{ date('h:m', strtotime($tour_package->time_from)) }} </b></td>
								</tr>
								</table>
								<br>
								<table class="mt-10" >
									@foreach ($selected_room_types as $selected_room_type)
									<tr style ="margin-top:10px">
										<td class="keys">{{ $selected_room_type->name }}:</td>
										<td class="values"><b>{{ $selected_room_type->count_room }}</b></td>
									</tr>
									@endforeach
								</table>
							</div>

    <!-- User Input Section -->
    <div class="ps-lg-5">
        <p class="mt-3" style="font-size:20px">Thanks for the Offer</p>
        <h1 class="main-title mt-5">Your  Offer</h1>
          <table>
        
        <tbody>
            @foreach($requestData as $key => $value)
			@if($key == "reference")
			<tr>
                <td>Reference No</td>
                <td>{{ is_array($value) ? json_encode($value) : $value }}</td>
            </tr>
			@endif
           @if($key == "status")
			<tr>
                <td>Status: </td>
                <td>{{ is_array($value) ? json_encode($value) : $value }}</td>
            </tr>
			@endif
			@if($key == "option_with_date")
			<tr>
                <td>Option Date: </td>
                <td>{{ is_array($value) ? json_encode($value) : $value }}</td>
            </tr>
			@endif
			@if($key == "portrage_perperson")
			<tr>
                <td>Porterage Per person</td>
                <td>{{ is_array($value) ? json_encode($value) : $value }}</td>
            </tr>
			@endif
			@if($key == "halfboard")
			<tr>
                <td>Halfboard Supp P.P:</td>
                <td>{{ is_array($value) ? json_encode($value) : $value }}</td>
            </tr>
			@endif
			@if($key == "city_tax")
			<tr>
                <td>City Tax: </td>
                <td>{{ is_array($value) ? json_encode($value) : $value }}</td>
            </tr>
			@endif
			@if($key == "halfboardMax")
			<tr>
                <td>Max per group</td>
                <td>{{ is_array($value) ? json_encode($value) : $value }}</td>
            </tr>
			@endif
			@foreach ($selected_room_types as $selected_room_type)
			@php $room_rate = "room_rate_". $selected_room_type->id; $printedRoomNames = []; @endphp
			@if (!in_array($selected_room_type->name, $printedRoomNames))
			@if($key == $room_rate)
			<tr style ="margin-top:10px">
				<td class="keys">{{ $selected_room_type->name }}:</td>
				<td>{{ is_array($value) ? json_encode($value) : $value }}</td>
			</tr>
			@endif
			@endif
			@endforeach
			@endforeach
        </tbody>
    </table>
    </div>
</body>

</html>

