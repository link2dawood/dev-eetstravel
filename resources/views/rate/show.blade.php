@extends('scaffold-interface.layouts.app')
@section('title','Show')
@section('content')
    @include('layouts.title',
           ['title' => 'Rate', 'sub_title' => 'Rate Show',
           'breadcrumbs' => [
           ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
           ['title' => 'Rates', 'icon' => null, 'route' => route('rate.index')],
           ['title' => 'Show', 'route' => null]]])
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="margin_button">
                            <a href="javascript:history.back()">
                                <button class='btn btn-primary'>{{trans('main.Back')}}</button>
                            </a>
                            <a href="{!! route('rate.edit', $rate->id) !!}">
                                <button class='btn btn-warning'>{{trans('main.Edit')}}</button>
                            </a>
                        </div>
                    </div>
                </div>
                <table class = 'table table-bordered'>
                    <tbody>
                    <tr>
                        <td class="show_width_td">
                            <b><i>{{trans('main.Name')}} : </i></b>
                        </td>
                        <td>{!!$rate->name!!}</td>
                    </tr>
                    <tr>
                        <td class="show_width_td">
                            <b><i>{{trans('main.Mark')}} : </i></b>
                        </td>
                        <td>{!!$rate->mark!!}</td>
                    </tr>
                    <tr>
                        <td class="show_width_td">
                            <b><i>{{trans('main.Ratetype')}}: </i></b>
                        </td>
                        <td>{{ $rate_type == null ? '' : $rate_type->name }}</td>
                    </tr>
                    <tr>
                        <td class="show_width_td">
                            <b><i>{{trans('main.Sortorder')}} : </i></b>
                        </td>
                        <td>{!!$rate->sort_order!!}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <span id="showPreviewBlock" data-info="{{ true }}"></span>
        <div class="box box-success" style="position: relative; left: 0px; top: 0px;">
            <div class="box-header ui-sortable-handle" style="cursor: move;">
                <i class="fa fa-comments-o"></i>

                <h3 class="box-title">{{trans('main.Comments')}}</h3>
            </div>
            <div class="box-body">
                <div class="slimScrollDiv" style="position: relative; overflow-y: scroll;  width: auto;">
                    <div class="box-body box chat" id="chat-box" style="width: auto; height: auto;">
                        <div id="show_comments"></div>
                    </div>
                    <div class="slimScrollRail" style="width: 7px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 7px; background: rgb(51, 51, 51) none repeat scroll 0% 0%; opacity: 0.2; z-index: 90; right: 1px;"></div>
                </div>
            </div>
            <!-- /.chat -->
            <div class="box-footer">
                <form method='POST' action='{{route('comment.store')}}' enctype="multipart/form-data" id="form_comment">
                    <div class="input-group" style="width: 100%">
                                        <span id="author_name" class="input-group-addon">
                                            <span id="name"></span>
                                            <a href="#" id="reply_close"><i class="fa fa-close"></i></a>
                                        </span>
                        <textarea class="form-control" id="content" name="content" placeholder="Ctrl + Enter to post comment"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Files</label>
                        @component('component.file_upload_field')@endcomponent
                    </div>
                    <input type="text" id="parent_comment" hidden name="parent" value="{{ null }}">
                    <input type="text" id="default_reference_id" hidden name="reference_id" value="{{ $rate->id }}">
                    <input type="text" id="default_reference_type" hidden name="reference_type" value="{{ \App\Comment::$services['rate']}}">

                    <button type="submit" class="btn btn-success pull-right" id="btn_send_comment" style="margin-top: 5px;">{{trans('main.Send')}}</button>
                </form>
            </div>
        </div>
    </section>
@endsection

@section('post_scripts')
    <script src="{{ asset('js/comment.js') }}"></script>
@endsection