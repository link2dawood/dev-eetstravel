@extends('scaffold-interface.layouts.tabler-app')
@section('title','Templates')

@section('content')
<x-ui.page-header
    :title="$service->name"
    description="Email templates for this service"
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Email templates', 'href' => route('templates.index')],
        ['label' => $service->name],
    ]"
>
    <x-slot name="actions">
        <x-ui.button as="a" href="{!! route('templates.index') !!}" variant="ghost" icon="arrow-left">{!! trans('main.Back') !!}</x-ui.button>
        <x-ui.button type="button" variant="primary" icon="plus" onclick="showModalTemplate();">{!! trans('main.New') !!}</x-ui.button>
        <span id="help" class="inline-flex h-9 w-9 items-center justify-center rounded border border-slate-300 bg-white text-slate-500 hover:text-slate-700">
            <x-ui.icon name="help-circle" size="sm" />
            @include('legend.templates_legend')
        </span>
    </x-slot>
</x-ui.page-header>

<div class="rounded border border-slate-200 bg-white mb-4">
    <dl class="px-5 py-4 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-sm">
        <div>
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('main.Templates') !!}</dt>
            <dd class="mt-0.5 text-slate-800 font-medium">{!! $service->name !!}</dd>
        </div>
    </dl>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    @foreach ($templates as $template)
        <div class="rounded border border-slate-200 bg-white overflow-hidden">
            <div class="preview text-center px-4 pt-4">
                <a data-toggle="modal" data-target="#myModal" href="#" onclick="initModalTemplate({!! $template->id !!},'{{ addslashes($template->name) }}');">
                    <canvas id="canvas_{!! $template->id !!}" class="my_shadow rounded" width="200" height="200" style="margin: 0 auto 16px;"></canvas>
                </a>
            </div>
            <div class="border-t border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                <form method="POST" action="/templates/{!! $template->id !!}/delete" id="deleteForm_{{ $template->id }}" class="space-y-1">
                    <p class="font-medium text-slate-900 truncate">{{ $template->name }}</p>
                    <p class="text-xs text-slate-500">{{ date("d F Y", strtotime($template->created_at)) }}</p>
                    <div class="flex items-center justify-end gap-1 pt-1">
                        <a data-toggle="modal" data-target="#myModal" href="#" onclick="initModalTemplate({!! $template->id !!},'{{ addslashes($template->name) }}');"
                           class="inline-flex h-7 w-7 items-center justify-center rounded border border-slate-300 bg-white text-primary-600 hover:bg-primary-50">
                            <x-ui.icon name="edit" size="xs" />
                        </a>
                        @if($template->name != 'Header' && $template->name != 'Footer')
                            <button type="button" id="{!! $template->id !!}"
                                    class="remove_templ inline-flex h-7 w-7 items-center justify-center rounded border border-slate-300 bg-white text-danger-600 hover:bg-danger-50">
                                <x-ui.icon name="trash" size="xs" />
                            </button>
                        @endif
                    </div>
                    <div class="template hidden" id="{!! $template->id !!}">
                        {!! $template->content !!}
                    </div>
                    <input name="_token" type="hidden" value="{{ csrf_token() }}">
                </form>
            </div>
        </div>
    @endforeach
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

<div class="mt-6 rounded border border-slate-200 bg-white">
    <div class="border-b border-slate-200 px-5 py-3 flex items-center gap-2">
        <x-ui.icon name="messages-square" size="sm" class="text-slate-400" />
        <h3 class="text-sm font-medium text-slate-700">{!! trans('main.Comments') !!}</h3>
    </div>
    <div class="px-5 py-4">
        <div id="chat-box" class="chat box max-h-96 overflow-y-auto pr-1">
            <div id="show_comments"></div>
        </div>
    </div>
    <div class="border-t border-slate-200 bg-slate-50 px-5 py-4">
        <form method="POST" action="{{ route('comment.store') }}" enctype="multipart/form-data" id="form_comment" class="space-y-3">
            @csrf
            <div class="input-group flex flex-col gap-2">
                <div id="author_name" class="input-group-addon hidden items-center gap-2 rounded border border-slate-200 bg-white px-3 py-1.5 text-xs text-slate-600">
                    <span id="name"></span>
                    <a href="#" id="reply_close" class="text-slate-400 hover:text-slate-600"><x-ui.icon name="x" size="xs" /></a>
                </div>
                <textarea class="form-control block w-full rounded border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600"
                          id="content" name="content" rows="3" placeholder="Ctrl + Enter to post comment"></textarea>
            </div>
            <div>
                <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.Files') !!}</label>
                @component('component.file_upload_field')@endcomponent
            </div>
            <input type="text" id="parent_comment" hidden name="parent" value="{{ null }}">
            <input type="text" id="default_reference_id" hidden name="reference_id" value="{{ $service->id }}">
            <input type="text" id="default_reference_type" hidden name="reference_type" value="{{ \App\Comment::$services['Templates'] }}">

            <div class="flex justify-end">
                <x-ui.button type="submit" variant="primary" icon="send" id="btn_send_comment">{!! trans('main.Send') !!}</x-ui.button>
            </div>
        </form>
    </div>
</div>

