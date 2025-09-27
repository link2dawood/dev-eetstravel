@extends('scaffold-interface.layouts.app')
@section('title','Edit')
@section('content')
    @include('layouts.title',
   ['title' => 'Comment', 'sub_title' => 'Comment Edit',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Comments', 'icon' => 'comment', 'route' => route('comment.index')],
   ['title' => 'Edit', 'route' => null]]])
    <section class="content">
        <div class="box box-primary">
            <div class="box box-body border_top_none">
                <form method='POST' action='{{route('comment.update', ['comment' => $comment->id])}}' enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="margin_button">
                                <a href="javascript:history.back()">
                                    <button class='btn btn-primary back_btn' type="button">{!!trans('main.Back')!!}</button>
                                </a>
                                <button class='btn btn-success' type='submit'>{!!trans('main.Save')!!}</button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">

                                {{csrf_field()}}
                                {{method_field('PUT')}}
                                <div class="form-group">
                                    <label for="content">{!!trans('main.Content')!!}</label>
                                    {!! Form::textarea('content', $comment->content, ['id' => 'content', 'class' => 'form-control']) !!}
                                </div>
                                <div class="form-group">
                                    <label for="attach">{!!trans('main.Files')!!}</label>
                                    @component('component.file_upload_field')@endcomponent
                                </div>
                                @component('component.files', ['files' => $files])@endcomponent
                                <button class='btn btn-success' type='submit'>{!!trans('main.Save')!!}</button>
                            <a href="{{\App\Helper\AdminHelper::getBackButton(route('comment.index'))}}">
                                <button class='btn btn-warning' type='button'>{!!trans('main.Cancel')!!}</button>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection