<!DOCTYPE html>
<html lang="en">
 @include('TMSClient.layout.supplier_head')
<body>
  <div class="main">
    @include('TMSClient.layout.supplier_nav')
    <div class="main-content" style="margin-top: 64px;">
      <section class="tours-archive">
        <div class="container">
          <div class="d-flex justify-content-between align-items-start">
            <h1 class="title">Your Offers</h1>

          </div>
          <div class="card">
	      	<table class="table table-striped table-responsive">
              <thead>
                <tr>
                  <th>Id</th>
                  <th>{!!trans('Tour Name')!!}</th>
                  <th>{!!trans('Offer')!!}</th>
                  <th>{!!trans('Paid')!!}</th>
				  <th>{!!trans('Total Amount')!!}</th>
					<th>{!!trans('Status')!!}</th>
					<th>{!!trans('Reference')!!}</th>
                 <th class="actions-button" style="width:140px; text-align: center;">{!!trans('main.Actions')!!}</th>
                </tr>
              </thead>
              <tbody>
                @foreach($offers ?? [] as $offer)
                <tr>
                  <td>{{ $offer->id }}</td>
                  <td>{{ $offer->tourName ?? '' }}</td>
                  <td class="touredit-departure_date">
                    <span class="status {{ $offer->is_expired ? 'active' : 'rejected' }}">
                      {{ $offer->is_expired ? 'Yes' : 'NO' }}
                    </span>
                  </td>
                  <td class="paid">
                    <span class="status {{ $offer->paid ? 'active' : 'rejected' }}">
                      {{ $offer->paid ? 'Yes' : 'NO' }}
                    </span>
                  </td>
                  <td>{{ $offer->total_amount ?? '' }}</td>
                  <td>{{ $offer->statusName ?? '' }}</td>
                  <td>{{ $offer->reference ?? '' }}</td>
                  <td>
                    @if($offer->supplier_url)
                    <div class="d-flex align-items-center gap-2">
                      <a href="{{ $offer->supplier_url }}" class="action-link btn-primary">
                        <i class="fas fa-eye"></i>
                      </a>
                    </div>
                    @endif
                  </td>
                </tr>
                @endforeach
                @if(empty($offers))
                <tr>
                  <td colspan="8" class="text-center">No offers found</td>
                </tr>
                @endif
              </tbody>
          </table>
          </div>
        </div>
      </section>
    </div>
  </div>



  <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
 @include('TMSClient.layout.footer')
	
</body>

</html>