{{-- Delete confirmation modal --}}
<div class="modal fade" id="myModalDelete" tabindex="-1" role="dialog" aria-labelledby="myModalLabelDelete">
    <div class="modal-dialog" role="document">
        <div class="modal-content rounded border border-slate-200 bg-white shadow-lg">
            <div class="modal-header border-b border-slate-200 px-5 py-3 flex items-center justify-between">
                <h4 class="modal-title text-sm font-medium text-slate-700" id="myModalLabel">{!! trans('main.Warning') !!}!</h4>
                <button type="button" class="close text-slate-400 hover:text-slate-600" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body px-5 py-4 text-sm text-slate-700">{!! trans('main.WouldyouliketoremoveThis') !!}?</div>
            <div class="modal-footer border-t border-slate-200 bg-slate-50 px-5 py-3 flex items-center justify-end gap-2">
                <x-ui.button type="button" variant="secondary" data-dismiss="modal">{!! trans('main.Close') !!}</x-ui.button>
                <x-ui.button type="button" variant="danger" class="destroy" onclick="deleteTemplate();">{!! trans('main.Agree') !!}</x-ui.button>
            </div>
        </div>
    </div>
</div>

{{-- Add / edit template modal --}}
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
    <div class="modal-dialog modal-lg" style="width: 90%;">
        <form class="modal-content rounded border border-slate-200 bg-white shadow-lg" action="#" method="POST" id="myModal">
            <input name="_token" type="hidden" value="{{ csrf_token() }}">
            <input name="id" type="hidden" value="{{ $service->id }}">
            <div class="modal-header border-b border-slate-200 px-5 py-3">
                <h3 class="box-title text-sm font-medium text-slate-700"></h3>
            </div>
            <div class="modal-body px-5 py-4 space-y-3">
                <div>
                    <label for="name" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.Name') ?? 'Name' }}</label>
                    <input name="name" id="name" required
                           oninvalid="this.setCustomValidity('Field required for filling')" onchange="this.setCustomValidity('')"
                           class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600"
                           placeholder="Name:" value="">
                </div>
                <div>
                    <textarea name="content" id="compose-textarea" class="form-control" style="height: 800px; visibility: hidden; display: none;"></textarea>
                </div>
            </div>
            <div class="modal-footer border-t border-slate-200 bg-slate-50 px-5 py-3 flex items-center justify-between">
                <x-ui.button type="reset" variant="secondary" class="modal-close" data-dismiss="modal" icon="x">{!! trans('main.Discard') !!}</x-ui.button>
                <x-ui.button type="submit" variant="primary" id="save" icon="save">{!! trans('main.Save') !!}</x-ui.button>
            </div>
        </form>
    </div>
</div>

<style>
    .my_shadow {
        box-shadow: 0 4px 12px -2px rgba(15, 23, 42, 0.15);
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
            $(document).find('.preview').each(function (index) {
                let id = $(this).find('.template').attr('id');
                let content = $(this).find('.template').html();
                var canvas = document.getElementById('canvas_' + id);
                if (canvas && typeof rasterizeHTML !== 'undefined') {
                    rasterizeHTML.drawHTML(content, canvas, { zoom: 0.5 });
                }
            });

            if ($(document).find('#compose-textarea').length > 0 && typeof CKEDITOR !== 'undefined') {
                CKEDITOR.replace('compose-textarea', {
                    title: false,
                    extraPlugins: 'imageuploader,uicolor',
                    height: '400px'
                });

                if ($.fn.modal && $.fn.modal.Constructor) {
                    $.fn.modal.Constructor.prototype.enforceFocus = function () {
                        var modal_this = this;
                        $(document).on('focusin.modal', function (e) {
                            if (modal_this.$element[0] !== e.target && !modal_this.$element.has(e.target).length
                                && !$(e.target.parentNode).hasClass('cke_dialog_ui_input_select')
                                && !$(e.target.parentNode).hasClass('cke_dialog_ui_input_text')) {
                                modal_this.$element.focus();
                            }
                        });
                    };
                }
            }
        });

        $(document).find('.remove_templ').on('click', function (e) {
            e.preventDefault();
            remove_id = $(this).attr('id');
            $('#myModalDelete').modal('show');
        });

        function deleteTemplate() {
            $('#deleteForm_' + remove_id).submit();
        }

        function showModalTemplate() {
            $('#myModal').find('form').attr('action', '{{ route('templates.store') }}');
            $('#myModal').find('.box-title').text('Add Template');
            $('#myModal').find('#name').val('');
            if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances['compose-textarea']) {
                CKEDITOR.instances['compose-textarea'].setData($('#header').html() + '<br><----- Place content here -----><br>' + $('#footer').html());
            }
            $('#myModal').modal('show');
        }

        function initModalTemplate(id, name) {
            let content = $(document).find('#deleteForm_' + id).find('.template').html();
            $('#myModal').find('#name').val(name);
            $('#myModal').find('#id').val(id);
            $('#myModal').find('.box-title').text('Edit Template');
            $('#myModal').find('form').attr('action', '/templates/' + id + '/update');
            if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances['compose-textarea']) {
                CKEDITOR.instances['compose-textarea'].setData(content);
            }
        }

        $('#myModal').submit(function (event) {
            $('#myModal').find('#save').prop('disabled', true);
        });
    </script>
@endsection
