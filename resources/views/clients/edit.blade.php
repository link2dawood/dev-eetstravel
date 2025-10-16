@extends('scaffold-interface.layouts.tabler-app')
@section('title','Edit Client')
@section('content')
    <!-- Page header -->
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/home') }}"><i class="ti ti-home"></i> Home</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('clients.index') }}">Clients</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Edit</li>
                            </ol>
                        </nav>
                    </div>
                    <h2 class="page-title">Edit Client</h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="javascript:history.back()" class="btn btn-primary">
                            <i class="ti ti-arrow-left"></i> {!!trans('main.Back')!!}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Page body -->
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <span id="client_id_span" data-info="{{$client->id}}"></span>
                            @if (count($errors) > 0)
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <form method='POST' action='{{route('clients.update', ['client' => $client->id])}}' enctype="multipart/form-data">
                                {{csrf_field()}}
                                {{method_field('PUT')}}

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="name" class="form-label">{!!trans('main.Name')!!}</label>
                                            <input id="name" name="name" type="text" class="form-control"
                                                   value="{{ $errors != null && count($errors) > 0 ? '' : $client->name }}{{ old('name') }}">
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="address" class="form-label">{!!trans('main.Address')!!}</label>
                                            <input id="address" name="address" type="text" class="form-control"
                                                   value="{{ $errors != null && count($errors) > 0 ? '' : $client->address }}{{ old('address') }}">
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="account_no" class="form-label">{!!trans('Account No')!!}</label>
                                            <input id="account_no" name="account_no" type="text" class="form-control" value="{{ $errors != null && count($errors) > 0 ? '' : $client->account_no }}{{old('account_no')}}">
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="company_address" class="form-label">{!!trans('Company Address')!!}</label>
                                            <input id="company_address" name="company_address" type="text" class="form-control" value="{{ $errors != null && count($errors) > 0 ? '' : $client->company_address }}{{old('company_address')}}">
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="invoice_address" class="form-label">{!!trans('Invoice Address')!!}</label>
                                            <input id="invoice_address" name="invoice_address" type="text" class="form-control" value="{{ $errors != null && count($errors) > 0 ? '' : $client->invoice_address }}{{old('invoice_address')}}">
                                        </div>

                                        @component('component.city_form', ['country_label' => 'country', 'country_translation' => 'main.Country', 'country_default' => $client->country,
                                         'city_label' => 'city','city_translation' =>'main.City', 'city_default' => \App\Helper\CitiesHelper::getCityById($client->city)['name']])
                                        @endcomponent

                                        <div class="form-group mb-3">
                                            <label for="work_phone" class="form-label">{!!trans('main.WorkPhone')!!}</label>
                                            <input id="work_phone" name="work_phone" type="text" class="form-control"
                                                   value="{{ $errors != null && count($errors) > 0 ? '' : $client->work_phone }}{{ old('work_phone') }}">
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="contact_phone" class="form-label">{!!trans('main.ContactPhone')!!}</label>
                                            <input id="contact_phone" name="contact_phone" type="text" class="form-control"
                                                   value="{{ $errors != null && count($errors) > 0 ? '' : $client->contact_phone }}{{ old('contact_phone') }}">
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="work_email" class="form-label">{!!trans('main.WorkEmail')!!}</label>
                                            <input id="work_email" name="work_email" type="text" class="form-control"
                                                   value="{{ $errors != null && count($errors) > 0 ? '' : $client->work_email }}{{ old('work_email') }}">
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="contact_email" class="form-label">{!!trans('main.ContactEmail')!!}</label>
                                            <input id="contact_email" name="contact_email" type="text" class="form-control"
                                                   value="{{ $errors != null && count($errors) > 0 ? '' : $client->contact_email }}{{ old('contact_email') }}">
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="password" class="form-label">{!!trans('password')!!}</label>
                                            <input id="password" name="password" type="password" class="form-control" >
                                         </div>

                                        <div class="form-group mb-3">
                                            <label for="work_fax" class="form-label">{!!trans('main.WorkFax')!!}</label>
                                            <input id="work_fax" name="work_fax" type="text" class="form-control"
                                                   value="{{ $errors != null && count($errors) > 0 ? '' : $client->work_fax }}{{ old('work_fax') }}">
                                        </div>

                                        <div class="form-group mb-3">
                                            <label class="form-label">{!!trans('main.Files')!!}</label>
                                            @component('component.file_upload_field')@endcomponent
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <button class='btn btn-success' id="add_contact" type='button'>
                                                <i class="ti ti-plus"></i>
                                                {!!trans('main.AddContact')!!}
                                            </button>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div id="items-contacts">

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-footer text-end">
                                    <button class='btn btn-success' type='submit'>
                                        <i class="ti ti-device-floppy"></i> {!!trans('main.Save')!!}
                                    </button>
                                    <a href="{{\App\Helper\AdminHelper::getBackButton(route('clients.index'))}}">
                                        <button class='btn btn-warning' type='button'>
                                            <i class="ti ti-x"></i> {!!trans('main.Cancel')!!}
                                        </button>
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
