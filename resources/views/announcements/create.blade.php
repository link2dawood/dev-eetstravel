@extends('scaffold-interface.layouts.tabler-app')
@section('title','Create')
@section('content')
    @include('layouts.title',
   ['title' => 'Announcement', 'sub_title' => 'Announcement Create',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Announcements', 'icon' => 'coffee', 'route' => route('announcements.index')],
   ['title' => 'Create', 'route' => null]]])

    <section class="content">
        <div class="box box-primary">
            <div class="box-body border_top_none">
                <form method="POST" action="{{ route('announcements.store') }}" enctype="multipart/form-data" id="announcementCreateForm">
                    @csrf
                    {{ Form::hidden('parent_id', old('parent_id', $parent_id)) }}
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <a href="{{ route('announcements.index') }}" class="btn btn-secondary">
                                <i class="ti ti-arrow-left me-1"></i>{!! trans('main.Back') !!}
                            </a>
                        </div>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            <div class="d-flex">
                                <div><i class="ti ti-alert-circle icon alert-icon"></i></div>
                                <div>
                                    <h4 class="alert-title">Please fix the highlighted fields</h4>
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

                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Create Announcement</h3>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="title" class="form-label">{!! trans('main.Title') !!} <span class="text-danger">*</span></label>
                                        <input type="text"
                                               name="title"
                                               id="title"
                                               class="form-control @error('title') is-invalid @enderror"
                                               value="{{ old('title', $title) }}"
                                               placeholder="Enter announcement title"
                                               required>
                                        @error('title')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="content" class="form-label">{!! trans('main.Content') !!} <span class="text-danger">*</span></label>
                                        <textarea name="content"
                                                  id="content"
                                                  rows="8"
                                                  class="form-control @error('content') is-invalid @enderror"
                                                  placeholder="Enter announcement content"
                                                  required>{{ old('content') }}</textarea>
                                        @error('content')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="files" class="form-label">{!! trans('main.Files') !!}</label>
                                        <input type="file"
                                               name="files[]"
                                               id="files"
                                               class="form-control @error('files.*') is-invalid @enderror"
                                               multiple
                                               accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif,.csv,.txt">
                                        <small class="form-text text-muted">You can select multiple files. Files are uploaded after saving.</small>
                                        @error('files.*')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="card-footer text-end">
                                    <button class="btn btn-success" type="submit">
                                        <i class="ti ti-device-floppy me-1"></i>{!! trans('main.Save') !!}
                                    </button>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('announcementCreateForm');
            if (!form) return;

            const submitButtons = form.querySelectorAll('button[type="submit"]');

            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                    form.classList.add('was-validated');
                    return;
                }

                submitButtons.forEach(function(button) {
                    button.disabled = true;
                    button.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Saving...';
                });
            });
        });
    </script>
@endpush
