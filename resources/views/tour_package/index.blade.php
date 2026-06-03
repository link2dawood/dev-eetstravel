@extends('scaffold-interface.layouts.tabler-app')
@section('title','Tour packages')

@section('content')
<x-ui.page-header
    title="Tour packages"
    description="All services defined as tour packages."
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Tour packages'],
    ]"
>
    <x-slot name="actions">
        <x-ui.button as="a" href="{{ url('tour_package') . '/create' }}" variant="primary" icon="plus">
            {{ trans('main.New') }}
        </x-ui.button>
    </x-slot>
</x-ui.page-header>

<div class="rounded border border-slate-200 bg-white shadow-subtle overflow-hidden">
    <div class="overflow-x-auto">
        <table class="table table-striped table-bordered table-hover w-full text-sm" style="background:#fff;">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                    <th class="px-4 py-3">{!! trans('main.name') !!}</th>
                    <th class="px-4 py-3">{!! trans('main.description') !!}</th>
                    <th class="px-4 py-3" style="width: 120px">{!! trans('main.status') !!}</th>
                    <th class="px-4 py-3 text-right" style="width: 160px">{!! trans('main.actions') !!}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($tourPackages as $tour_package)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 text-sm font-medium text-slate-900">{!! $tour_package->name !!}</td>
                        <td class="px-4 py-3 text-sm text-slate-700">{!! $tour_package->description !!}</td>
                        <td class="px-4 py-3">
                            @if($tour_package->status)
                                <span class="inline-flex items-center rounded bg-success-50 px-2 py-0.5 text-xs font-medium text-success-700">
                                    {{ trans('main.Yes') }}
                                </span>
                            @else
                                <span class="inline-flex items-center rounded bg-danger-50 px-2 py-0.5 text-xs font-medium text-danger-700">
                                    {{ trans('main.No') }}
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="inline-flex items-center gap-1">
                                <a href="#"
                                   class="viewShow inline-flex h-8 w-8 items-center justify-center rounded border border-slate-300 bg-white text-warning-700 hover:bg-warning-50"
                                   data-link="/tour_package/{!! $tour_package->id !!}"
                                   title="{{ trans('main.View') ?? 'View' }}">
                                    <x-ui.icon name="info" size="sm" />
                                </a>
                                <a href="/tour_package/{!! $tour_package->id !!}/edit"
                                   class="inline-flex h-8 w-8 items-center justify-center rounded border border-slate-300 bg-white text-primary-700 hover:bg-primary-50"
                                   data-link="/tour_package/{!! $tour_package->id !!}/edit"
                                   title="{{ trans('main.Edit') ?? 'Edit' }}">
                                    <x-ui.icon name="pencil" size="sm" />
                                </a>
                                <a href="#"
                                   class="delete inline-flex h-8 w-8 items-center justify-center rounded border border-slate-300 bg-white text-danger-700 hover:bg-danger-50"
                                   data-toggle="modal" data-target="#myModal"
                                   data-bs-toggle="modal" data-bs-target="#myModal"
                                   data-link="/tour_package/{!! $tour_package->id !!}/deleteMsg"
                                   title="{{ trans('main.Delete') ?? 'Delete' }}">
                                    <x-ui.icon name="trash-2" size="sm" />
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-12 text-center text-sm text-slate-500">
                            <span class="inline-flex items-center gap-2">
                                <x-ui.icon name="package" class="text-slate-400" />
                                No tour packages yet
                            </span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($tourPackages, 'hasPages') && $tourPackages->hasPages())
        <div class="border-t border-slate-200 px-4 py-3 bg-slate-50">
            {!! $tourPackages->render() !!}
        </div>
    @endif
</div>
@endsection
