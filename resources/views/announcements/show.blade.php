@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'View Announcement')
@section('content')
    @include('layouts.title', [
        'title' => 'Announcement Details',
        'sub_title' => $announcement->title,
        'breadcrumbs' => [
            ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
            ['title' => 'Announcements', 'icon' => 'coffee', 'route' => route('announcements.index')],
            ['title' => 'View', 'icon' => 'eye', 'route' => null]
        ]
    ])
    
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">{{ $announcement->title }}</h3>
                <div class="box-tools pull-right">
                    <!-- FIXED: Use direct anchor tags with proper styling -->
                    <a href="{{ route('announcements.index') }}" class="btn btn-default btn-sm" style="cursor: pointer;">
                        <i class="fa fa-arrow-left"></i> Back
                    </a>
                    
                    @if(Auth::user()->can('announcements.edit'))
                        <a href="{{ route('announcements.edit', $announcement->id) }}" class="btn btn-warning btn-sm" style="cursor: pointer; text-decoration: none;">
                            <i class="fa fa-edit"></i> Edit
                        </a>
                    @endif

                    @if(Auth::user()->can('announcements.destroy'))
                        <button type="button" class="btn btn-danger btn-sm delete-announcement-btn" 
                                data-url="{{ route('announcements.destroy', $announcement->id) }}"
                                style="cursor: pointer;">
                            <i class="fa fa-trash"></i> Delete
                        </button>
                    @endif
                </div>
            </div>
            
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="info-box bg-light-blue" style="background: #f9f9f9; border-left: 4px solid #3c8dbc; padding: 15px; margin-bottom: 20px;">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong><i class="fa fa-user"></i> Author:</strong> {{ optional(\App\User::find($announcement->author))->name ?? 'Unknown' }}</p>
                                    <p><strong><i class="fa fa-envelope"></i> Sender:</strong> {{ $announcement->sender ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong><i class="fa fa-calendar"></i> Created:</strong> {{ $announcement->created_at ? $announcement->created_at->format('Y-m-d H:i:s') : 'N/A' }}</p>
                                    <p><strong><i class="fa fa-clock-o"></i> Updated:</strong> {{ $announcement->updated_at ? $announcement->updated_at->format('Y-m-d H:i:s') : 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title"><i class="fa fa-file-text-o"></i> Content</h4>
                            </div>
                            <div class="panel-body" style="min-height: 200px; padding: 20px; white-space: pre-wrap; word-wrap: break-word;">
                                {!! nl2br(e($announcement->content)) !!}
                            </div>
                        </div>

                        @php
                            $attachments = collect();
                            try {
                                if (Schema::hasTable('media')) {
                                    $attachments = $announcement->getMedia('announcement_files');
                                }
                            } catch (\Exception $e) {
                                // Media table doesn't exist, skip attachments
                            }
                        @endphp

                        @if($attachments->isNotEmpty())
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title"><i class="fa fa-paperclip"></i> Attachments ({{ $attachments->count() }})</h4>
                            </div>
                            <div class="panel-body">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th width="50">#</th>
                                            <th>File Name</th>
                                            <th width="120">Size</th>
                                            <th width="100" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($attachments as $index => $attachment)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <i class="fa fa-file-o"></i> 
                                                    {{ $attachment->file_name }}
                                                </td>
                                                <td>{{ $attachment->human_readable_size }}</td>
                                                <td class="text-center">
                                                    <a href="{{ $attachment->getUrl() }}" 
                                                       target="_blank" 
                                                       class="btn btn-primary btn-xs"
                                                       title="Download">
                                                        <i class="fa fa-download"></i> Download
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif

                        @php
                            $childs = $announcement->childs()->with('author')->get();
                        @endphp
                        @if($childs && $childs->isNotEmpty())
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title"><i class="fa fa-comments"></i> Replies ({{ $childs->count() }})</h4>
                            </div>
                            <div class="panel-body">
                                @include('announcements.childs', ['childs' => $childs, 'nesting' => 1])
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // FIXED: Edit button click handler
    $('a[href*="/edit"]').on('click', function(e) {
        // Allow normal link navigation
        // Don't prevent default - let the link work
        console.log('Edit link clicked:', $(this).attr('href'));
    });

    // Delete announcement handler
    $('.delete-announcement-btn').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const button = $(this);
        const deleteUrl = button.data('url');
        
        console.log('Delete URL:', deleteUrl);
        
        if (confirm('Are you sure you want to delete this announcement?')) {
            button.prop('disabled', true);
            button.html('<i class="fa fa-spinner fa-spin"></i> Deleting...');
            
            $.ajax({
                url: deleteUrl,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    console.log('Delete response:', response);
                    if (response.success) {
                        alert('Announcement deleted successfully!');
                        window.location.href = '{{ route("announcements.index") }}';
                    } else {
                        alert('Error: ' + (response.message || 'Unknown error'));
                        button.prop('disabled', false);
                        button.html('<i class="fa fa-trash"></i> Delete');
                    }
                },
                error: function(xhr) {
                    console.error('Delete error:', xhr);
                    const errorMsg = xhr.responseJSON?.message || xhr.statusText || 'Unknown error';
                    alert('Error deleting announcement: ' + errorMsg);
                    button.prop('disabled', false);
                    button.html('<i class="fa fa-trash"></i> Delete');
                }
            });
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.panel {
    margin-bottom: 20px;
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-shadow: 0 1px 1px rgba(0,0,0,.05);
}

.panel-heading {
    padding: 10px 15px;
    border-bottom: 1px solid #ddd;
    background-color: #f5f5f5;
    border-radius: 3px 3px 0 0;
}

.panel-title {
    margin: 0;
    font-size: 16px;
    font-weight: bold;
}

.panel-body {
    padding: 15px;
}

.info-box p {
    margin: 5px 0;
}

.info-box strong {
    display: inline-block;
    min-width: 100px;
}

/* Ensure buttons are clickable */
.btn {
    cursor: pointer !important;
    display: inline-block;
    text-decoration: none !important;
}

.btn-warning, .btn-default, .btn-danger {
    transition: all 0.3s ease;
}

.btn:hover {
    opacity: 0.9;
}
</style>
@endpush