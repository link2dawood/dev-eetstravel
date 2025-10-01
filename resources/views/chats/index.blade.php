@extends('scaffold-interface.layouts.app')
@section('title','Index')
@section('content')
    @include('layouts.title', [
                    'title' => 'Chats',
                     'sub_title' => 'List'])
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                <br>
                <table id="chats-table" class="table table-striped table-bordered table-hover" style='background:#fff'>
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>{!!trans('main.Title')!!}</th>
                        <th>{!!trans('main.Description')!!}</th>
                        <th>{!!trans('main.Type')!!}</th>
                        <th>{!!trans('main.Author')!!}</th>
                        <th class="actions-button" style="width: 140px">{!!trans('main.Actions')!!}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($chatsData as $chat)
                        <tr>
                            <td>{{ $chat->id }}</td>
                            <td>{{ $chat->title }}</td>
                            <td>{{ $chat->description }}</td>
                            <td>{{ $chat->type }}</td>
                            <td>{{ $chat->author }}</td>
                            <td>{!! $chat->action_buttons !!}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </section>
@endsection
