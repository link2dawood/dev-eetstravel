{{-- @extends('scaffold-interface.layouts.app') --}}
{{-- @section('content') --}}
<div class="box box-primary">
	<div class="box-body">
		<section class="content">
			<div class="row">
				<div class="col-sm-4" style="margin-bottom: 20px">
                    <label for="type">{!!trans('main.ServiceType')!!}</label>
                    <select class="form-control" id="tour-package-service-type">
                    	@foreach($serviceTypes as $key => $value)
						<option value="{{$key}}">{{$value}}</option>
                    	@endforeach
                    </select>
                </div>
			</div>

			<!-- Hotels Table -->
			<div id="hotel-table" class="service-table" style="display: block;">
				<table class="table table-striped table-bordered table-hover">
					<thead>
						<tr>
							<th>Id</th>
							<th>Name</th>
							<th>Address</th>
							<th>City</th>
							<th>Country</th>
							<th>Work Phone</th>
							<th>Contact Email</th>
							<th>Select</th>
						</tr>
					</thead>
					<tbody>
						@foreach($servicesData['hotel'] ?? [] as $hotel)
						<tr>
							<td>{{ $hotel->id }}</td>
							<td>{{ $hotel->name ?? '' }}</td>
							<td>{{ $hotel->address_first ?? '' }}</td>
							<td>{{ $hotel->city ?? '' }}</td>
							<td>{{ $hotel->country ?? '' }}</td>
							<td>{{ $hotel->work_phone ?? '' }}</td>
							<td>{{ $hotel->contact_email ?? '' }}</td>
							<td>
								<button class="btn btn-primary btn-sm tour_package_select_button"
									data-id="{{ $hotel->id }}"
									data-name="{{ $hotel->name }}">
									Select
								</button>
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>

			<!-- Transfer Table -->
			<div id="transfer-table" class="service-table" style="display: none;">
				<table class="table table-striped table-bordered table-hover">
					<thead>
						<tr>
							<th>Id</th>
							<th>Name</th>
							<th>Address</th>
							<th>Country</th>
							<th>City</th>
							<th>Phone</th>
							<th>Contact</th>
							<th>Select</th>
						</tr>
					</thead>
					<tbody>
						@foreach($servicesData['transfer'] ?? [] as $transfer)
						<tr>
							<td>{{ $transfer->id }}</td>
							<td>{{ $transfer->name ?? '' }}</td>
							<td>{{ $transfer->address_first ?? '' }}</td>
							<td>{{ $transfer->country ?? '' }}</td>
							<td>{{ $transfer->city ?? '' }}</td>
							<td>{{ $transfer->work_phone ?? '' }}</td>
							<td>{{ $transfer->contact_name ?? '' }}</td>
							<td>
								<button class="btn btn-primary btn-sm tour_package_select_button"
									data-id="{{ $transfer->id }}"
									data-name="{{ $transfer->name }}">
									Select
								</button>
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>

			<!-- Flight Table -->
			<div id="flight-table" class="service-table" style="display: none;">
				<table class="table table-striped table-bordered table-hover">
					<thead>
						<tr>
							<th>Id</th>
							<th>Name</th>
							<th>Date From</th>
							<th>Date To</th>
							<th>Country From</th>
							<th>City From</th>
							<th>Country To</th>
							<th>City To</th>
							<th>Select</th>
						</tr>
					</thead>
					<tbody>
						@foreach($servicesData['flight'] ?? [] as $flight)
						<tr>
							<td>{{ $flight->id }}</td>
							<td>{{ $flight->name ?? '' }}</td>
							<td>{{ $flight->date_from ?? '' }}</td>
							<td>{{ $flight->date_to ?? '' }}</td>
							<td>{{ $flight->country_from ?? '' }}</td>
							<td>{{ $flight->city_from ?? '' }}</td>
							<td>{{ $flight->country_to ?? '' }}</td>
							<td>{{ $flight->city_to ?? '' }}</td>
							<td>
								<button class="btn btn-primary btn-sm tour_package_select_button"
									data-id="{{ $flight->id }}"
									data-name="{{ $flight->name }}">
									Select
								</button>
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>

			<!-- Event Table -->
			<div id="event-table" class="service-table" style="display: none;">
				<table class="table table-striped table-bordered table-hover">
					<thead>
						<tr>
							<th>Id</th>
							<th>Name</th>
							<th>Address</th>
							<th>Country</th>
							<th>City</th>
							<th>Phone</th>
							<th>Contact Email</th>
							<th>Select</th>
						</tr>
					</thead>
					<tbody>
						@foreach($servicesData['event'] ?? [] as $event)
						<tr>
							<td>{{ $event->id }}</td>
							<td>{{ $event->name ?? '' }}</td>
							<td>{{ $event->address_first ?? '' }}</td>
							<td>{{ $event->country ?? '' }}</td>
							<td>{{ $event->city ?? '' }}</td>
							<td>{{ $event->work_phone ?? '' }}</td>
							<td>{{ $event->contact_email ?? '' }}</td>
							<td>
								<button class="btn btn-primary btn-sm tour_package_select_button"
									data-id="{{ $event->id }}"
									data-name="{{ $event->name }}">
									Select
								</button>
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>

			<!-- Guide Table -->
			<div id="guide-table" class="service-table" style="display: none;">
				<table class="table table-striped table-bordered table-hover">
					<thead>
						<tr>
							<th>Id</th>
							<th>Name</th>
							<th>Address</th>
							<th>Country</th>
							<th>City</th>
							<th>Phone</th>
							<th>Contact</th>
							<th>Select</th>
						</tr>
					</thead>
					<tbody>
						@foreach($servicesData['guide'] ?? [] as $guide)
						<tr>
							<td>{{ $guide->id }}</td>
							<td>{{ $guide->name ?? '' }}</td>
							<td>{{ $guide->address_first ?? '' }}</td>
							<td>{{ $guide->country ?? '' }}</td>
							<td>{{ $guide->city ?? '' }}</td>
							<td>{{ $guide->work_phone ?? '' }}</td>
							<td>{{ $guide->contact_name ?? '' }}</td>
							<td>
								<button class="btn btn-primary btn-sm tour_package_select_button"
									data-id="{{ $guide->id }}"
									data-name="{{ $guide->name }}">
									Select
								</button>
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>

			<!-- Restaurant Table -->
			<div id="restaurant-table" class="service-table" style="display: none;">
				<table class="table table-striped table-bordered table-hover">
					<thead>
						<tr>
							<th>Id</th>
							<th>Name</th>
							<th>Address</th>
							<th>City</th>
							<th>Country</th>
							<th>Phone</th>
							<th>Email</th>
							<th>Select</th>
						</tr>
					</thead>
					<tbody>
						@foreach($servicesData['restaurant'] ?? [] as $restaurant)
						<tr>
							<td>{{ $restaurant->id }}</td>
							<td>{{ $restaurant->name ?? '' }}</td>
							<td>{{ $restaurant->address_first ?? '' }}</td>
							<td>{{ $restaurant->city ?? '' }}</td>
							<td>{{ $restaurant->country ?? '' }}</td>
							<td>{{ $restaurant->work_phone ?? '' }}</td>
							<td>{{ $restaurant->contact_email ?? '' }}</td>
							<td>
								<button class="btn btn-primary btn-sm tour_package_select_button"
									data-id="{{ $restaurant->id }}"
									data-name="{{ $restaurant->name }}">
									Select
								</button>
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>

			<!-- Cruise Table -->
			<div id="cruise-table" class="service-table" style="display: none;">
				<table class="table table-striped table-bordered table-hover">
					<thead>
						<tr>
							<th>Id</th>
							<th>Name</th>
							<th>Date From</th>
							<th>Date To</th>
							<th>Country From</th>
							<th>City From</th>
							<th>Country To</th>
							<th>City To</th>
							<th>Select</th>
						</tr>
					</thead>
					<tbody>
						@foreach($servicesData['cruise'] ?? [] as $cruise)
						<tr>
							<td>{{ $cruise->id }}</td>
							<td>{{ $cruise->name ?? '' }}</td>
							<td>{{ $cruise->date_from ?? '' }}</td>
							<td>{{ $cruise->date_to ?? '' }}</td>
							<td>{{ $cruise->country_from ?? '' }}</td>
							<td>{{ $cruise->city_from ?? '' }}</td>
							<td>{{ $cruise->country_to ?? '' }}</td>
							<td>{{ $cruise->city_to ?? '' }}</td>
							<td>
								<button class="btn btn-primary btn-sm tour_package_select_button"
									data-id="{{ $cruise->id }}"
									data-name="{{ $cruise->name }}">
									Select
								</button>
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>

			<!-- Tour Package Table -->
			<div id="tourPackage-table" class="service-table" style="display: none;">
				<table class="table table-striped table-bordered table-hover">
					<thead>
						<tr>
							<th>Id</th>
							<th>Name</th>
							<th>Start Date</th>
							<th>End Date</th>
							<th>Country Begin</th>
							<th>City Begin</th>
							<th>Assigned User</th>
							<th>Status</th>
							<th>Select</th>
						</tr>
					</thead>
					<tbody>
						@foreach($servicesData['tourPackage'] ?? [] as $tour)
						<tr>
							<td>{{ $tour->id }}</td>
							<td>{{ $tour->name ?? '' }}</td>
							<td>{{ $tour->departure_date ?? '' }}</td>
							<td>{{ $tour->retirement_date ?? '' }}</td>
							<td>{{ $tour->country_begin ?? '' }}</td>
							<td>{{ $tour->city_begin ?? '' }}</td>
							<td>{{ $tour->assigned_user ?? '' }}</td>
							<td>{{ $tour->status ?? '' }}</td>
							<td>
								<button class="btn btn-primary btn-sm tour_package_select_button"
									data-id="{{ $tour->id }}"
									data-name="{{ $tour->name }}">
									Select
								</button>
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>

		</section>
	</div>
</div>
{{-- @endsection --}}
@push('scripts')
<script type="text/javascript">
$(document).ready(function() {
    // Service type mapping
    const serviceTypeMapping = {
        '0': 'hotel',
        '1': 'event',
        '2': 'guide',
        '3': 'transfer',
        '4': 'restaurant',
        '5': 'tourPackage',
        '6': 'cruise',
        '7': 'flight'
    };

    // Show the first service table by default (hotel)
    showServiceTable('hotel');

    // Handle service type dropdown change
    $('#tour-package-service-type').on('change', function(){
        const selectedValue = $(this).val();
        const serviceType = serviceTypeMapping[selectedValue];
        showServiceTable(serviceType);
    });

    // Handle service selection
    $('body').on('click', '.tour_package_select_button', function(){
        const id = this.getAttribute('data-id');
        const name = this.getAttribute('data-name');
        const select = document.getElementById('tour-package-service-type');
        const value = select.options[select.selectedIndex].text;

        // Set values in hidden fields (if they exist)
        if (document.getElementById('tour_package_service_type_value')) {
            document.getElementById('tour_package_service_type_value').value = value;
        }
        if (document.getElementById('tour_package_service_type_id')) {
            document.getElementById('tour_package_service_type_id').value = id;
        }
        if (document.getElementById('service_name')) {
            document.getElementById('service_name').textContent = `${value} - ${name}`;
        }
    });

    function showServiceTable(serviceType) {
        // Hide all service tables
        $('.service-table').hide();

        // Show the selected service table
        $('#' + serviceType + '-table').show();
    }
});
</script>
@endpush