@extends('scaffold-interface.layouts.app')
@section('title','Show')
@section('content')
    @include('layouts.title',
           ['title' => 'Email Templates', 'sub_title' => 'Show',
           'breadcrumbs' => [
           ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
           ['title' => 'Email Templates', 'icon' => 'templates', 'route' => route('templates.index')],
           ['title' => 'Show', 'route' => null]]])
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="container-fluid" >
                    <div class="row">
                        <div class="col-lg-6">
                            <a href="{!! route('templates.index') !!}">
                                <button class='btn btn-primary'>{!!trans('main.Back')!!}</button>
                            </a>

                            <button class="btn btn-success" onclick="showModalTemplate();"></i> {!!trans('main.New')!!}</button>

                        </div>
                        <div class="col-lg-6">

                            <span id="help" class="btn btn-box-tool pull-right"><i class="fa fa-question-circle" aria-hidden="true"></i>
                            @include('legend.templates_legend')
                            </span>

                        </div>

                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <br>
                        </div>
                    </div>

                    <div class="row">
                    <div class="col-lg-12">
                <table class = 'table table-bordered'>
                    <tbody>
                    <tr>
                        <td class="show_width_td">
                            <b><i>{!!trans('main.Templates')!!}: </i></b>
                        </td>
                        <td>{!! $service->name !!}</td>
                    </tr>
                    </tbody>
                </table>
                    </div>
                    </div>
                </div>


                <div class="container-fluid" >
                    <div class="row">
                    @foreach ($templates as $template)
                            <div class="col-lg-3">
                                <div class="thumbnail preview" align="center" style="min-height: 374px;max-height: 374px;" >
                                    <a data-toggle="modal" data-target="#myModal" href="#" onclick="initModalTemplate({!! $template->id !!},'{!! $template->name !!}');" >
                                        <canvas id="canvas_{!! $template->id !!}" class="my_shadow" width="200" height="200" style="margin: 20px"></canvas></a>
                                    <div class="caption">
                                        <form method="POST" action="/templates/{!! $template->id !!}/delete" id="deleteForm_{{ $template->id }}" ><p>{{$template->name}}</p><p>{{ date("d F Y",strtotime($template->created_at)) }}</p><p><a data-toggle="modal" data-target="#myModal" href="#" onclick="initModalTemplate({!! $template->id !!},'{!! $template->name !!}');" ><button type="button" class="btn btn-primary btn-sm" ><i class="fa fa-pencil-square-o"></i></button></a>@if($template->name !='Header' && $template->name !='Footer') <button type="button" class="btn btn-danger btn-sm remove_templ" id="{!! $template->id !!}" ><i class="fa fa-trash-o"></i></button>@endif</p>
                                            <div class="template" id="{!! $template->id !!}" style="display: none">
                                                {!! $template->content !!}
                                            </div>
                                            <input name="_token" type="hidden" value="{{ csrf_token() }}">
                                        </form>
                                    </div>
                                </div>
                            </div>
                     @endforeach
                    </div>
                </div>
        <div class="hidden" id="header">
                @if(isset($header))
                    {!! $header->content !!}
                @endif
        </div>
        <div class="hidden" id="footer">
                @if(isset($footer))
                    {!! $footer->content !!}
                @endif
        </div>
        <span id="showPreviewBlock" data-info="{{ true }}"></span>
        <div class="box box-success" style="position: relative; left: 0px; top: 0px;">
            <div class="box-header ui-sortable-handle" style="cursor: move;">
                <i class="fa fa-comments-o"></i>

                <h3 class="box-title">{!!trans('main.Comments')!!}</h3>
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
                        <label>{!!trans('main.Files')!!}</label>
                        @component('component.file_upload_field')@endcomponent
                    </div>
                    <input type="text" id="parent_comment" hidden name="parent" value="{{ null }}">
                    <input type="text" id="default_reference_id" hidden name="reference_id" value="{{ $service->id }}">
                    <input type="text" id="default_reference_type" hidden name="reference_type" value="{{ \App\Comment::$services['Templates']}}">

                    <button type="submit" class="btn btn-success pull-right" id="btn_send_comment" style="margin-top: 5px;">{!!trans('main.Send')!!}</button>
                </form>
            </div>

        </div>
    </section>

    <div class="modal fade" id="myModalDelete" tabindex="-1" role="dialog" aria-labelledby="myModalLabelDelete" >
        <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                        <h4 class="modal-title" id="myModalLabel">{!!trans('main.Warning')!!}!!</h4></div>
                    <div class="modal-body">
                        <div class="modal-body">{!!trans('main.WouldyouliketoremoveThis')!!}?</div></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">{!!trans('main.Close')!!}</button>
                        <button type="button" class="destroy btn btn-primary" onclick="deleteTemplate();" >{!!trans('main.Agree')!!}</button>
                    </div></div></div>
                </div>
        </div>
    </div>

    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false" style="padding-left: 17px;padding-right: 17px;">
        <div class="modal-dialog modal-lg" style="width: 90%;">
            <form class="modal-content" action="#" method="POST" id="myModal">
                <input name="_token" type="hidden" value="{{ csrf_token() }}">
                <input name="id" type="hidden" value="{{ $service->id }}">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"></h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="form-group">
                            <input name="name" id="name" class="form-control" placeholder="Name:" required  oninvalid="this.setCustomValidity('Field required for filling')" onchange="this.setCustomValidity('')" value="">
                        </div>
                        <div class="form-group">
                            <textarea name="content" id="compose-textarea" class="form-control" style="height: 800px; visibility: hidden; display: none;">
                            </textarea>
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <div class="pull-right">
                        <button type="submit" class="btn btn-primary" id="save"><i class="fa fa-file-code-o"></i> {!!trans('main.Save')!!}</button>
                        </div>
                        <button type="reset" class="btn btn-default modal-close" data-dismiss="modal"><i class="fa fa-times"></i> {!!trans('main.Discard')!!}</button>
                    </div>
                    <!-- /.box-footer -->
                </div>

            </form>
        </div>
    </div>

    <style>
        .my_shadow {
            -webkit-box-shadow: 5px 5px 21px 0px rgba(0,0,0,0.41);
            -moz-box-shadow: 5px 5px 21px 0px rgba(0,0,0,0.41);
            box-shadow: 5px 5px 21px 0px rgba(0,0,0,0.41);
        }
    </style>
