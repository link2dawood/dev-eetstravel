@extends('scaffold-interface.layouts.tabler-app')
@section('title','Create Hotel')
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
                                <li class="breadcrumb-item"><a href="{{ route('hotel.index') }}">Hotels</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Create</li>
                            </ol>
                        </nav>
                            </div>
                    <h2 class="page-title">Create Hotel</h2>
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
                    @if (count($errors) > 0)
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            <div class="d-flex">
                                <div>
                                    <i class="ti ti-alert-circle icon alert-icon"></i>
                                </div>
                                <div>
                                    <h4 class="alert-title">Error!</h4>
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                </div>
                            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                                </div>
                    @endif

                    <x-modern-form method="POST" action="{{ route('hotel.store') }}" id="hotel-form">
                        <div class="row">
                            <div class="col-md-6">
                                <x-form-section title="Basic Information" icon="ti ti-building">
                                    <x-form-input 
                                        name="name" 
                                        label="{{ trans('main.Name') }}" 
                                        :required="true"
                                        col="col-12"
                                        value="{{ old('name') }}"
                                    />

                                    <x-form-input 
                                        name="address_first" 
                                        label="{{ trans('main.AddressFirst') }}" 
                                        col="col-12"
                                        value="{{ old('address_first') }}"
                                    />

                                    <x-form-input 
                                        name="address_second" 
                                        label="{{ trans('main.AddressSecond') }}" 
                                        col="col-12"
                                        value="{{ old('address_second') }}"
                                    />

                                    @component('component.city_form', [
                                        'country_label' => 'country', 
                                        'country_translation' => 'main.Country', 
                                        'country_default' => 0,
                                        'city_label' => 'city',
                                        'city_translation' =>'main.City', 
                                        'city_default' => 0
                                    ])
                                    @endcomponent

                                    <x-form-input 
                                        name="code" 
                                        label="{{ trans('main.Code') }}" 
                                        col="col-12"
                                        value="{{ old('code') }}"
                                    />

                                    <x-form-input 
                                        name="website" 
                                        label="{{ trans('main.Website') }}" 
                                        type="url"
                                        col="col-12"
                                        placeholder="https://example.com"
                                        value="{{ old('website') }}"
                                    />

                                    <x-form-input 
                                        name="city_tax" 
                                        label="{{ trans('main.CityTax') }}" 
                                        type="number"
                                        step="0.01"
                                        col="col-12"
                                        value="{{ old('city_tax') }}"
                                    />
                                </x-form-section>

                                <x-form-section title="Contact Information" icon="ti ti-phone">
                                    <x-form-input 
                                        name="work_phone" 
                                        label="{{ trans('main.WorkPhone') }}" 
                                        type="tel"
                                        icon="ti ti-phone"
                                        col="col-12"
                                        value="{{ old('work_phone') }}"
                                    />

                                    <x-form-input 
                                        name="work_fax" 
                                        label="{{ trans('main.WorkFax') }}" 
                                        type="tel"
                                        icon="ti ti-printer"
                                        col="col-12"
                                        value="{{ old('work_fax') }}"
                                    />

                                    <x-form-input 
                                        name="work_email" 
                                        label="{{ trans('main.WorkEmail') }}" 
                                        type="email"
                                        icon="ti ti-mail"
                                        col="col-12"
                                        value="{{ old('work_email') }}"
                                    />

                                    <x-form-input 
                                        name="contact_name" 
                                        label="{{ trans('main.ContactName') }}" 
                                        icon="ti ti-user"
                                        col="col-12"
                                        value="{{ old('contact_name') }}"
                                    />

                                    <x-form-input 
                                        name="contact_phone" 
                                        label="{{ trans('main.ContactPhone') }}" 
                                        type="tel"
                                        icon="ti ti-phone"
                                        col="col-12"
                                        value="{{ old('contact_phone') }}"
                                    />

                                    <x-form-input 
                                        name="contact_email" 
                                        label="{{ trans('main.ContactEmail') }}" 
                                        type="email"
                                        icon="ti ti-mail"
                                        col="col-12"
                                        value="{{ old('contact_email') }}"
                                    />
                                </x-form-section>

                                <x-form-section title="Additional Information" icon="ti ti-notes">
                                    <x-form-input 
                                        name="comments" 
                                        label="{{ trans('main.Comments') }}" 
                                        type="textarea"
                                        col="col-12"
                                        value="{{ old('comments') }}"
                                    />

                                    <x-form-input 
                                        name="note" 
                                        label="{{ trans('main.Note') }}" 
                                        type="textarea"
                                        col="col-12"
                                        value="{{ old('note') }}"
                                    />

                                    <x-form-input 
                                        name="int_comments" 
                                        label="{{ trans('main.IntComments') }}" 
                                        type="textarea"
                                        col="col-12"
                                        help="Internal comments (not visible to clients)"
                                        value="{{ old('int_comments') }}"
                                    />
                                </x-form-section>
                                </div>

                            <div class="col-md-6">
                                <x-form-section title="Criteria & Classification" icon="ti ti-star">
                                    <div class="col-12">
                                <div class="form-group">
                                            <label class="form-label">{!!trans('main.Criteria')!!}</label>
                                            <div class="form-selectgroup form-selectgroup-boxes d-flex flex-column gap-2">
                                                @foreach($criterias as $criteria)
                                                <label class="form-selectgroup-item flex-fill">
                                                    <input type="checkbox" name="criterias" value="{{ $criteria->id }}" class="form-selectgroup-input">
                                                    <div class="form-selectgroup-label d-flex align-items-center p-3">
                                                        <div class="me-3">
                                                            <span class="form-selectgroup-check"></span>
                                </div>
                                                        <div class="form-selectgroup-label-content d-flex align-items-center">
                                                            <span>{{ $criteria->name }}</span>
                                </div>
                                </div>
                                                </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    <x-form-select 
                                        name="rate" 
                                        label="{{ trans('main.Rate') }}" 
                                        :options="$rates->pluck('name', 'id')->toArray()"
                                        :value="old('rate')"
                                        col="col-12"
                                    />
                                </x-form-section>

                                <x-form-section title="Location & Map" icon="ti ti-map-pin">
                                    <div class="col-12">
                                        <input type="hidden" name="place_id" id="place_id">
                                        
                                        <div class="mb-3">
                                            <button type="button" class="btn btn-primary me-2" id="btn_generate_map">
                                                <i class="ti ti-location"></i>
                                                {!!trans('main.GenerateLocation')!!}
                                            </button>
                                            <button type="button" class="btn btn-outline-primary btn_google_maps" id="btn_select_location">
                                                <i class="ti ti-map"></i>
                                                {!!trans('main.SelectLocation')!!}
                                            </button>
                                        </div>
                                        
                                        <span id="page" data-page="create" style="display:none;"></span>
                                        <span id="error_map" class="text-danger"></span>
                                        
                                        <div class="block_map" style="margin-top: 1rem;">
                                            <div id="map" style="height: 400px; border-radius: 8px; border: 1px solid #e2e8f0;"></div>
                                </div>
                                    </div>
                                </x-form-section>

                                <x-form-section title="Files & Documents" icon="ti ti-paperclip">
                                    <div class="col-12">
                                <div class="form-group">
                                            <label class="form-label">{!!trans('main.Files')!!}</label>
                                    @component('component.file_upload_field')@endcomponent
                                </div>
                        </div>
                                </x-form-section>
                            </div>
                        </div>

                        <x-form-actions 
                            :submitText="trans('main.Save')" 
                            :sticky="true"
                        />
                    </x-modern-form>
                    </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        $(document).on('keydown', '.price_room_type_in_hotel', function(e){
            if (e.keyCode === 13) {
                e.preventDefault();
                $('.price_room_type_in_hotel').blur();
            }
        });
    </script>
 <script>
	

    </script>
@endpush
