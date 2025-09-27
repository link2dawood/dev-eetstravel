@extends('scaffold-interface.layouts.app')
@section('content')
    @include('layouts.title',
   ['title' => 'Role', 'sub_title' => 'Role Edit',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Roles', 'icon' => 'user', 'route' => url('roles')],
   ['title' => 'Edit', 'route' => null]]])
   <style>
	.select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    background: #f5f5f5!important;
    border: none;
    border-right: 1px solid #aaa;
    border-top-left-radius: 4px;
    border-bottom-left-radius: 4px;
    color: #999;
    cursor: pointer;
    font-size: 1em;
    font-weight: bold;
    padding: 0 4px;
    position: relative!important;
    left: -5px!important;
    top: 0!important;
}
.select2-container--default .select2-search--inline .select2-search__field {
	position: absolute;
}
   </style>
<section class="content">
	<div class="box box-primary">

        @if (count($errors) > 0)
            <br>
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

		<div class="box box-body border_top_none">
			<form action="{{url('roles/update')}}" method = "post">
                <div class="row">
                    <div class="col-md-12">
                        <div class="margin_button">
                            <a href="javascript:history.back()">
                                <button class='btn btn-primary back_btn' type="button">{{trans('main.Back')}}</button>
                            </a>
                            <button class='btn btn-success' type='submit'>{{trans('main.Save')}}</button>
                        </div>
                    </div>
                </div>
				{!! csrf_field() !!}
				<input type="hidden" name = "role_id" value = "{{$role->id}}">
				<div class="form-group">
				<label for="">Role</label>
					<input type="text" name = "name" class = "form-control" placeholder = "Name" value = "{{$role->name}}">
				</div>
					<button class = 'btn btn-success' type = "submit">{{trans('main.Save')}}</button>
                    <a href="{{\App\Helper\AdminHelper::getBackButton(route('roles.index'))}}">
                        <button class='btn btn-warning' type='button'>{{trans('main.Cancel')}}</button>
                    </a>
			</form>
		</div>
	</div>
    <div class="row">
        <div class="col-md-6">
            <div class="box box-primary">
                <div class="box-header">
                    <h3>{{$role->name}} {{trans('main.Permissions')}}</h3>
                </div>
                <div class="box-body">
                    <form action="{{url('roles/addPermission')}}" method = "post">
                        {!! csrf_field() !!}
                        <input type="hidden" name = "role_id" value = "{{$role->id}}">
                        <div class="form-group">
                            <select name="permission_name[]" id="" class = "js-state form-control select22"
                                    multiple="multiple">
                                @foreach($permissions as $key => $permission)
                                    <option value="{{$key}}">{{$permission}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <button class = 'btn btn-primary'>{{trans('main.Addpermission')}}</button>
                        </div>
                    </form>
                    <table class = 'table'>
                        <thead>
                        <th>{{trans('main.Permission')}}</th>
                        <th>{{trans('main.Action')}}</th>
                        </thead>
                        <tbody>
                        @foreach($rolePermissions as $key => $permission)
                            <tr>
                                <td>{{$permission}}</td>
                                <td><a href="{{url('roles/removePermission')}}/{{$key}}/{{$role->id}}" class = "btn btn-danger btn-sm"><i class="fa fa-trash-o" aria-hidden="true"></i></a></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
	$(document).ready(function() {
		$('.select22').select2({
			placeholder: "Select permissions",
			allowClear: true
		});
	});
</script>
@endsection

