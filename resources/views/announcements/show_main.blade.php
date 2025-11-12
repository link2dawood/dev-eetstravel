@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ $announcement->title }}</h3>
                    <div class="card-tools pull-right">
                        <a href="{{ route('announcements.index') }}" class="btn btn-sm btn-default">
                            <i class="fa fa-arrow-left"></i> Back
                        </a>
                        @can('update', $announcement)
                            <a href="{{ route('announcements.edit', $announcement) }}" class="btn btn-sm btn-warning">
                                <i class="fa fa-edit"></i> Edit
                            </a>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <div class="chat">
                        <div class="item">
                            <div class="chat-details">
                                <span class="chat-author">
                                    by <b>{{ $announcement->author->name ?? 'Unknown' }}</b>
                                </span>
                                <span class="chat-date">
                                    <i>{{ $announcement->created_at }}</i>
                                </span>
                            </div>

                            <div class="chat-content">
                                {!! $announcement->content !!}
                            </div>

                            {{-- Only show attachments if media table exists --}}
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
                            <div class="chat-attachments">
                                <h5>Attachments:</h5>
                                <table class="table">
                                    <tbody>
                                        @foreach($attachments as $attachment)
                                            <tr class="del-container">
                                                <td class="td_link_attach">
                                                    <div class="td_link_attach__name">
                                                        <a class="name_attach" href="{{ $attachment->getUrl() }}" target="_blank">
                                                            <span class="glyphicon glyphicon-paperclip"></span>
                                                            {{ $attachment->file_name }}
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @endif

                            <div class="announcement-actions">
                                {{-- Optional actions go here --}}
                            </div>

                            @php
                                $childs = $announcement->childs()->get();
                            @endphp
                            @if($childs && $childs->isNotEmpty())
                                @include('announcements.childs', ['childs' => $childs, 'nesting' => 1])
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection