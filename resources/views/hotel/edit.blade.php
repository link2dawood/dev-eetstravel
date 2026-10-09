@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Edit Hotel')

@section('content')
<div class="container-xl">
    {{-- Page Header --}}
    <div class="page-header d-print-none">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('/home') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('hotel.index') }}">Hotels</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('hotel.show', $hotel->id) }}">{{ $hotel->name }}</a></li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                    </nav>
                </div>
                <h2 class="page-title">
                    <i class="ti ti-edit me-2"></i>Edit Hotel
                </h2>
            </div>
        </div>
    </div>

    {{-- Form --}}
    <form method="POST" action="{{ route('hotel.update', ['hotel' => $hotel->id]) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Hotel Information</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">{!!trans('main.Name')!!}</label>
                                <input id="name" name="name" type="text" class="form-control" value="{{ old('name', $hotel->name) }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">{!!trans('main.Code')!!}</label>
                                <input id="code" name="code" type="text" class="form-control" value="{{ old('code', $hotel->code) }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">{!!trans('main.AddressFirst')!!}</label>
                                <input id="address_first" name="address_first" type="text" class="form-control" value="{{ old('address_first', $hotel->address_first) }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">{!!trans('main.AddressSecond')!!}</label>
                                <input id="address_second" name="address_second" type="text" class="form-control" value="{{ old('address_second', $hotel->address_second) }}">
                            </div>

                            <div class="col-12 mb-3">
                                @if(!empty($hotel->city) && is_numeric($hotel->city))
                                    @component('component.city_form', [
                                        'country_label' => 'country',
                                        'country_translation' => 'main.Country',
                                        'country_default' => $hotel->country,
                                        'city_label' => 'city',
                                        'city_translation' => 'main.City',
                                        'city_default' => \App\Helper\CitiesHelper::getCityById($hotel->city)['name']
                                    ])
                                    @endcomponent
                                @else
                                    @component('component.city_form', [
                                        'country_label' => 'country',
                                        'country_translation' => 'main.Country',
                                        'country_default' => $hotel->country,
                                        'city_label' => 'city',
                                        'city_translation' => 'main.City',
                                        'city_default' => $hotel->city
                                    ])
                                    @endcomponent
                                @endif
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">{!!trans('main.WorkPhone')!!}</label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="ti ti-phone"></i></span>
                                    <input id="work_phone" name="work_phone" type="text" class="form-control" value="{{ old('work_phone', $hotel->work_phone) }}">
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">{!!trans('main.WorkFax')!!}</label>
                                <input id="work_fax" name="work_fax" type="text" class="form-control" value="{{ old('work_fax', $hotel->work_fax) }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">{!!trans('main.WorkEmail')!!}</label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="ti ti-mail"></i></span>
                                    <input id="work_email" name="work_email" type="email" class="form-control" value="{{ old('work_email', $hotel->work_email) }}">
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">{!!trans('main.Website')!!}</label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="ti ti-world"></i></span>
                                    <input id="website" name="website" type="url" class="form-control" value="{{ old('website', $hotel->website) }}">
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">{!!trans('main.ContactName')!!}</label>
                                <input id="contact_name" name="contact_name" type="text" class="form-control" value="{{ old('contact_name', $hotel->contact_name) }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">{!!trans('main.ContactPhone')!!}</label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="ti ti-phone"></i></span>
                                    <input id="contact_phone" name="contact_phone" type="text" class="form-control" value="{{ old('contact_phone', $hotel->contact_phone) }}">
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">{!!trans('main.ContactEmail')!!}</label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="ti ti-mail"></i></span>
                                    <input id="contact_email" name="contact_email" type="email" class="form-control" value="{{ old('contact_email', $hotel->contact_email) }}">
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">{!!trans('main.Comments')!!}</label>
                                <input id="comments" name="comments" type="text" class="form-control" value="{{ old('comments', $hotel->comments) }}">
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label">{!!trans('main.IntComments')!!}</label>
                                <textarea id="int_comments" name="int_comments" rows="3" class="form-control">{{ old('int_comments', $hotel->int_comments) }}</textarea>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">{!!trans('main.Rate')!!}</label>
                                <select name="rate" id="rate" class="form-select">
                                    @foreach($rates as $rate)
                                        <option value="{{ $rate->id }}" {{ old('rate', $hotel->rate) == $rate->id ? 'selected' : '' }}>
                                            {{ $rate->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label">{!!trans('main.Criteria')!!}</label>
                                <div class="row">
                                    @foreach($criterias as $criteria)
                                        <div class="col-md-6 mb-2">
                                            <label class="form-check">
                                                <input type="checkbox" 
                                                       class="form-check-input" 
                                                       value="{{ $criteria->id }}" 
                                                       name="criterias"
                                                       @foreach($hotel->criterias as $item)
                                                           {{ $criteria->id == $item->criteria_id ? 'checked' : '' }}
                                                       @endforeach>
                                                <span class="form-check-label">{{ $criteria->name }}</span>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label">{!!trans('main.Files')!!}</label>
                                @component('component.file_upload_field', ['enableAjaxUploads' => false])@endcomponent
                            </div>

                            <div class="col-12">
                                @component('component.files', ['files' => $files])@endcomponent
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="btn-list">
                            <button class="btn btn-primary" type="submit">
                                <i class="ti ti-check me-1"></i>{!!trans('main.Save')!!}
                            </button>
                            <a href="{{ route('hotel.show', $hotel->id) }}" class="btn">
                                <i class="ti ti-x me-1"></i>Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Map Sidebar --}}
            <div class="col-lg-4">
                <div class="card sticky-top" style="top: 1rem;">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="ti ti-map me-2"></i>Location
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <button class="btn btn-primary w-100 mb-2" 
                                    type="button" 
                                    id="btn_generate_map">
                                <i class="ti ti-map-pin me-1"></i>{!!trans('main.GenerateLocation')!!}
                            </button>
                            <button class="btn btn-outline-primary w-100" 
                                    type="button" 
                                    id="btn_select_location">
                                <i class="ti ti-click me-1"></i>{!!trans('main.SelectLocation')!!}
                            </button>
                        </div>
                        <span id="error_map" class="text-danger"></span>
                        <div class="block_map">
                            <div id="map" style="height: 400px; border-radius: 6px;"></div>
                        </div>
                        <input type="hidden" name="place_id" id="place_id">
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<span id="hotel_id_span" data-info="{{ $hotel->id }}" hidden></span>
<span id="page" data-page="edit" hidden></span>
@endsection

@push('scripts')
<script src="{{ asset('js/google_map.js') }}"></script>
@endpush
