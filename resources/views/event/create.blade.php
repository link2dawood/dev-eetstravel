@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Create Event')

@section('content')
<div class="container-xl">
    <div class="page-header d-print-none">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('/home') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('event.index') }}">Events</a></li>
                            <li class="breadcrumb-item active">Create</li>
                        </ol>
                    </nav>
                </div>
                <h2 class="page-title"><i class="ti ti-plus me-2"></i>Create Event</h2>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('event.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">Event Information</h3></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">Name</label>
                                <input name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Code</label>
                                <input name="code" type="text" class="form-control" value="{{ old('code') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Address 1</label>
                                <input name="address_first" type="text" class="form-control" value="{{ old('address_first') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Address 2</label>
                                <input name="address_second" type="text" class="form-control" value="{{ old('address_second') }}">
                            </div>
                            <div class="col-12 mb-3">
                                @component('component.city_form', ['country_label' => 'country', 'country_translation' => 'main.Country', 'country_default' => 0, 'city_label' => 'city', 'city_translation' => 'main.City', 'city_default' => 0])@endcomponent
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Work Phone</label>
                                <div class="input-icon"><span class="input-icon-addon"><i class="ti ti-phone"></i></span>
                                <input name="work_phone" type="text" class="form-control" value="{{ old('work_phone') }}"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Work Fax</label>
                                <input name="work_fax" type="text" class="form-control" value="{{ old('work_fax') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Work Email</label>
                                <div class="input-icon"><span class="input-icon-addon"><i class="ti ti-mail"></i></span>
                                <input name="work_email" type="email" class="form-control" value="{{ old('work_email') }}"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Contact Name</label>
                                <input name="contact_name" type="text" class="form-control" value="{{ old('contact_name') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Contact Phone</label>
                                <div class="input-icon"><span class="input-icon-addon"><i class="ti ti-phone"></i></span>
                                <input name="contact_phone" type="text" class="form-control" value="{{ old('contact_phone') }}"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Contact Email</label>
                                <div class="input-icon"><span class="input-icon-addon"><i class="ti ti-mail"></i></span>
                                <input name="contact_email" type="email" class="form-control" value="{{ old('contact_email') }}"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Website</label>
                                <div class="input-icon"><span class="input-icon-addon"><i class="ti ti-world"></i></span>
                                <input name="website" type="url" class="form-control" value="{{ old('website') }}"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Comments</label>
                                <input name="comments" type="text" class="form-control" value="{{ old('comments') }}">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Internal Comments</label>
                                <textarea name="int_comments" rows="3" class="form-control">{{ old('int_comments') }}</textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Rate</label>
                                <select name="rate" class="form-select">
                                    @foreach($rates as $rate)
                                        <option value="{{ $rate->id }}" {{ old('rate') == $rate->id ? 'selected' : '' }}>{{ $rate->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Criterias</label>
                                <div class="row">
                                    @foreach($criterias as $criteria)
                                        <div class="col-md-6 mb-2">
                                            <label class="form-check">
                                                <input type="checkbox" class="form-check-input" value="{{ $criteria->id }}" name="criterias">
                                                <span class="form-check-label">{{ $criteria->name }}</span>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Files</label>
                                @component('component.file_upload_field')@endcomponent
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-primary" type="submit"><i class="ti ti-check me-1"></i>Save</button>
                        <a href="{{ route('event.index') }}" class="btn"><i class="ti ti-x me-1"></i>Cancel</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card sticky-top" style="top: 1rem;">
                    <div class="card-header"><h3 class="card-title"><i class="ti ti-map me-2"></i>Location</h3></div>
                    <div class="card-body">
                        <button class="btn btn-primary w-100 mb-2" type="button" id="btn_generate_map"><i class="ti ti-map-pin me-1"></i>Generate Location</button>
                        <button class="btn btn-outline-primary w-100" type="button" id="btn_select_location"><i class="ti ti-click me-1"></i>Select Location</button>
                        <div id="map" style="height: 400px; border-radius: 6px; margin-top: 1rem;"></div>
                        <input type="hidden" name="place_id" id="place_id">
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<span id="page" data-page="create" hidden></span>
@endsection

@push('scripts')
<script src="{{ asset('js/google_map.js') }}"></script>
@endpush
