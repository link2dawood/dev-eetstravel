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
                    
                    {{-- ================================== --}}
                    {{-- == FIX: Only showing the Back button as requested == --}}
                    {{-- ================================== --}}
                    <a href="{{ route('announcements.index') }}" class="btn btn-default btn-sm" style="cursor: pointer;">
                        <i class="fa fa-arrow-left"></i> Back
                    </a>
                    
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

{{-- ================================== --}}
{{-- == FIX: REMOVED ALL PUSHED SCRIPTS AND STYLES == --}}
{{-- The old broken scripts were removed --}}
{{-- ================================== --}}