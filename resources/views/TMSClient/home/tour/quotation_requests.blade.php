<!DOCTYPE html>
<html lang="en">
 @include('TMSClient.layout.head')
<body>
  <div class="main">
    @include('TMSClient.layout.nav')
    <div class="main-content" style="margin-top: 64px;">
      <section class="tours-archive">
        <div class="container">
          <div class="d-flex justify-content-between align-items-start">
            <h1 class="title">Your Requests</h1>
            <a href="{{url('TMS-Client-tours/create')}}" class="btn btn-primary">Add New</a>
          </div>
          <div class="card">
	      	<table id="tour-table" class="table table-responsive">
              <thead>
                <tr>
                  <th>Id</th>
                  <th>{!!trans('main.Name')!!}</th>
                  <th>{!!trans('main.Depdate')!!}</th>
                  <th>{!!trans('main.Status')!!}</th>
				  <th>{!!trans('main.ExternalName')!!}</th>
                 <th class="actions-button" style="width:140px; text-align: center;">{!!trans('main.Actions')!!}</th>
                </tr>
              </thead>
              <tbody>
                @forelse($toursData as $tour)
                <tr>
                  <td>{{ $tour->id }}</td>
                  <td>
                    <h6 class="fw-bold mb-0">{{ $tour->name }}</h6>
                    <p class="text mb-0">{{ $tour->formatted_departure_date }} - {{ $tour->retirement_date ? \Carbon\Carbon::parse($tour->retirement_date)->format('Y-m-d') : '' }}</p>
                  </td>
                  <td>{{ $tour->pax }}</td>
                  <td>
                    <div class="{{ $tour->status_class }}">
                      {{ $tour->status_name }}
                    </div>
                  </td>
                  <td>{{ $tour->external_name }}</td>
                  <td>
                    {!! $tour->action_buttons !!}
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="6" class="text-center">No quotation requests found</td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </section>
    </div>
  </div>



  <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
 @include('TMSClient.layout.footer')
	
<script>
    $(document).ready(function() {
        // Simple table functionality without DataTable
        console.log('TMSClient Tour Quotation Requests table loaded with direct controller data');
    });
</script>
</body>

</html>