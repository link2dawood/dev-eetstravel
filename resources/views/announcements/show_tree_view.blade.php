@extends('scaffold-interface.layouts.app')

@section('content')
    @include('layouts.title',
   ['title' => 'Announcement', 'sub_title' => $announcement->title,
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Announcements', 'icon' => 'coffee', 'route' => route('announcements.index')],
   ['title' => 'Show', 'route' => null]]])
    <section class="content">
        {{-- <div class="box box-primary">
            <div class="box-body">
                <div style="margin-bottom: 10px;">
                    <a href="javascript:history.back()">
                        <button class='btn btn-primary'>{!!trans('main.Back')!!}</button>
                    </a>
                    <a href="{!! route('announcements.edit', $announcement->id) !!}">
                        <button class='btn btn-warning'>{!!trans('main.Edit')!!}</button>
                    </a>
                </div>
                <div class="chat">
                    <div class="item">
                        <div class="chat-details">
                            <span class="chat-author">
                                by <b>{{\App\User::find($mainParent->author)->name}}</b>
                            </span>
                            <span class="chat-date">
                               <i>{{$mainParent->created_at}}</i>
                            </span>
                        </div>
                        <div class="chat-content">
                            {!! $mainParent->content !!}

                        </div>
                        <div class="chat-attachments">
                            <table class="table">
                                <tbody>


                            @foreach($mainParent->files as $attach)
                                <tr class="del-container">
                                    <td class="td_link_attach">
                                        <div class="td_link_attach__name">
                                            <a class="name_attach" href="{{$attach->attach->url()}}" target="_blank">
                                                <span class="glyphicon glyphicon-paperclip"></span>
                                                {{$attach->attach_file_name}}
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                                </tbody>
                            </table>
                        </div>
                            <div class="announcement-actions">
                                <a href="{{route('announcement_reply', ['id' => $mainParent->id])}}" class="link-black text-sm reply_comment" style="margin-top: 10px"><i class="fa fa-reply margin-r-5"></i> Reply</a>
                            </div>

                            @if(count($mainParent->childs))
                                @include('announcements.childs',['childs' => $mainParent->childs, 'nesting' => 1])
                            @endif
                    </div>
                </div>

            </div>
        </div> --}}
        <span id="showPreviewBlock" data-info="{{ true }}"></span>
        <div class="box box-success" style="position: relative; left: 0px; top: 0px;">
            <div class="box-header ui-sortable-handle" style="cursor: move;">
                <i class="fa fa-comments-o"></i>

                <h3 class="box-title">{!!trans('main.Announcement')!!}</h3>
            </div>
            <div class="box-body">
                <div style="margin-bottom: 10px;">
                    <a href="javascript:history.back()">
                        <button class='btn btn-primary'>{!!trans('main.Back')!!}</button>
                    </a>
                    @if(\Auth::id() == $announcement->author)
                    <a href="{!! route('announcements.edit', $announcement->id) !!}">
                        <button class='btn btn-warning'>{!!trans('main.Edit')!!}</button>
                    </a>
                    @endif
                </div>
                <div class="slimScrollDiv" style="position: relative; overflow-y: scroll;  width: auto;">
                    <div class="box-body box chat" id="chat-box" style="width: auto; height: auto;">
                        <div id="show_comments"></div>
                    </div>
                    <div class="slimScrollRail" style="width: 7px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 7px; background: rgb(51, 51, 51) none repeat scroll 0% 0%; opacity: 0.2; z-index: 90; right: 1px;"></div>
                </div>
            </div>
            <!-- /.chat -->
            <div class="box-footer">
                <form method='post' action='{{route('announcement_reply', ['id' => $announcement->id])}}' enctype="multipart/form-data" id="form_comment">
                {{csrf_field()}}
                    <div class="input-group" style="width: 100%">
                                        <span id="author_name" class="input-group-addon">
                                            <span id="name"></span>
                                            <a href="#" id="reply_close"><i class="fa fa-close"></i></a>
                                        </span>
                        {{-- <input type="text" name="title" class="form-control" placeholder="Enter title for comment"> --}}
                        <textarea class="form-control" id="content" name="content" placeholder="Ctrl + Enter to post comment"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Files</label>
                        @component('component.file_upload_field')@endcomponent
                    </div>
                    <input type="text" id="parent_comment" hidden name="parent_id" value="{{ $announcement->id }}">
                    {{-- <input type="text" id="default_reference_id" hidden name="parent_id" value="{{ $announcement->parent_id }}"> --}}
                    {{-- <input type="text" id="default_reference_type" hidden name="reference_type" value="{{ $announcement->reference_type }}"> --}}
                    <input type="text" id="id_comment" hidden name="id_comment" value="{{ $announcement->id}}">

                    <button type="submit" class="btn btn-success pull-right" {{-- id="btn_send_comment" --}} style="margin-top: 5px;">{!!trans('main.Send')!!}</button>
                </form>
            </div>
        </div>
        <span id="announcements" data-announ-id="{{$announcement->id}}"></span>
    </section>
@endsection
@push('scripts')
<script src="{{ asset('js/comment.js') }}"></script>
@endpush