@endsection


@section('post_scripts')
    <script src="{{ asset('js/comment.js') }}"></script>
    <script type="text/javascript" src="/js/ckeditor/ckeditor.js"></script>
    <script src="{{ asset('js/lib/rasterizeHTML.allinone.js') }}"></script>

    <script type="text/javascript">

        var remove_id = 0;

        $(function () {

            $(document).find('.preview').each(function( index ) {
                let id = $(this).find('.template').attr('id') ;
                let content = $(this).find('.template').html();
                var canvas = document.getElementById('canvas_'+id);

                rasterizeHTML.drawHTML(content,
                    canvas,{zoom: 0.5});
            });

            if($(document).find('#compose-textarea').length > 0) {

                CKEDITOR.replace( 'compose-textarea',{
                    title : false,
                    extraPlugins: 'imageuploader,uicolor',
                    height: '400px'
                } );

                $.fn.modal.Constructor.prototype.enforceFocus = function () {
                    modal_this = this;
                    $(document).on('focusin.modal', function (e) {
                        if (modal_this.$element[0] !== e.target && !modal_this.$element.has(e.target).length
                            // add whatever conditions you need here:
                            &&
                            !$(e.target.parentNode).hasClass('cke_dialog_ui_input_select') && !$(e.target.parentNode).hasClass('cke_dialog_ui_input_text')) {
                            modal_this.$element.focus()
                        }
                    })
                };

            }
        });

        $(document).find('.remove_templ').on('click', function(e) {
            e.preventDefault();
            remove_id = $(this).attr('id');
            $('#myModalDelete').modal('show');

        });

        function  deleteTemplate() {
            $('#deleteForm_'+remove_id).submit();
        }

        function showModalTemplate() {
            $('#myModal').find('form').attr('action', '{{ route('templates.store') }}' );
            $('#myModal').find('.box-title').text('Add Template');
            $('#myModal').find('#name').val('');
            CKEDITOR.instances['compose-textarea'].setData($('#header').html() + '<br><----- Place content here -----><br>' + $('#footer').html());
            $('#myModal').modal('show');
        }

        function initModalTemplate(id,name) {
           let content =  $(document).find('#deleteForm_'+id).find('.template').html();
           $('#myModal').find('#name').val(name);
           $('#myModal').find('#id').val(id);
           $('#myModal').find('.box-title').text('Edit Template');
           $('#myModal').find('form').attr('action', '/templates/'+id+'/update');
           CKEDITOR.instances['compose-textarea'].setData(content);
        }

        $("#myModal" ).submit(function( event ) {
            $("#myModal").find("#save").prop('disabled', true);
        });

    </script>
@endsection



