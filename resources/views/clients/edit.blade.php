@extends('scaffold-interface.layouts.app')
@section('title','Edit')
@section('content')
    @include('layouts.title',
   ['title' => 'Client', 'sub_title' => 'Client Edit',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Clients', 'icon' => 'handshake-o', 'route' => route('clients.index')],
   ['title' => 'Edit', 'route' => null]]])
    <section class="content">
        <div class="box box-primary">
            <div class="box box-body border_top_none">
				<span id="client_id_span" data-info="{{$client->id}}"></span>
                <form method='POST' action='{{route('clients.update', ['client' => $client->id])}}' enctype="multipart/form-data">
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
                    <div class="row">
                        <div class="col-md-12">
                            <div class="margin_button">
                                <a href="javascript:history.back()">
                                    <button class='btn btn-primary back_btn' type="button">{!!trans('main.Back')!!}</button>
                                </a>
                                <button class='btn btn-success' type='submit'>{!!trans('main.Edit')!!}</button>
                            </div>
                        </div>
                    </div>
                <div class="row">
                    <div class="col-md-6">

                            {{csrf_field()}}
                            {{method_field('PUT')}}
                            <div class="form-group ">
                                <label for="name">{!!trans('main.Name')!!}</label>
                                <input id="name" name="name" type="text" class="form-control"
                                       value="{{ $errors != null && count($errors) > 0 ? '' : $client->name }}{{ old('name') }}">
                            </div>
                            <div class="form-group">
                                <label for="address">{!!trans('main.Address')!!}</label>
                                <input id="address" name="address" type="text" class="form-control"
                                       value="{{ $errors != null && count($errors) > 0 ? '' : $client->address }}{{ old('address') }}">
                            </div>
						<div class="form-group">
                                    <label for="account_no">{!!trans('Account No')!!}</label>
                                    <input id="account_no" name="account_no" type="text" class="form-control" value="{{ $errors != null && count($errors) > 0 ? '' : $client->account_no }}{{old('account_no')}}">
                                </div>
								<div class="form-group">
                                    <label for="company_address">{!!trans('Company Address')!!}</label>
                                    <input id="company_address" name="company_address" type="text" class="form-control" value="{{ $errors != null && count($errors) > 0 ? '' : $client->company_address }}{{old('company_address')}}">
                                </div>
								<div class="form-group">
                                    <label for="invoice_address">{!!trans('Invoice Address')!!}</label>
                                    <input id="invoice_address" name="invoice_address" type="text" class="form-control" value="{{ $errors != null && count($errors) > 0 ? '' : $client->invoice_address }}{{old('invoice_address')}}">
                                </div>
							
                            @component('component.city_form', ['country_label' => 'country', 'country_translation' => 'main.Country', 'country_default' => $client->country,
                             'city_label' => 'city','city_translation' =>'main.City', 'city_default' => \App\Helper\CitiesHelper::getCityById($client->city)['name']])
                            @endcomponent
                            <div class="form-group">
                                <label for="work_phone">{!!trans('main.WorkPhone')!!}</label>
                                <input id="work_phone" name="work_phone" type="text" class="form-control"
                                       value="{{ $errors != null && count($errors) > 0 ? '' : $client->work_phone }}{{ old('work_phone') }}">
                            </div>
                            <div class="form-group">
                                <label for="contact_phone">{!!trans('main.ContactPhone')!!}</label>
                                <input id="contact_phone" name="contact_phone" type="text" class="form-control"
                                       value="{{ $errors != null && count($errors) > 0 ? '' : $client->contact_phone }}{{ old('contact_phone') }}">
                            </div>
                            <div class="form-group">
                                <label for="work_email">{!!trans('main.WorkEmail')!!}</label>
                                <input id="work_email" name="work_email" type="text" class="form-control"
                                       value="{{ $errors != null && count($errors) > 0 ? '' : $client->work_email }}{{ old('work_email') }}">
                            </div>
                            <div class="form-group">
                                <label for="contact_email">{!!trans('main.ContactEmail')!!}</label>
                                <input id="contact_email" name="contact_email" type="text" class="form-control"
                                       value="{{ $errors != null && count($errors) > 0 ? '' : $client->contact_email }}{{ old('contact_email') }}">
                            </div>
							<div class="form-group">
								<label for="password">{!!trans('password')!!}</label>
								<input id="password" name="password" type="password" class="form-control" >
                             </div>
                            <div class="form-group">
                                <label for="work_fax">{!!trans('main.WorkFax')!!}</label>
                                <input id="work_fax" name="work_fax" type="text" class="form-control"
                                       value="{{ $errors != null && count($errors) > 0 ? '' : $client->work_fax }}{{ old('work_fax') }}">
                            </div>
                            <div class="form-group">
                                <label>{!!trans('main.Files')!!}</label>
                                @component('component.file_upload_field')@endcomponent
                            </div>
                            <button class='btn btn-success' type='submit'>{!!trans('main.Save')!!}</button>
                        <a href="{{\App\Helper\AdminHelper::getBackButton(route('clients.index'))}}">
                            <button class='btn btn-warning' type='button'>{!!trans('main.Cancel')!!}</button>
                        </a>
                    </div>
					<div class="col-md-6">
						<div class="margin_button">
							<button class='btn btn-success' id="add_contact" type='button'>
								<i class="fa fa-plus"></i>
								{!!trans('main.AddContact')!!}
							</button>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div id="items-contacts">

								</div>
							</div>
						</div>
					</div>
                </div>
                </form>
            </div>
        </div>
    </section>

@endsection
@push('scripts')
	<script type="text/javascript">
// add new empty contact item

		 var contactItemCount = 0;
		let clientId = $('#client_id_span').attr('data-info');
		// get all items contacts at document ready
        $.ajax({
            url: '/api/getClientContacts',
            method: 'GET',
            data: {
                itemCount: contactItemCount,
                clientId: clientId
            }
        }).done((res) => {
            contactItemCount = res.count;
            $('#items-contacts').append(res.content);
        });

        $('#add_contact').on('click', function () {
            $.ajax({
                url: '/api/getItemContactView',
                method: 'GET',
                data: {
                    itemCount: contactItemCount + 1
                }
            }).done((res) => {
                contactItemCount++;
                $('#items-contacts').append(res);
            })
        });

        // delete item contact
        $(document).on('click', '#delete_contact_item', function () {
            $(this).closest('.item-contact').remove();
        });
    </script>
@endpush