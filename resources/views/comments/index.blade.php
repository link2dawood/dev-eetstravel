@extends('scaffold-interface.layouts.tabler-app')
@section('title','Index')
@section('content')
    @include('layouts.title',
    ['title' => 'Comments', 'sub_title' => 'Comments List',
    'breadcrumbs' => [
    ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
    ['title' => 'Comments', 'icon' => 'comment', 'route' => null]]])
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                @if (session('not_found'))
                    <div class="alert alert-info">
                        {{session('not_found')}}
                    </div>
                @endif
                <table id="comment-table" class="table table-striped table-bordered table-hover" style='background:#fff'>
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>{!!trans('main.Content')!!}</th>
                        <th>{!!trans('main.Time')!!}</th>
                        <th>{!!trans('main.Sender')!!}</th>
                        <th class="actions-button" style="width: 140px">{!!trans('main.Actions')!!}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($commentsData as $comment)
                        <tr>
                            <td>{{ $comment->id }}</td>
                            <td data-delete-label>{{ $comment->content }}</td>
                            <td>{{ $comment->created_at }}</td>
                            <td>{{ $comment->sender }}</td>
                            <td>
                                <div class="btn-list flex-nowrap">
                                    @include('component.action_buttons', [
                                        'item' => $comment,
                                        'routePrefix' => 'comment'
                                    ])
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </section>
@endsection

@include('component.delete_modal_simple')
