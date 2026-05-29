@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Restaurant Details')

@section('content')
<x-ui.page-header
    :title="$restaurant->name"
    description="Restaurant supplier record"
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Restaurants', 'href' => route('restaurant.index')],
        ['label' => $restaurant->name],
    ]"
>
    <x-slot name="actions">
        <x-ui.button as="a" href="{{ route('restaurant.index') }}" variant="ghost" icon="arrow-left">
            {{ trans('main.Back') ?? 'Back' }}
        </x-ui.button>
        @if(Auth::user()->can('restaurant.edit'))
            <x-ui.button as="a" href="{{ route('restaurant.edit', $restaurant->id) }}" variant="secondary" icon="edit">
                {{ trans('main.Edit') ?? 'Edit' }}
            </x-ui.button>
        @endif
    </x-slot>
</x-ui.page-header>

@php
    $tabBase   = 'group inline-flex items-center gap-2 whitespace-nowrap border-b-2 px-1 pb-3 pt-3 text-sm transition-colors border-transparent text-slate-600 hover:text-slate-900 hover:border-slate-300';
    $tabActive = '[&.active]:border-primary-600 [&.active]:text-primary-700 [&.active]:font-medium';
    $tabClass  = $tabBase . ' ' . $tabActive;
@endphp

<div class="rounded border border-slate-200 bg-white">
    <div class="border-b border-slate-200 px-1" role="tablist">
        <ul class="nav nav-tabs nav-tabs-underline -mb-px flex flex-nowrap gap-6 overflow-x-auto border-0 px-3 list-none pl-0 m-0 [&_.nav-link]:cursor-pointer" data-bs-toggle="tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a href="#info-tab" class="nav-link active {{ $tabClass }}" data-bs-toggle="tab" aria-selected="true" role="tab">
                    <x-ui.icon name="info" />{{ trans('main.Info') }}
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="#history-tab" class="nav-link {{ $tabClass }}" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                    <x-ui.icon name="history" />{{ trans('main.History') }}
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="#menu-tab" class="nav-link {{ $tabClass }}" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                    <x-ui.icon name="list" />{{ trans('main.Menu') }}
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="#invoices-tab" id="invoices_tab" class="nav-link {{ $tabClass }}" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                    <x-ui.icon name="receipt" />{!! trans('Invoices') !!}
                </a>
            </li>
        </ul>
    </div>

    <div class="p-5">
        <div class="tab-content">

            {{-- ============================================================ --}}
            {{-- Info tab --}}
            {{-- ============================================================ --}}
            <div class="tab-pane fade show active" role="tabpanel" id="info-tab">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                    <div class="lg:col-span-2 rounded border border-slate-200 bg-white">
                        <div class="border-b border-slate-200 px-4 py-3 flex items-center gap-2">
                            <x-ui.icon name="utensils" size="sm" class="text-slate-400" />
                            <h2 class="text-sm font-medium text-slate-700">Restaurant information</h2>
                        </div>
                        <dl class="px-4 py-4 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{{ trans('main.Name') }}</dt>
                                <dd class="mt-0.5 text-slate-800">{{ $restaurant->name ?: '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{{ trans('main.Code') }}</dt>
                                <dd class="mt-0.5 font-mono text-slate-800">{{ $restaurant->code ?: '—' }}</dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{{ trans('main.AddressFirst') }}</dt>
                                <dd class="mt-0.5 text-slate-800">{{ $restaurant->address_first ?: '—' }}</dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{{ trans('main.AddressSecond') }}</dt>
                                <dd class="mt-0.5 text-slate-800">{{ $restaurant->address_second ?: '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{{ trans('main.Country') }}</dt>
                                <dd class="mt-0.5 text-slate-800">
                                    @if(!empty($restaurant->country))
                                        {{ \App\Helper\CitiesHelper::getCountryById($restaurant->country)['name'] ?? '—' }}
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{{ trans('main.City') }}</dt>
                                <dd class="mt-0.5 text-slate-800">
                                    @if(!empty($restaurant->city))
                                        @if(is_numeric($restaurant->city))
                                            {{ \App\Helper\CitiesHelper::getCityById($restaurant->city)['name'] ?? '—' }}
                                        @else
                                            {{ $restaurant->city }}
                                        @endif
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{{ trans('main.WorkPhone') }}</dt>
                                <dd class="mt-0.5">
                                    @if($restaurant->work_phone)
                                        <a href="tel:{{ $restaurant->work_phone }}" class="text-primary-700 hover:underline">{{ $restaurant->work_phone }}</a>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{{ trans('main.WorkFax') }}</dt>
                                <dd class="mt-0.5 text-slate-800">{{ $restaurant->work_fax ?: '—' }}</dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{{ trans('main.WorkEmail') }}</dt>
                                <dd class="mt-0.5">
                                    @if($restaurant->work_email)
                                        <a href="mailto:{{ $restaurant->work_email }}" class="text-primary-700 hover:underline break-all">{{ $restaurant->work_email }}</a>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{{ trans('main.ContactName') }}</dt>
                                <dd class="mt-0.5 text-slate-800">{{ $restaurant->contact_name ?: '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{{ trans('main.ContactPhone') }}</dt>
                                <dd class="mt-0.5">
                                    @if($restaurant->contact_phone)
                                        <a href="tel:{{ $restaurant->contact_phone }}" class="text-primary-700 hover:underline">{{ $restaurant->contact_phone }}</a>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{{ trans('main.ContactEmail') }}</dt>
                                <dd class="mt-0.5">
                                    @if($restaurant->contact_email)
                                        <a href="mailto:{{ $restaurant->contact_email }}" class="text-primary-700 hover:underline break-all">{{ $restaurant->contact_email }}</a>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{{ trans('main.Rate') }}</dt>
                                <dd class="mt-0.5 text-slate-800">{{ $restaurant->rate_name ?: '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{{ trans('main.Website') }}</dt>
                                <dd class="mt-0.5">
                                    @if($restaurant->website)
                                        <a href="{{ $restaurant->website }}" target="_blank" class="text-primary-700 hover:underline break-all">{{ $restaurant->website }}</a>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{{ trans('main.Comments') }}</dt>
                                <dd class="mt-0.5 text-slate-800 whitespace-pre-line">{{ $restaurant->comments ?: '—' }}</dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{{ trans('main.IntComments') }}</dt>
                                <dd class="mt-0.5 text-slate-800 whitespace-pre-line">{{ $restaurant->int_comments ?: '—' }}</dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{{ trans('main.Criterias') }}</dt>
                                <dd class="mt-0.5 flex flex-wrap gap-1">
                                    @php $hasCrit = false; @endphp
                                    @foreach($criterias as $criteria)
                                        @foreach($restaurant->criterias as $item)
                                            @if($criteria->id == $item->criteria_id)
                                                @php $hasCrit = true; @endphp
                                                <span class="inline-flex items-center rounded bg-info-50 px-2 py-0.5 text-xs font-medium text-info-700">{{ $criteria->name }}</span>
                                            @endif
                                        @endforeach
                                    @endforeach
                                    @if(!$hasCrit)<span class="text-slate-400 text-sm">—</span>@endif
                                </dd>
                            </div>
                        </dl>
                    </div>

                    {{-- Files side panel --}}
                    <div class="rounded border border-slate-200 bg-white">
                        <div class="border-b border-slate-200 px-4 py-3 flex items-center gap-2">
                            <x-ui.icon name="paperclip" size="sm" class="text-slate-400" />
                            <h2 class="text-sm font-medium text-slate-700">Files</h2>
                        </div>
                        @php
                            $images = collect($files['image'] ?? [])
                                ->filter(fn($i) => !empty($i->attach_file_name))
                                ->values();
                            $attachments = collect($files['attach'] ?? [])
                                ->filter(fn($a) => !empty($a->attach_file_name))
                                ->values();
                            $imagePreviewLimit = 4;
                            $attachPreviewLimit = 5;
                            $previewImages = $images->take($imagePreviewLimit);
                            $previewAttach = $attachments->take($attachPreviewLimit);
                            $hiddenImages  = max(0, $images->count() - $imagePreviewLimit);
                            $hiddenAttach  = max(0, $attachments->count() - $attachPreviewLimit);
                            $totalCount = $images->count() + $attachments->count();
                        @endphp
                        @if($totalCount === 0)
                            <div class="px-4 py-8 text-center">
                                <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-slate-400 mb-2">
                                    <x-ui.icon name="paperclip" />
                                </div>
                                <p class="text-sm font-medium text-slate-700">No files yet</p>
                                <p class="mt-1 text-xs text-slate-500">Attach contracts, menus, anything related from the Edit page.</p>
                            </div>
                        @else
                            @if($images->count())
                                <div class="px-4 pt-4">
                                    <h3 class="text-xs font-medium uppercase tracking-wide text-slate-500 mb-2">Photos <span class="ml-1 text-slate-400 normal-case font-normal">({{ $images->count() }})</span></h3>
                                    <div class="image grid grid-cols-2 gap-2">
                                        @foreach($previewImages as $image)
                                            @php $imgUrl = asset('storage/' . $image->attach_file_name); @endphp
                                            <div class="del-container relative group rounded overflow-hidden border border-slate-200 bg-slate-50">
                                                <a href="{{ $imgUrl }}" class="block">
                                                    <img src="{{ $imgUrl }}" alt="" loading="lazy" class="w-full h-24 object-cover" />
                                                </a>
                                                <button type="button" class="del-attach absolute top-1 right-1 inline-flex h-6 w-6 items-center justify-center rounded-full bg-white/90 text-danger-600 shadow-subtle opacity-0 group-hover:opacity-100 transition-opacity"
                                                        data-attach-url="{{ route('file_delete', ['id' => $image->id]) }}" aria-label="Delete photo">
                                                    <x-ui.icon name="x" size="xs" />
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                    @if($hiddenImages > 0)
                                        <button type="button" onclick="filesModalOpen('images')" class="mt-3 inline-flex items-center gap-1 text-xs font-medium text-primary-700 hover:text-primary-800">
                                            View all photos ({{ $images->count() }})<x-ui.icon name="arrow-right" size="xs" />
                                        </button>
                                    @endif
                                </div>
                            @endif
                            @if($attachments->count())
                                <div class="px-4 pt-4 pb-4">
                                    <h3 class="text-xs font-medium uppercase tracking-wide text-slate-500 mb-2">Files <span class="ml-1 text-slate-400 normal-case font-normal">({{ $attachments->count() }})</span></h3>
                                    <ul class="divide-y divide-slate-100 list-none pl-0 m-0">
                                        @foreach($previewAttach as $attach)
                                            @php
                                                $fileUrl = asset('storage/' . $attach->attach_file_name);
                                                $displayName = basename($attach->attach_file_name);
                                            @endphp
                                            <li class="del-container py-2 flex items-center gap-3">
                                                <span class="flex h-8 w-8 items-center justify-center rounded bg-slate-100 text-slate-500 shrink-0">
                                                    <x-ui.icon name="paperclip" size="sm" />
                                                </span>
                                                <div class="min-w-0 flex-1">
                                                    <a href="{{ $fileUrl }}" target="_blank" class="link_file block text-sm font-medium text-slate-700 hover:text-primary-700 truncate">
                                                        <span class="name_link_file">{{ $displayName }}</span>
                                                    </a>
                                                    <p class="text-xs text-slate-500 mt-0.5">{{ $attach->created_at }}</p>
                                                </div>
                                                <button type="button" class="del-attach inline-flex h-7 w-7 items-center justify-center rounded text-slate-400 hover:bg-danger-50 hover:text-danger-700 shrink-0"
                                                        data-attach-url="{{ route('file_delete', ['id' => $attach->id]) }}" aria-label="Delete file">
                                                    <x-ui.icon name="trash-2" size="sm" />
                                                </button>
                                            </li>
                                        @endforeach
                                    </ul>
                                    @if($hiddenAttach > 0)
                                        <button type="button" onclick="filesModalOpen('attachments')" class="mt-3 inline-flex items-center gap-1 text-xs font-medium text-primary-700 hover:text-primary-800">
                                            View all files ({{ $attachments->count() }})<x-ui.icon name="arrow-right" size="xs" />
                                        </button>
                                    @endif
                                </div>
                            @endif
                        @endif
                    </div>
                </div>

                {{-- Comments — anchor IDs preserved for comment.js --}}
                <span id="showPreviewBlock" data-info="true" hidden></span>
                <div class="mt-6 rounded border border-slate-200 bg-white">
                    <div class="border-b border-slate-200 px-4 py-3 flex items-center gap-2">
                        <x-ui.icon name="message-circle" size="sm" class="text-slate-400" />
                        <h2 class="text-sm font-medium text-slate-700">{{ trans('main.Comments') }}</h2>
                    </div>
                    <div class="px-4 py-4">
                        <div class="max-h-80 overflow-y-auto">
                            <div id="show_comments"></div>
                        </div>
                    </div>
                    <div class="border-t border-slate-200 bg-slate-50 px-4 py-4 rounded-b">
                        <form method="POST" action="{{ route('comment.store') }}" enctype="multipart/form-data" id="form_comment" class="space-y-3">
                            @csrf
                            <div>
                                <span id="author_name" class="hidden mb-2 inline-flex items-center gap-2 rounded bg-primary-50 px-2 py-1 text-xs text-primary-700">
                                    Replying to <span id="name" class="font-medium"></span>
                                    <a href="#" id="reply_close" class="text-primary-700/70 hover:text-primary-900"><x-ui.icon name="x" size="xs" /></a>
                                </span>
                                <textarea class="form-control block w-full rounded border border-slate-300 bg-white px-3 py-2 text-sm shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600"
                                          id="content" name="content" rows="3" placeholder="Add a comment — Ctrl + Enter to post"></textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Files</label>
                                @component('component.file_upload_field')@endcomponent
                            </div>
                            <input type="hidden" id="parent_comment" name="parent">
                            <input type="hidden" id="default_reference_id" name="reference_id" value="{{ $restaurant->id }}">
                            <input type="hidden" id="default_reference_type" name="reference_type" value="{{ \App\Comment::$services['restaurant'] }}">
                            <div class="flex">
                                <button type="submit" id="btn_send_comment" class="inline-flex h-9 items-center gap-2 rounded bg-primary-600 px-4 text-sm font-medium text-white hover:bg-primary-700">
                                    <x-ui.icon name="send" size="sm" />
                                    {{ trans('main.Send') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- History tab — JS-injected --}}
            {{-- ============================================================ --}}
            <div class="tab-pane fade" role="tabpanel" id="history-tab">
                <div id="history-container" class="text-sm text-slate-600"></div>
            </div>

            {{-- ============================================================ --}}
            {{-- Menu tab --}}
            {{-- ============================================================ --}}
            <div class="tab-pane fade" role="tabpanel" id="menu-tab">
                <div class="mb-4 flex items-center justify-between">
                    <h4 class="text-sm font-medium text-slate-700">{{ trans('main.Menu') }}</h4>
                    @if(Auth::user()->can('menu.create'))
                        <x-ui.button as="a" href="{{ route('menu.create', ['type' => 'restaurant', 'id' => $restaurant->id]) }}" icon="plus">
                            {{ trans('main.AddMenu') }}
                        </x-ui.button>
                    @endif
                </div>

                @if($restaurant->menus->count())
                    {{-- Desktop table --}}
                    <div class="hidden md:block overflow-x-auto rounded border border-slate-200">
                        <table class="min-w-full divide-y divide-slate-200 text-sm finder-disable">
                            <thead class="bg-slate-50">
                                <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                                    <th class="px-4 py-3">{{ trans('main.Name') }}</th>
                                    <th class="px-4 py-3">{{ trans('main.Price') }}</th>
                                    <th class="px-4 py-3">{{ trans('main.Description') }}</th>
                                    <th class="px-4 py-3 text-right">{{ trans('main.Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($restaurant->menus as $menu)
                                    <tr class="hover:bg-slate-50">
                                        <td class="px-4 py-3 font-medium text-slate-900">
                                            <a href="{{ route('menu.show', ['menu' => $menu->id]) }}" class="text-primary-700 hover:underline">{{ $menu->name }}</a>
                                        </td>
                                        <td class="px-4 py-3 text-slate-700">{{ $menu->price }}</td>
                                        <td class="px-4 py-3 text-slate-700">{!! $menu->description !!}</td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center justify-end gap-1">
                                                @if(Auth::user()->can('menu.edit'))
                                                    <a href="{{ route('menu.edit', ['menu' => $menu->id]) }}"
                                                       class="inline-flex h-7 w-7 items-center justify-center rounded text-slate-500 hover:bg-slate-100 hover:text-primary-700"
                                                       title="Edit">
                                                        <x-ui.icon name="edit" size="sm" />
                                                    </a>
                                                @endif
                                                @if(Auth::user()->can('menu.destroy_menu'))
                                                    <a href="{{ route('menu.delete', ['menu' => $menu->id], false) }}"
                                                       class="inline-flex h-7 w-7 items-center justify-center rounded text-slate-500 hover:bg-danger-50 hover:text-danger-700"
                                                       title="Delete">
                                                        <x-ui.icon name="trash-2" size="sm" />
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{-- Mobile cards --}}
                    <div class="md:hidden space-y-3">
                        @foreach($restaurant->menus as $menu)
                            <div class="rounded border border-slate-200 bg-white p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0 flex-1">
                                        <a href="{{ route('menu.show', ['menu' => $menu->id]) }}" class="block font-medium text-slate-900 hover:text-primary-700 truncate">{{ $menu->name }}</a>
                                        @if($menu->price)
                                            <p class="text-xs text-slate-500 mt-0.5">{{ $menu->price }}</p>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-1 shrink-0">
                                        @if(Auth::user()->can('menu.edit'))
                                            <a href="{{ route('menu.edit', ['menu' => $menu->id]) }}"
                                               class="inline-flex h-8 w-8 items-center justify-center rounded text-slate-500 hover:bg-slate-100 hover:text-primary-700"
                                               aria-label="Edit">
                                                <x-ui.icon name="edit" size="sm" />
                                            </a>
                                        @endif
                                        @if(Auth::user()->can('menu.destroy_menu'))
                                            <a href="{{ route('menu.delete', ['menu' => $menu->id], false) }}"
                                               class="inline-flex h-8 w-8 items-center justify-center rounded text-slate-500 hover:bg-danger-50 hover:text-danger-700"
                                               aria-label="Delete">
                                                <x-ui.icon name="trash-2" size="sm" />
                                            </a>
                                        @endif
                                    </div>
                                </div>
                                @if($menu->description)
                                    <div class="mt-2 text-xs text-slate-600">{!! $menu->description !!}</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <x-ui.empty-state icon="list" title="No menu items yet" message="Add menu items from the Add Menu button above." />
                @endif
            </div>

            {{-- ============================================================ --}}
            {{-- Invoices tab --}}
            {{-- ============================================================ --}}
            <div class="tab-pane fade" role="tabpanel" id="invoices-tab">
                <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        {!! \App\Helper\PermissionHelper::getCreateButton(route('invoices.create'), \App\Invoices::class) !!}
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="relative flex-1 max-w-xs">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                <x-ui.icon name="search" size="sm" />
                            </span>
                            <input type="text" id="restaurantInvoiceSearchInput"
                                   class="block w-full h-9 rounded border border-slate-300 bg-white pl-9 pr-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600"
                                   placeholder="Search invoices..."
                                   onkeyup="filterRestaurantInvoiceTable()">
                        </div>
                        <x-ui.button variant="secondary" icon="download" size="sm" onclick="exportRestaurantInvoicesToCSV()">Export CSV</x-ui.button>
                        <x-ui.button variant="secondary" icon="file-spreadsheet" size="sm" onclick="exportRestaurantInvoicesToExcel()">Export Excel</x-ui.button>
                    </div>
                </div>

                <div class="hidden md:block overflow-x-auto rounded border border-slate-200">
                    <table id="inovices-table" class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50">
                            <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                                <th class="px-4 py-3">ID</th>
                                <th class="px-4 py-3">Invoice No</th>
                                <th class="px-4 py-3">Due Date</th>
                                <th class="px-4 py-3">Received Date</th>
                                <th class="px-4 py-3">Tour</th>
                                <th class="px-4 py-3">Service</th>
                                <th class="px-4 py-3">Office Name</th>
                                <th class="px-4 py-3">Total Price</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @if(isset($invoices) && $invoices->count() > 0)
                                @foreach($invoices as $invoice)
                                    <tr class="hover:bg-slate-50">
                                        <td class="px-4 py-3 font-mono text-xs text-slate-500">{{ $invoice->id }}</td>
                                        <td class="px-4 py-3 text-slate-700">{{ $invoice->invoice_no ?? 'N/A' }}</td>
                                        <td class="px-4 py-3 text-slate-700">{{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('Y-m-d') : 'N/A' }}</td>
                                        <td class="px-4 py-3 text-slate-700">{{ $invoice->received_date ? \Carbon\Carbon::parse($invoice->received_date)->format('Y-m-d') : 'N/A' }}</td>
                                        <td class="px-4 py-3 text-slate-700">{{ $invoice->tour_name ?? 'N/A' }}</td>
                                        <td class="px-4 py-3 text-slate-700">{{ $invoice->service_name ?? 'N/A' }}</td>
                                        <td class="px-4 py-3 text-slate-700">{{ $invoice->office_name ?? 'N/A' }}</td>
                                        <td class="px-4 py-3 text-slate-700">{{ number_format($invoice->total_amount ?? 0, 2) }}</td>
                                        <td class="px-4 py-3">
                                            @php
                                                $status = $invoice->status ?? 'pending';
                                                $statusClass = $status === 'paid'
                                                    ? 'bg-success-50 text-success-700'
                                                    : ($status === 'pending' ? 'bg-warning-50 text-warning-700' : 'bg-danger-50 text-danger-700');
                                            @endphp
                                            <span class="inline-flex items-center rounded px-2 py-0.5 text-xs font-medium {{ $statusClass }}">{{ ucfirst($status) }}</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center justify-end gap-1">
                                                @if(Auth::user()->can('invoices.show'))
                                                    <a href="{{ route('invoices.show', $invoice->id) }}"
                                                       class="inline-flex h-7 w-7 items-center justify-center rounded text-slate-500 hover:bg-slate-100 hover:text-slate-700"
                                                       title="View"><x-ui.icon name="eye" size="sm" /></a>
                                                @endif
                                                @if(Auth::user()->can('invoices.edit'))
                                                    <a href="{{ route('invoices.edit', $invoice->id) }}"
                                                       class="inline-flex h-7 w-7 items-center justify-center rounded text-slate-500 hover:bg-slate-100 hover:text-primary-700"
                                                       title="Edit"><x-ui.icon name="edit" size="sm" /></a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="10" class="px-4 py-8 text-center text-sm text-slate-500">No invoices found for this restaurant</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                @if(isset($invoices) && method_exists($invoices, 'links'))
                    <div class="mt-4">{{ $invoices->links() }}</div>
                @endif
            </div>

        </div>
    </div>
</div>

<span id="services_name" data-service-name="Restaurant" data-history-route="{{ route('services_history', ['id' => $restaurant->id]) }}" hidden></span>

{{-- "View all" files modal --}}
@if(isset($images) && ($images->count() > 0 || $attachments->count() > 0))
<div id="filesModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true" aria-labelledby="filesModalTitle">
    <div class="absolute inset-0 bg-slate-900/60" onclick="filesModalClose()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4 pointer-events-none">
        <div class="relative w-full max-w-4xl max-h-[90vh] rounded-md bg-white shadow-overlay pointer-events-auto flex flex-col">
            <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between shrink-0">
                <h3 id="filesModalTitle" class="text-sm font-semibold text-slate-900 flex items-center gap-2">
                    <x-ui.icon name="paperclip" size="sm" class="text-slate-400" />
                    <span data-files-modal-title>All files</span>
                </h3>
                <button type="button" onclick="filesModalClose()" class="inline-flex h-8 w-8 items-center justify-center rounded text-slate-400 hover:bg-slate-100 hover:text-slate-700" aria-label="Close">
                    <x-ui.icon name="x" />
                </button>
            </div>
            <div class="overflow-y-auto px-5 py-5 flex-1">
                @if(isset($images) && $images->count())
                    <section data-files-modal-section="images" class="mb-6">
                        <h4 class="text-xs font-medium uppercase tracking-wide text-slate-500 mb-3">Photos <span class="ml-1 text-slate-400 normal-case font-normal">({{ $images->count() }})</span></h4>
                        <div class="image-modal-gallery grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                            @foreach($images as $image)
                                @php $imgUrl = asset('storage/' . $image->attach_file_name); @endphp
                                <a href="{{ $imgUrl }}" class="block group rounded overflow-hidden border border-slate-200 bg-slate-50 aspect-square">
                                    <img src="{{ $imgUrl }}" alt="" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform" />
                                </a>
                            @endforeach
                        </div>
                    </section>
                @endif
                @if(isset($attachments) && $attachments->count())
                    <section data-files-modal-section="attachments">
                        <h4 class="text-xs font-medium uppercase tracking-wide text-slate-500 mb-3">Files <span class="ml-1 text-slate-400 normal-case font-normal">({{ $attachments->count() }})</span></h4>
                        <ul class="divide-y divide-slate-100 list-none pl-0 m-0 rounded border border-slate-200">
                            @foreach($attachments as $attach)
                                @php $fileUrl = asset('storage/' . $attach->attach_file_name); $displayName = basename($attach->attach_file_name); @endphp
                                <li class="px-4 py-3 flex items-center gap-3 hover:bg-slate-50">
                                    <span class="flex h-9 w-9 items-center justify-center rounded bg-slate-100 text-slate-500 shrink-0"><x-ui.icon name="paperclip" size="sm" /></span>
                                    <div class="min-w-0 flex-1">
                                        <a href="{{ $fileUrl }}" target="_blank" class="block text-sm font-medium text-slate-700 hover:text-primary-700 truncate">{{ $displayName }}</a>
                                        <p class="text-xs text-slate-500 mt-0.5">{{ $attach->created_at }}@if(!empty($attach->attach_file_size)) · {{ round($attach->attach_file_size / 1024, 1) }} KB @endif</p>
                                    </div>
                                    <a href="{{ $fileUrl }}" download class="inline-flex h-8 w-8 items-center justify-center rounded text-slate-400 hover:bg-slate-100 hover:text-slate-700" aria-label="Download"><x-ui.icon name="download" size="sm" /></a>
                                </li>
                            @endforeach
                        </ul>
                    </section>
                @endif
            </div>
        </div>
    </div>
</div>
<script>
    window.filesModalOpen = function (mode) {
        var modal = document.getElementById('filesModal'); if (!modal) return;
        var title = modal.querySelector('[data-files-modal-title]');
        if (title) title.textContent = mode === 'attachments' ? 'All files' : 'All photos';
        modal.classList.remove('hidden'); document.body.style.overflow = 'hidden';
        var section = modal.querySelector('[data-files-modal-section="' + mode + '"]');
        if (section) requestAnimationFrame(function () { section.scrollIntoView({ block: 'start', behavior: 'auto' }); });
        if (window.jQuery && jQuery.fn.magnificPopup) {
            jQuery('#filesModal .image-modal-gallery').magnificPopup({ delegate: 'a', type: 'image', gallery: { enabled: true } });
        }
    };
    window.filesModalClose = function () {
        var modal = document.getElementById('filesModal'); if (!modal) return;
        modal.classList.add('hidden'); document.body.style.overflow = '';
    };
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && document.getElementById('filesModal') && !document.getElementById('filesModal').classList.contains('hidden')) window.filesModalClose();
    });
</script>
@endif
@endsection

@section('post_scripts')
    <script src="{{ asset('js/comment.js') }}"></script>
    <script src="{{ asset('js/bootstrap-tables.js') }}"></script>
    <script>
        $(document).ready(function () {
            initializeBootstrapTable('inovices-table');

            if ($.fn.magnificPopup) {
                $('.image').magnificPopup({ delegate: 'a', type: 'image', gallery: { enabled: true } });
            }
            $(document).on('click', '.del-attach', function (e) {
                e.preventDefault();
                var btn = this;
                var url = $(btn).attr('data-attach-url');
                if (!url) return;
                if (!confirm('Are you sure you want to delete this attachment?')) return;
                $.ajax({
                    url: url, method: 'POST', data: { "_token": "{{ csrf_token() }}" },
                    success: function () { $(btn).closest('.del-container').hide(); },
                    error:   function (res) { console.log(res); }
                });
            });
        });

        function filterRestaurantInvoiceTable() {
            const input = document.getElementById('restaurantInvoiceSearchInput');
            const filter = input.value.toUpperCase();
            const table = document.getElementById('inovices-table');
            const tr = table.getElementsByTagName('tr');
            for (let i = 1; i < tr.length; i++) {
                let display = false;
                const td = tr[i].getElementsByTagName('td');
                for (let j = 0; j < td.length - 1; j++) {
                    if (td[j]) {
                        const txtValue = td[j].textContent || td[j].innerText;
                        if (txtValue.toUpperCase().indexOf(filter) > -1) { display = true; break; }
                    }
                }
                tr[i].style.display = display ? '' : 'none';
            }
        }

        function exportRestaurantInvoicesToCSV() {
            exportTableToCSV('inovices-table', 'restaurant-invoices.csv');
        }

        function exportRestaurantInvoicesToExcel() {
            exportTableToExcel('inovices-table', 'restaurant-invoices');
        }
    </script>
@endsection
