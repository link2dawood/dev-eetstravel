@extends('scaffold-interface.layouts.tabler-app')
@section('title','Email templates')

@section('post_styles')
    @include('component.datatables_cdn')
@endsection

@section('content')
<x-ui.page-header
    title="Email templates"
    description="Templates used to send email notifications across services."
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Email templates'],
    ]"
/>

<div class="rounded border border-slate-200 bg-white">
    <div class="overflow-x-auto">
        <table id="templates" class="min-w-full divide-y divide-slate-200 text-sm" style="background:#fff;width: 100%;">
            <thead class="bg-slate-50">
                <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                    <th class="px-4 py-3" style="width:100%">{!! trans('main.ServiceName') !!}</th>
                    <th class="px-4 py-3 text-right">{!! trans('main.Actions') !!}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($templatesData as $template)
                    <tr class="hover:bg-slate-50 cursor-pointer">
                        <td class="px-4 py-3 font-medium text-slate-900">{{ $template->name }}</td>
                        <td class="px-4 py-3"><div class="flex items-center justify-end gap-1">{!! $template->action_buttons !!}</div></td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th>{!! trans('main.ServiceName') !!}</th>
                    <th>{!! trans('main.Actions') !!}</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        if (!$.fn.DataTable) return;
        $('#templates').DataTable({
            columnDefs: [{ targets: [1], orderable: false }]
        });
        $('#templates').find('tfoot').remove();
        $('#templates tbody').on('click', 'tr', function () {
            let url = $(this).find('a').attr('href');
            if (url) { window.location.href = url; }
        });
    });
</script>
@endpush
