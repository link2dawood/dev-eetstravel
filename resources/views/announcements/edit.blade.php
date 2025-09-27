@extends('scaffold-interface.layouts.app')
@section('title','Create')
@section('content')
    @include('layouts.title',
   ['title' => 'Announcement', 'sub_title' => 'Announcement Edit',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Announcements', 'icon' => 'coffee', 'route' => route('announcements.index')],
   ['title' => 'Edit', 'route' => null]]])
    <section class="content">
        <div class="box box-primary">
            <div class="box box-body border_top_none">
                <form method='POST' action='{{route('announcements.update', ['announcement' => $announcement->id])}}' enctype="multipart/form-data">
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
                                <div class="form-group {{$errors->has('title') ? 'has-error' : ''}}">
                                    <label for="title">{!!trans('main.Title')!!}</label>
                                    {!! Form::input('text', 'title', $announcement->title, ['class' => 'form-control', 'id' => 'title']) !!}
                                    @if($errors->has('title'))
                                        <strong>{{$errors->first('title')}}</strong>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="content">{!!trans('main.Content')!!}</label>
                                    {!! Form::textarea('content', $announcement->content, ['id' => 'content', 'class' => 'form-control']) !!}
                                </div>
                                <div class="form-group">
                                    <label for="attach">{!!trans('main.Files')!!}</label>
                                    @component('component.file_upload_field')@endcomponent
                                </div>
                                @component('component.files', ['files' => $files])@endcomponent

                            <button class='btn btn-success' type='submit'>{!!trans('main.Save')!!}</button>
                            <a href="{{\App\Helper\AdminHelper::getBackButton(route('announcements.index'))}}">
                                <button class='btn btn-warning' type='button'>{!!trans('main.Cancel')!!}</button>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection