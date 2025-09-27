@if(count($packages) > 0)
<div class="col-md-12">
	<table id="history-table" class="table table-condensed table-bordered table-hover">
		<thead>
			<tr>
				<th>{!!trans('main.TourName')!!}</th>
				<th>{!!trans('main.PackageName')!!}</th>
				<th>{!!trans('main.PriceperPerson')!!}</th>
				<th>{!!trans('main.TotalAmount')!!}</th>
				<th>Pax</th>
				<th>{!!trans('main.PaxFree')!!}</th>
				<th>{!!trans('main.TimeFrom')!!}</th>
				<th>{!!trans('main.TimeTo')!!}</th>
				<th>{!!trans('main.AssignedUser')!!}</th>
				<th>{!!trans('main.Created')!!}</th>
			</tr>
		</thead>
		<tbody>
			@foreach($packages as $package)
			<tr>
				<td >
{{--					@if($package->tour && !$package->tour->deleted)
					<a href="{{route('tour.show', ['tour' => $package->tour->id])}}">
					{{$package->tour->name}} </a>
					@else
						{{ $package->tour['name']  }} (deleted)
					@endif
--}}
                    @if($package->tour && !$package->tour->deleted)
                        @if($package->tour->isMyTour())
                            <a href="{{route('tour.show', ['tour' => $package->tour->id])}}">
                            {{$package->tour->name}} </a>
                        @else
                            {{$package->tour->name}} 
                        @endif
					@else
						{{ $package->tour['name']  }} (deleted)
					@endif
				</td>
				<td>{{$package->name}}</td>
				<td>{{round($package->total_amount, 2)}}</td>
				<td>{{round($package->getTotalAmountTourPackage(), 2)}}</td>
				<td>{{$package->pax}}</td>
				<td>{{$package->pax_free}}</td>
				<td>{{$package->time_from}}</td>
				<td>{{$package->time_to}}</td>
				{{-- <td>{{$package->tour->getAssignedUserName()}}</td> --}}
				<td>
					@if($package->tour)
					{{@$package->tour->showAllAssignedName()}}
					@endif
				</td>
				<td>{{$package->created_at}}</td>
			</tr>
			@endforeach
		</tbody>
	</table>
</div>
@else
	<div class="col-md-12">
		<h3>{!!trans('main.Historyisempty')!!}</h3>
	</div>
@endif
<script>
    $(function() {
        $('#history-table tr').click(function() {
            var link = $(this).find('a').attr('href');
                if (link){
                    window.location.href = link;
                }
        });
    });
</script>