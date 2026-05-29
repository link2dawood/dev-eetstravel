@extends('scaffold-interface.layouts.tabler-app')
@section('title','Comments')

@section('content')
<x-ui.page-header
    title="Comments"
    description="Comments left on tours, services, and tasks across the system."
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Comments'],
    ]"
/>

@if(session('not_found'))
    <div class="mb-4 flex items-start gap-3 rounded border border-info-600/20 bg-info-50 px-4 py-3 text-sm text-info-700">
        <x-ui.icon name="info" class="mt-0.5 text-info-600" />
        <div class="flex-1">{{ session('not_found') }}</div>
    </div>
@endif

@if(count($commentsData) === 0)
    <div class="rounded border border-slate-200 bg-white">
        <x-ui.empty-state icon="messages" title="No comments yet" message="When users leave a comment on a tour, service, or task it will appear here." />
    </div>
@else
    <div class="rounded border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table id="comment-table" class="min-w-full divide-y divide-slate-200 text-sm" style="background:#fff">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                        <th class="px-4 py-3">ID</th>
                        <th class="px-4 py-3">{!! trans('main.Content') !!}</th>
                        <th class="px-4 py-3">{!! trans('main.Time') !!}</th>
                        <th class="px-4 py-3">{!! trans('main.Sender') !!}</th>
                        <th class="px-4 py-3 text-right actions-button" style="width: 140px">{!! trans('main.Actions') !!}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($commentsData as $comment)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-mono text-xs text-slate-500">#{{ $comment->id }}</td>
                            <td class="px-4 py-3 text-slate-800 max-w-xl truncate" data-delete-label>{{ $comment->content }}</td>
                            <td class="px-4 py-3 text-xs font-mono text-slate-500 whitespace-nowrap">{{ $comment->created_at }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $comment->sender }}</td>
                            <td class="px-4 py-3">
                                <div class="btn-list flex-nowrap flex items-center justify-end gap-1">
                                    @include('component.action_buttons', ['item' => $comment, 'routePrefix' => 'comment'])
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
@endsection

@include('component.delete_modal_simple')
