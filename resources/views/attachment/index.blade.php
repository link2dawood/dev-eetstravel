@extends('scaffold-interface.layouts.tabler-app')
@section('title','Images')
@section('content')
@include('layouts.title',
['title' => 'Images', 'sub_title' => 'Images List',
'breadcrumbs' => [
['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
['title' => 'Images', 'icon' => 'image', 'route' => null]]])
<section class="content">
    <div class="box box-primary">
        <div class="box-body">
            <div class="row">
                @if (Session::has('message'))
                <div class="alert alert-danger"><center>{{ Session::get('message') }}</center></div>
                @endif
                {{-- The form is the grid row, so each category card sits in a column --}}
                <form action="{{ route('images.savefile') }}" method="post" enctype="multipart/form-data" class="row g-3 m-0 w-100">
                    @csrf

                    @forelse($attachmenttypes as $attachmenttype)
                    <div class="col-sm-6 col-lg-4 col-xl-3">
                        <div class="card h-100">
                          <div class="card-body text-center">
                            <h3 class="card-title mb-2">{{ $attachmenttype->name }}</h3>

                            @php $attachmentImage = $attachmenttype->attachments()->first(); @endphp
                            <div class="ratio ratio-4x3 bg-light rounded mb-2 position-relative overflow-hidden">
                                <div class="d-flex flex-column align-items-center justify-content-center text-muted image-placeholder"{!! $attachmentImage ? ' style="display:none !important"' : '' !!}>
                                    <i class="ti ti-photo-off" style="font-size: 2rem;"></i>
                                    <small>{{ $attachmentImage ? 'Image not available on this server' : 'No image yet' }}</small>
                                </div>
                                @if($attachmentImage)
                                    <img class="pic" src="{{ $attachmentImage->url }}" alt="{{ $attachmenttype->name }}" style="width:100%; height:100%; object-fit:cover;"
                                         onerror="this.style.display='none'; this.parentNode.querySelector('.image-placeholder').style.removeProperty('display');">
                                @endif
                            </div>
                            <div class="caption">
{{--                                
                                <div class="imginput">
                                    Change
                                    <input type="file" name="fileToUpload[]" class="fileToUpload hide_file" data-model="Attachmenttype" data-name="{{ $attachmenttype->model }}" data-id="{{ $attachmenttype->id }}">
                                </div>
--}}                                
                                <div class="upload-btn-wrapper">
                                    <label class="form-label small text-muted mb-1">Replace image</label>
                                    <input name="files[]" data-name="{{ $attachmenttype->model }}" data-id="{{ $attachmenttype->id }}" data-model="Attachmenttype" class="fileToUpload form-control form-control-sm" type="file" accept="image/*" />
                                </div>
                            </div>
                          </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12">
                        <div class="alert alert-info">No image categories found yet. Sample: Landing Page Image, Announcement Image, Supplier Gallery.</div>
                    </div>
                    @endforelse

                </form>
                <span id="url" hidden data-url="{{ route('images.savefile') }}"></span>
            </div>    
        </div>
    </div>
    @push('scripts')
    <script type="text/javascript" src='{{asset('js/attachments.js')}}'></script>
    @endpush
</section>
@endsection
