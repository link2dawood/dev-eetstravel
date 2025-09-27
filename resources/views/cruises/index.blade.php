@extends('scaffold-interface.layouts.app')
@section('title','Index')
@section('content')
	@include('layouts.title',
   ['title' => 'Cruises', 'sub_title' => 'Cruises List',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Cruises', 'icon' => 'ship', 'route' => null]]])
	<section class="content">
		<div class="box box-primary">
			<div class="box-body">
                @if (Session::has('message'))
                    <div class="alert alert-danger"><center>{{ Session::get('message') }}</center></div>
                @endif
					<div>
						{!! \App\Helper\PermissionHelper::getCreateButton(route('cruises.create'), \App\Cruises::class) !!}
					</div>
				@if(session('export_all'))
                <div class="alert alert-info col-md-12" style="text-align: center;">
                    {{session('export_all')}}
                </div>
                @endif
				<br>
				<br>
				<table id="cruise-table" class="table table-striped table-bordered table-hover" style='background:#fff; width: 98%; table-layout: fixed'>
					<thead>
					<tr>
						<th>Id</th>
						<th>{!!trans('main.Name')!!}</th>
						<th>{!!trans('main.Datefrom')!!}</th>
						<th>{!!trans('main.Dateto')!!}</th>
						<th>{!!trans('main.CountryFrom')!!}</th>
						<th>{!!trans('main.Cityfrom')!!}</th>
						<th class="actions-button" style="width: 140px">{!!trans('main.Actions')!!}</th>
					</tr>
					</thead>

					<tfoot>
					<tr>
						<th class="not"></th>
						<th>{!!trans('main.Name')!!}</th>
						<th>{!!trans('main.Datefrom')!!}</th>
						<th>{!!trans('main.Dateto')!!}</th>
						<th>{!!trans('main.CountryFrom')!!}</th>
						<th>{!!trans('main.Cityfrom')!!}</th>
						<th class="not" style="width: 140px"></th>
					</tr>
					</tfoot>
				</table>
			</div>
		</div>
	</section>
<span id="service-name" hidden data-service-name='Cruises'></span>
@endsection

@push('scripts')

<script>
	$(document).ready(function() {
		let table = $('#cruise-table').DataTable({
			dom: 	"<'row'<'col-sm-4'l><'col-sm-3'B><'col-sm-5'f>>" +
					"<'row'<'col-sm-12'tr>>" +
					"<'row'<'col-sm-5'i><'col-sm-7'p>>",
			buttons: [
				{
					extend: 'csv',
                    title: 'Cruises List',
					exportOptions: {
						columns: ':not(.actions-button)'
					}
				},
				{
					extend: 'excel',
                    title: 'Cruises List',
					exportOptions: {
						columns: ':not(.actions-button)'
					}
				},
				{
					extend: 'pdfHtml5',
                    title: 'Cruises List',
					exportOptions: {
						columns: ':not(.actions-button)'
					}
				},
				{
					text: 'Import',
					action: () => {
						getModalForImport();
					}
				},
				{
					extend: 'collection',
					text: 'Export All',
					buttons: [
						{
							text: 'Pdf',
							action: () => {
								exportAll('pdf');
							}
						},
						{
							text: 'Excel',
							action: () => {
								exportAll('xlsx');
							}
						},
						{
							text: 'Csv',
							action: () => {
								exportAll('csv');
							}
						}
					],
                    autoClose: true
				}
			],
            language : {
                search: "Global Search :"
            },
			processing: true,
			serverSide: true,
            pageLength: 50,
			ajax: {
				url: "{{route('cruises_data')}}"
			},
			columns: [
				{data: 'id', name: 'cruises.id'},
				{data: 'name', name: 'cruises.name'},
				{data: 'date_from', name: 'cruises.date_from'},
				{data: 'date_to', name: 'cruises.date_to'},
				{data: 'country_from', name: 'countries_from.name'},
				{data: 'city_from', name: 'cities_from.name'},
				{data: 'action', name: 'action', searchable: false, sorting: false, orderable: false}
			],
		});
		$('#cruise-table tfoot th').each(function() {
			let column = this;
			if(column.className !== 'not') {
				let title = $(this).text();
				$(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');
			}
		});
		table.columns().every(function() {
			let that = this;

			$('input', this.footer()).on('keyup change', function() {
				if(that.search() !== this.value) {
					that.search(this.value).draw();
				}
			});
		});
		$('#cruise-table tfoot th').appendTo('#cruise-table thead');
        $('#search-form').on('submit', function(e) {
            table.draw();
            e.preventDefault();
        });
	});
</script>
@endpush
