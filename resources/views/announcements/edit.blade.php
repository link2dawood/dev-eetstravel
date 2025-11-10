@extends('scaffold-interface.layouts.tabler-app')
@section('title','Edit Announcement')
@section('content')
    @include('layouts.title',
    ['title' => 'Announcement', 'sub_title' => 'Edit Announcement',
    'breadcrumbs' => [
    ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
    ['title' => 'Announcements', 'icon' => 'coffee', 'route' => route('announcements.index')],
    ['title' => 'Edit', 'route' => null]]])
    
    <section class="content">
        <div class="box box-primary">
            <div class="box-body border_top_none">
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        <h4 class="alert-heading">Error!</h4>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('announcements.update', ['announcement' => $announcement->id]) }}" enctype="multipart/form-data" id="announcementForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="btn-group" role="group">
                                <a href="{{ route('announcements.index') }}" class="btn btn-primary">
                                    <i class="ti ti-arrow-left"></i> Back
                                </a>
                                <button type="submit" class="btn btn-success">
                                    <i class="ti ti-check"></i> Save Changes
                                </button>
                                <a href="{{ route('announcements.index') }}" class="btn btn-secondary">
                                    <i class="ti ti-x"></i> Cancel
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <!-- Title Field -->
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

                            <!-- Content Field -->
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

                            <!-- File Upload -->
                            <div class="mb-3">
                                <label for="files" class="form-label">Attach Files</label>
                                <input type="file" 
                                       class="form-control" 
                                       id="files" 
                                       name="files[]" 
                                       multiple
                                       accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif">
                                <small class="form-text text-muted">You can select multiple files (PDF, DOC, XLS, Images, etc.)</small>
                            </div>

                            <!-- Existing Files -->
                            @if($files && count($files) > 0)
                                <div class="mb-3">
                                    <label class="form-label">Existing Files</label>
                                    <div class="list-group">
                                        @foreach($files as $file)
                                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                                <div>
                                                    <i class="ti ti-file"></i>
                                                    <a href="{{ $file->url }}" target="_blank">{{ $file->name }}</a>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-danger delete-file" data-file-id="{{ $file->id }}">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Submit Buttons -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-success me-2">
                                        <i class="ti ti-check"></i> Save Announcement
                                    </button>
                                    <a href="{{ route('announcements.index') }}" class="btn btn-secondary">
                                        <i class="ti ti-x"></i> Cancel
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Sidebar Info -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Announcement Info</h3>
                                </div>
                                <div class="card-body">
                                    <dl class="row">
                                        <dt class="col-sm-6">ID:</dt>
                                        <dd class="col-sm-6">{{ $announcement->id }}</dd>
                                        
                                        <dt class="col-sm-6">Created:</dt>
                                        <dd class="col-sm-6">{{ $announcement->created_at->format('Y-m-d H:i') }}</dd>
                                        
                                        <dt class="col-sm-6">Updated:</dt>
                                        <dd class="col-sm-6">{{ $announcement->updated_at->format('Y-m-d H:i') }}</dd>
                                        
                                        <dt class="col-sm-6">Author:</dt>
                                        <dd class="col-sm-6">{{ $announcement->author_name ?? 'Unknown' }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle file deletion (if you want to implement async file deletion)
            document.querySelectorAll('.delete-file').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    // You can implement async file deletion here if needed
                    if (confirm('Are you sure you want to delete this file?')) {
                        // Add your file deletion logic here
                        this.closest('.list-group-item').remove();
                    }
                });
            });

            // Form validation
            const form = document.getElementById('announcementForm');
            form.addEventListener('submit', function(e) {
                if (!form.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                form.classList.add('was-validated');
            });
        });
    </script>

    <style>
        .border_top_none {
            border-top: none;
        }
        
        .box-primary {
            border-top: 3px solid #007bff;
        }
        
        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        
        .text-danger {
            color: #dc3545;
        }
        
        .list-group-item {
            padding: 10px 15px;
        }
    </style>
@endsection