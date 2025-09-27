<div class="modal fade" tabindex="-1" role='dialog' id="import-modal">
	<div class="modal-dialog" role='document'>
		<div class="modal-content">
			<div class="modal-header">
				<button class="close" type="button" data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button>
				<h4 class="modal-title">{!!trans('main.Import')!!}</h4>
			</div>
			<div class="modal-body">
				<form action="{{route('file_import')}}" method="post" id="import-form" enctype="multipart/form-data">
				{{csrf_field()}}
					<input type="file" name="attach" style="margin-bottom: 10px">
					<input type="text" name="service_name" hidden id="service_name">
					<button type="submit" class="btn btn-default">{!!trans('main.Submit')!!}</button>
				</form>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" tabindex="-1" role='dialog' id="import-modal-seasons">
	<div class="modal-dialog" role='document'>
		<div class="modal-content">
			<div class="modal-header">
				<button class="close" type="button" data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button>
				<h4 class="modal-title">{!!trans('main.ImportSeasonsPrices')!!}</h4>
			</div>
			<div class="modal-body">
				<form action="{{route('file_import_seasons')}}" method="post" id="import-form" enctype="multipart/form-data">
				{{csrf_field()}}
					<input type="file" name="attach" style="margin-bottom: 10px">	
					<input type="text" name="service_name" hidden id="service_name" value="Seasons">					
					<button type="submit" class="btn btn-default">{!!trans('main.Submit')!!}</button>
				</form>
			</div>
		</div>
	</div>
</div>