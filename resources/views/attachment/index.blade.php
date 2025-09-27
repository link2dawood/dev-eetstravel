@extends('scaffold-interface.layouts.app')
@section('title','Index')
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
                <form action="upload.php" method="post" enctype="multipart/form-data">
                    
                    @foreach($attachmenttypes as $attachmenttype)
                    <div class="col-md-3">
                        <div class="thumbnail text-center">
                           
                            <h3>{{ $attachmenttype->name }} </h3>
                            
                            <img class="pic" src="@if($attachmenttype->attachments()->first() != null) {{ $attachmenttype->attachments()->first()->url }} @endif" alt="{{ $attachmenttype->name }}" style="width:100%">
                            <div class="caption">
{{--                                
                                <div class="imginput">
                                    Change
                                    <input type="file" name="fileToUpload[]" class="fileToUpload hide_file" data-model="Attachmenttype" data-name="{{ $attachmenttype->model }}" data-id="{{ $attachmenttype->id }}">
                                </div>
--}}                                
                                <div class="upload-btn-wrapper">
                                    {{--<button class="btn btn-primary">Change</button>--}}
                                    <input name="fileToUpload[]" data-name="{{ $attachmenttype->model }}" data-id="{{ $attachmenttype->id }}" data-model="Attachmenttype" class="fileToUpload"type="file" name="myfile" />
                                </div>                                
                            </div>
                        </div>
                    </div>
                    @endforeach

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
