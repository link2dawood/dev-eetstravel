@extends('scaffold-interface.layouts.tabler-app')
@section('title','Edit Announcement')

@section('content')
    @include('layouts.title',
    ['title' => 'Announcement', 'sub_title' => 'Edit Announcement',
    'breadcrumbs' => [
    ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
    ['title' => 'Announcements', 'icon' => 'bullhorn', 'route' => route('announcements.index')],
    ['title' => 'Edit', 'route' => null]]])

    <div class="page-body">
        <div class="container-xl">

            <form method="POST" action="{{ route('announcements.update', ['announcement' => $announcement->id]) }}" enctype="multipart/form-data" id="announcementForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="deleted_files" id="deleted_files_input" value="">

                {{-- Action Buttons --}}
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="btn-list">
                            <a href="{{ route('announcements.index') }}" class='btn btn-secondary'>
                                <i class="ti ti-arrow-left me-1"></i>
                                {!!trans('main.Back')!!}
                            </a>
                            <button class='btn btn-primary' type='submit' id="submitBtn">
                                <i class="ti ti-device-floppy me-1"></i>
                                {!!trans('main.Save')!!}
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Messages --}}
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <div class="d-flex">
                            <div>
                                <i class="ti ti-alert-circle icon alert-icon"></i>
                            </div>
                            <div>
                                <h4 class="alert-title">Validation Errors</h4>
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
                    {{-- Left Column --}}
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Edit Announcement</h3>
                            </div>
                            <div class="card-body">
                                
                                <div class="mb-3">
                                    <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('title') is-invalid @enderror" 
                                           id="title" 
                                           name="title" 
                                           value="{{ old('title', $announcement->title) }}"
                                           placeholder="Enter announcement title"
                                           required>
                                    @error('title')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="content" class="form-label">Content <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('content') is-invalid @enderror" 
                                              id="content" 
                                              name="content" 
                                              rows="8"
                                              placeholder="Enter announcement content"
                                              required>{{ old('content', $announcement->content) }}</textarea>
                                    @error('content')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="files" class="form-label">Attach New Files</label>
                                    <input type="file" 
                                           class="form-control" 
                                           id="files" 
                                           name="files[]" 
                                           multiple
                                           accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif">
                                    <small class="form-text text-muted">You can select multiple files (PDF, DOC, XLS, Images, etc.)</small>
                                </div>

                                @if($files && count($files) > 0)
                                    <div class="mb-3">
                                        <label class="form-label">Existing Files</label>
                                        <div class="list-group" id="existing-files-list">
                                            @foreach($files as $file)
                                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <i class="ti ti-file me-2"></i>
                                                        <a href="{{ $file->url }}" target="_blank">{{ $file->name }}</a>
                                                    </div>
                                                    <button type="button" class="btn btn-sm btn-icon btn-ghost-danger delete-file" data-file-id="{{ $file->id }}" title="Delete this file">
                                                        <i class="ti ti-trash"></i>
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                            </div>
                            <div class="card-footer text-end">
                                <div class="btn-list">
                                    <a href="{{ route('announcements.index') }}" class='btn btn-secondary'>
                                        <i class="ti ti-x me-1"></i>
                                        {!!trans('main.Cancel')!!}
                                    </a>
                                    <button class='btn btn-primary' type='submit' id="submitBtnFooter">
                                        <i class="ti ti-device-floppy me-1"></i>
                                        {!!trans('main.Save')!!}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Right Column (Sidebar) --}}
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Announcement Info</h3>
                            </div>
                            <div class="card-body">
                                <dl class="row">
                                    <dt class="col-sm-5">ID:</dt>
                                    <dd class="col-sm-7">{{ $announcement->id }}</dd>
                                    
                                    <dt class="col-sm-5">Created:</dt>
                                    <dd class="col-sm-7">{{ $announcement->created_at->format('Y-m-d H:i') }}</dd>
                                    
                                    <dt class="col-sm-5">Updated:</dt>
                                    <dd class="col-sm-7">{{ $announcement->updated_at->format('Y-m-d H:i') }}</dd>
                                    
                                    <dt class="col-sm-5">Author:</dt>
                                    <dd class="col-sm-7">{{ $announcement->author_name ?? 'Unknown' }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>

                </div>
            </form>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let deletedFileIds = [];
            const deletedFilesInput = document.getElementById('deleted_files_input');
            
            // Handle file deletion
            document.querySelectorAll('.delete-file').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (confirm('Are you sure you want to delete this file? This will be permanent upon saving.')) {
                        const fileId = this.getAttribute('data-file-id');
                        deletedFileIds.push(fileId);
                        
                        // Update the hidden input
                        deletedFilesInput.value = deletedFileIds.join(',');
                        
                        // Remove the item from the list
                        this.closest('.list-group-item').remove();
                    }
                });
            });

            // Prevent multiple form submissions
            const form = document.getElementById('announcementForm');
            const submitBtns = form.querySelectorAll('button[type="submit"]');

            if (form) {
                form.addEventListener('submit', function(e) {
                    // Check browser validity
                    if (!form.checkValidity()) {
                        e.preventDefault();
                        e.stopPropagation();
                        form.classList.add('was-validated');
                        return;
                    }

                    // Disable all submit buttons
                    submitBtns.forEach(btn => {
                        btn.disabled = true;
                        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Saving...';
                    });
                });
            }
        });
    </script>
@endpush