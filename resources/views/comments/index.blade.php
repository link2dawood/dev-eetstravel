@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Comments')
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
                <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                    <div class="input-icon flex-grow-1" style="max-width: 420px;">
                        <span class="input-icon-addon"><i class="ti ti-search"></i></span>
                        <input type="text" class="form-control" placeholder="Search comments or sender..." name="search" value="{{ request('search') }}" data-list-search>
                    </div>
                </div>
                <div class="table-responsive">
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
                            {{-- comments are stored as HTML: show readable text --}}
                            <td data-delete-label>{{ \Illuminate\Support\Str::limit(trim(html_entity_decode(strip_tags((string) $comment->content))), 120) ?: '-' }}</td>
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
                    @if($commentsData->isEmpty())
                        <tr><td colspan="5" class="text-center text-muted py-4">{{ request('search') ? 'No comments match your search' : 'No comments yet' }}</td></tr>
                    @endif
                    </tbody>
                </table>
                </div>
                @if($commentsData->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">Showing {{ $commentsData->firstItem() }} to {{ $commentsData->lastItem() }} of {{ $commentsData->total() }} comments</div>
                    <div>{{ $commentsData->links() }}</div>
                </div>
                @endif
            </div>
        </div>

    </section>
@endsection

@include('component.delete_modal_simple')

@push('scripts')
<script src="{{ asset('js/list-search.js') }}"></script>
@endpush
