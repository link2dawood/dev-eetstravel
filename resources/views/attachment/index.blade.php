@extends('scaffold-interface.layouts.tabler-app')
@section('title','Images')

@section('content')
<x-ui.page-header
    title="Images"
    description="Reference images used as placeholders / icons across attachment types."
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Images'],
    ]"
/>

@if(Session::has('message'))
    <div class="mb-4 flex items-start gap-3 rounded border border-danger-600/20 bg-danger-50 px-4 py-3 text-sm text-danger-700">
        <x-ui.icon name="alert-octagon" class="mt-0.5 text-danger-600" />
        <div class="flex-1">{{ Session::get('message') }}</div>
    </div>
@endif

<form action="upload.php" method="post" enctype="multipart/form-data">
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @foreach($attachmenttypes as $attachmenttype)
            <div class="rounded border border-slate-200 bg-white overflow-hidden">
                <div class="border-b border-slate-200 px-4 py-2 flex items-center gap-2">
                    <x-ui.icon name="image" size="sm" class="text-slate-400" />
                    <h3 class="text-sm font-medium text-slate-700 truncate">{{ $attachmenttype->name }}</h3>
                </div>
                <div class="aspect-[4/3] flex items-center justify-center bg-slate-50">
                    @if($attachmenttype->attachments()->first() != null)
                        <img class="pic w-full h-full object-cover"
                             src="{{ $attachmenttype->attachments()->first()->url }}"
                             alt="{{ $attachmenttype->name }}">
                    @else
                        <span class="text-xs text-slate-400">No image yet</span>
                    @endif
                </div>
                <div class="caption px-4 py-3 bg-slate-50 border-t border-slate-200">
                    {{-- attachments.js binds to .fileToUpload[data-id][data-model][data-name] --}}
                    <label class="inline-flex w-full cursor-pointer items-center justify-center gap-1.5 rounded border border-slate-300 bg-white px-3 h-9 text-sm text-slate-700 hover:bg-slate-50">
                        <x-ui.icon name="upload" size="sm" /> Change
                        <input name="fileToUpload[]" type="file"
                               data-name="{{ $attachmenttype->model }}"
                               data-id="{{ $attachmenttype->id }}"
                               data-model="Attachmenttype"
                               class="fileToUpload hidden">
                    </label>
                </div>
            </div>
        @endforeach
    </div>
</form>

<span id="url" hidden data-url="{{ route('images.savefile') }}"></span>
@endsection

@push('scripts')
<script type="text/javascript" src='{{ asset('js/attachments.js') }}'></script>
@endpush
