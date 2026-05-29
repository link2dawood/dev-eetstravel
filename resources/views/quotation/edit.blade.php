{{--
    /quotation/{id}/edit — full quotation editor.
    Visual chrome migrated to Tailwind. The grid + calculation behaviour
    is driven by resources/js/quotation.js + jquery.repeater. Every JS
    hook below is preserved verbatim:
      - #quotation_id (hidden input, .update-quotation reads it)
      - #quotation_name, .validate-name, .hide, .namesToggle, .hideTitle
      - #quotation_table div + tbody, .quotation-row + data-row,
        data-column + data-value on every cell
      - .additional-column (th), .additional-cell, .remove-quotation-column,
        .quotation-add-column, .quotation-cell-general, .quotation-cell-per-person
      - .calculate-quotation, .update-quotation, .confirm-button,
        .confirm_cancel-button (button click handlers)
      - #rate, #mark_up, #note, #note_show
      - #summary, #summary_all (JS populates rows by data-row)
      - #calculation, #netto_first, #netto_second, #netto_third,
        #netto_fourth (JS-injected tables on the Calculation tab)
      - .repeater, data-repeater-list="package_menu", data-repeater-item,
        data-repeater-create, data-repeater-delete, .package-menu-item,
        .active-person
      - #help (legend tooltip trigger)
      - quotationId + calculationArray globals
--}}
@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Edit Quotation')

@section('content')
<x-ui.page-header
    :title="$quotation->name ?: 'Edit quotation'"
    :description="optional($quotation->tour)->name ? 'Tour: ' . $quotation->tour->name : null"
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Quotations', 'href' => route('quotation.index')],
        ['label' => $quotation->name ?: 'Edit'],
    ]"
>
    <x-slot name="actions">
        <x-ui.button as="a" href="javascript:history.back()" variant="ghost" icon="arrow-left">
            {{ trans('main.Back') }}
        </x-ui.button>
        <x-ui.button type="button" variant="secondary" icon="calculator" class="calculate-quotation">
            Calculate
        </x-ui.button>
        <x-ui.button type="button" variant="primary" icon="save" class="update-quotation">
            {{ trans('main.Save') }}
        </x-ui.button>
        @if($quotation->is_confirm == 0)
            <x-ui.button type="button" variant="primary" icon="check" class="confirm-button">
                Confirm
            </x-ui.button>
        @else
            <x-ui.button type="button" variant="danger" icon="x" class="confirm_cancel-button">
                Cancel confirmation
            </x-ui.button>
        @endif
    </x-slot>
</x-ui.page-header>

<input type="hidden" value="{{ $quotation->id }}" id="quotation_id" />

{{-- Help / legend trigger — preserved. Renders a help icon button that
     the legend.quotation_legend_edit partial reacts to. --}}
<div class="mb-3 flex justify-end">
    <span id="help" class="inline-flex h-8 w-8 items-center justify-center rounded text-slate-500 hover:bg-slate-100 hover:text-slate-700 cursor-pointer" title="Legend">
        <x-ui.icon name="help-circle" size="sm" />
        @include('legend.quotation_legend_edit')
    </span>
</div>

{{-- Tab card --}}
<div class="rounded border border-slate-200 bg-white">
    @php
        $tabBase   = 'group inline-flex items-center gap-2 whitespace-nowrap border-b-2 px-1 pb-3 pt-3 text-sm transition-colors border-transparent text-slate-600 hover:text-slate-900 hover:border-slate-300';
        $tabActive = '[&.active]:border-primary-600 [&.active]:text-primary-700 [&.active]:font-medium';
        $tabClass  = $tabBase . ' ' . $tabActive;
    @endphp
    <div class="border-b border-slate-200 px-1" role="tablist">
        {{-- Keep BOTH data-toggle (BS3) AND data-bs-toggle (BS5) so whichever
             one quotation.js + Tabler's tab JS expects, it works. --}}
        <ul class="nav nav-tabs nav-tabs-underline -mb-px flex flex-nowrap gap-6 overflow-x-auto border-0 px-3 list-none pl-0 m-0 [&_.nav-link]:cursor-pointer" data-bs-toggle="tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a href="#main" class="nav-link active {{ $tabClass }}" data-toggle="tab" data-bs-toggle="tab" aria-selected="true" role="tab">
                    <x-ui.icon name="layout-grid" />{{ trans('main.Main') }}
                </a>
            </li>
            @if(Auth::user()->can('quotation.calculation'))
                <li class="nav-item" role="presentation">
                    <a href="#calculation" class="nav-link {{ $tabClass }}" data-toggle="tab" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                        <x-ui.icon name="calculator" />{{ trans('main.Calculation') }}
                    </a>
                </li>
            @endif
        </ul>
    </div>

    <div class="p-5">
        <div class="tab-content">

            {{-- ============================================================ --}}
            {{-- MAIN TAB                                                       --}}
            {{-- ============================================================ --}}
            <div class="tab-pane active" id="main">

                {{-- Name + show-titles toggle --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                    <div>
                        <label for="quotation_name" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">
                            Quotation Name <span class="text-danger-600">*</span>
                        </label>
                        <input type="text" id="quotation_name" placeholder="Name" value="{{ $quotation->name }}"
                               class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                    </div>
                    <div>
                        <div class="hide validate-name inline-flex items-center gap-1.5 rounded bg-danger-50 border border-danger-600/20 px-3 py-2 text-sm text-danger-700">
                            <x-ui.icon name="alert-circle" size="sm" />
                            {{ trans('main.Nameisrequiredfield') }}
                        </div>
                    </div>
                    <div class="md:text-right">
                        <a href="#" class="namesToggle hideTitle inline-flex h-8 items-center gap-1.5 rounded border border-slate-300 bg-white px-3 text-xs font-medium text-slate-700 hover:bg-slate-50">
                            <x-ui.icon name="eye" size="xs" />
                            {{ trans('main.Showtitles') }}
                        </a>
                    </div>
                </div>

                {{-- Pricing grid + add-column button --}}
                <div class="mt-5">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-xs font-medium uppercase tracking-wide text-slate-500">Pricing grid</h3>
                        <button type="button"
                                data-link="{{ route('quotation.add_column_message') }}"
                                class="quotation-add-column inline-flex h-8 items-center gap-1.5 rounded border border-slate-300 bg-white px-3 text-xs font-medium text-slate-700 hover:bg-slate-50">
                            <x-ui.icon name="plus" size="xs" />
                            {{ trans('main.Addcolumn') }}
                        </button>
                    </div>

                    @php
                        $columns = array("date","cityName","hotelName","SIN","htlpp","lunchName","lunch","dinnerName","dinner","entrance","comments","local_g_d","bus","group_cost","driver","porterage");
                        $insertIndex = array_search("hotelName", $columns);
                        $sortedQuotationRows = $quotation->rows->sortBy(function ($quotationRow) {
                            return $quotationRow->getValueByKey('date')->value ?? '';
                        });
                    @endphp

                    <div id="quotation_table" class="rounded border border-slate-200 overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-sm">
                            <thead class="bg-slate-50">
                                <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                                    <th class="px-3 py-2 whitespace-nowrap">{{ trans('main.Date') }}</th>
                                    <th class="px-3 py-2 whitespace-nowrap">{{ trans('main.City') }}</th>
                                    <th class="px-3 py-2 whitespace-nowrap">{{ trans('main.Hotel') }}</th>
                                    <th class="px-3 py-2 whitespace-nowrap" data-container="body" data-toggle="tooltip" data-bs-toggle="tooltip" data-placement="bottom" data-original-title="Single Suppl." data-bs-title="Single Suppl.">SS</th>
                                    <th class="px-3 py-2 whitespace-nowrap" data-column="htlpp" data-container="body" data-toggle="tooltip" data-bs-toggle="tooltip" data-placement="bottom" data-original-title="Hotel P.P" data-bs-title="Hotel P.P">HPP</th>
                                    <th class="px-3 py-2 whitespace-nowrap" data-column="lunchName" data-container="body" data-toggle="tooltip" data-bs-toggle="tooltip" data-placement="bottom" data-original-title="Lunch Name" data-bs-title="Lunch Name">L.Name</th>
                                    <th class="px-3 py-2 whitespace-nowrap" data-container="body" data-toggle="tooltip" data-bs-toggle="tooltip" data-placement="bottom" data-original-title="Lunch" data-bs-title="Lunch">Lun</th>
                                    <th class="px-3 py-2 whitespace-nowrap" data-column="dinnerName" data-container="body" data-toggle="tooltip" data-bs-toggle="tooltip" data-placement="bottom" data-original-title="Dinner Name" data-bs-title="Dinner Name">D.Name</th>
                                    <th class="px-3 py-2 whitespace-nowrap" data-container="body" data-toggle="tooltip" data-bs-toggle="tooltip" data-placement="bottom" data-original-title="Dinner" data-bs-title="Dinner">Din</th>
                                    <th class="px-3 py-2 whitespace-nowrap">Entr</th>
                                    <th class="px-3 py-2 whitespace-nowrap" data-container="body" data-toggle="tooltip" data-bs-toggle="tooltip" data-placement="bottom" data-original-title="Comments" data-bs-title="Comments">Com</th>
                                    <th class="px-3 py-2 whitespace-nowrap" data-container="body" data-toggle="tooltip" data-bs-toggle="tooltip" data-placement="bottom" data-original-title="Local G\D" data-bs-title="Local G\D">LGD</th>
                                    <th class="px-3 py-2 whitespace-nowrap">BUS</th>
                                    <th class="px-3 py-2 whitespace-nowrap" data-container="body" data-toggle="tooltip" data-bs-toggle="tooltip" data-placement="bottom" data-original-title="Group Cost" data-bs-title="Group Cost">GC</th>
                                    <th class="px-3 py-2 whitespace-nowrap" data-container="body" data-toggle="tooltip" data-bs-toggle="tooltip" data-placement="bottom" data-original-title="Driver" data-bs-title="Driver">Dri</th>
                                    <th class="px-3 py-2 whitespace-nowrap" data-container="body" data-toggle="tooltip" data-bs-toggle="tooltip" data-placement="bottom" data-original-title="Porterage" data-bs-title="Porterage">Por</th>
                                    @foreach($quotation->additional_columns as $column)
                                        <th data-column="{{ $column->name }}" data-type="{{ $column->type }}"
                                            class="additional-column px-3 py-2 whitespace-nowrap relative group">
                                            <span class="pr-5">{{ $column->name }}</span>
                                            <i class="fa fa-times remove-quotation-column absolute top-1/2 right-2 -translate-y-1/2 cursor-pointer text-slate-400 hover:text-danger-600"></i>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody id="quotation_table" class="divide-y divide-slate-100">
                                @foreach($sortedQuotationRows as $key => $row)
                                    <tr class="quotation-row hover:bg-slate-50" data-row="{{ $row->id }}">
                                        @foreach($columns as $column)
                                            @php $found = false; @endphp
                                            @foreach($row->values as $value)
                                                @if($value->key === $column)
                                                    @php $found = true; @endphp
                                                    @if(!empty($value->value) && in_array($value->key, $columns))
                                                        <td class="px-3 py-2" data-column="{{ $value->key }}">{{ $value->value }}</td>
                                                    @elseif(in_array($value->key, $columns))
                                                        <td class="px-3 py-2" data-column="{{ $value->key }}">
                                                            <input type="text" value="{{ $value->value }}" />
                                                        </td>
                                                    @endif
                                                    @break
                                                @endif
                                            @endforeach
                                            @if(!$found)
                                                <td class="px-3 py-2" data-column="{{ $column }}"></td>
                                            @endif
                                        @endforeach

                                        @foreach($quotation->additional_columns as $column)
                                            <td data-column="{{ $column->name }}"
                                                class="additional-cell px-3 py-2 @if($column->type == 'all') quotation-cell-general @endif @if($column->type == 'person') quotation-cell-per-person @endif">
                                                {{ @$quotation->getAdditionalColumnValueCell($row->id, $column->name)->value }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Rate + Markup --}}
                <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="rate" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.Rate') }}</label>
                        <input type="text" id="rate" name="rate" value="{{ $quotation->rate != '' ? $quotation->rate : '1.00' }}"
                               class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                    </div>
                    <div>
                        <label for="mark_up" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.MarkUp') }}</label>
                        <input type="text" id="mark_up" name="mark_up" value="{{ $quotation->mark_up != '' ? $quotation->mark_up : '0' }}"
                               class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                    </div>
                </div>

                {{-- Note + Note-show-in-PDF --}}
                <div class="mt-5 space-y-3">
                    <div>
                        <label for="note" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.Note') }}</label>
                        <textarea name="note" id="note" rows="2"
                                  class="form-control block w-full rounded border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">{{ $quotation->note }}</textarea>
                    </div>
                    <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                        {{ Form::checkbox('note_show', 1, $quotation->note_show, ['id' => 'note_show', 'class' => 'h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-600/30']) }}
                        <span>{{ trans('main.NoteShowinPDF') }}</span>
                    </label>
                </div>

                {{-- Summary tables — JS populates rows by data-row. --}}
                <div class="mt-5 space-y-4">
                    <h3 class="text-xs font-medium uppercase tracking-wide text-slate-500">Summary</h3>
                    <div class="rounded border border-slate-200 overflow-x-auto">
                        <table id="summary" class="min-w-full divide-y divide-slate-200 finder-disable text-sm">
                            <tbody class="divide-y divide-slate-100">
                                <tr data-row="person"></tr>
                                <tr data-row="netto_euro"></tr>
                                <tr data-row="mark_up"></tr>
                                <tr data-row="brutto"></tr>
                                <tr data-row="active"></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="rounded border border-slate-200 overflow-x-auto">
                        <table id="summary_all" class="min-w-full divide-y divide-slate-200 finder-disable text-sm">
                            <tbody class="divide-y divide-slate-100">
                                <tr data-row="person"></tr>
                                <tr data-row="netto_euro"></tr>
                                <tr data-row="mark_up"></tr>
                                <tr data-row="brutto"></tr>
                                <tr data-row="active"></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Additional configures (jquery.repeater) --}}
                <div class="mt-5">
                    <h3 class="text-xs font-medium uppercase tracking-wide text-slate-500 mb-3">{{ trans('main.AdditionalConfigures') }}</h3>
                    <div class="repeater rounded border border-slate-200 bg-white p-4">
                        <div data-repeater-list="package_menu" class="space-y-3">
                            {{-- TEMPLATE ROW (hidden by data-repeater-list — gets cloned on add) --}}
                            <div class="grid grid-cols-12 gap-3 items-end" data-repeater-item>
                                <div class="col-span-12 sm:col-span-4 package-menu-item">
                                    {!! Form::label('person', 'Person', ['class' => 'block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1']) !!}
                                    {!! Form::input('string', 'person', 0, ['class' => 'form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600']) !!}
                                </div>
                                <div class="col-span-12 sm:col-span-4 package-menu-item">
                                    {!! Form::label('price', 'Price', ['class' => 'block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1']) !!}
                                    {!! Form::input('string', 'price', 0, ['class' => 'form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600']) !!}
                                </div>
                                <div class="col-span-6 sm:col-span-2">
                                    <span class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1 text-center">Active</span>
                                    <div class="flex justify-center h-9 items-center">
                                        {!! Form::checkbox('active', 1, @$additional->active, ['class' => 'active-person h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-600/30']) !!}
                                    </div>
                                </div>
                                <div class="col-span-6 sm:col-span-2 flex justify-end">
                                    <a href="#" data-repeater-delete name="remove"
                                       class="inline-flex h-9 w-9 items-center justify-center rounded border border-slate-300 bg-white text-danger-600 hover:bg-danger-50 hover:border-danger-300" title="Remove">
                                        <i class="fa fa-trash-o"></i>
                                    </a>
                                </div>
                            </div>

                            @foreach($quotation->additional_persons as $additional)
                                <div class="grid grid-cols-12 gap-3 items-end" data-repeater-item>
                                    <div class="col-span-12 sm:col-span-4 package-menu-item">
                                        {!! Form::label('person', 'Person', ['class' => 'block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1']) !!}
                                        {!! Form::input('string', 'person', @$additional->person, ['class' => 'form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600']) !!}
                                    </div>
                                    <div class="col-span-12 sm:col-span-4 package-menu-item">
                                        {!! Form::label('price', 'Price', ['class' => 'block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1']) !!}
                                        {!! Form::input('string', 'price', @$additional->price, ['class' => 'form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600']) !!}
                                    </div>
                                    <div class="col-span-6 sm:col-span-2">
                                        <span class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1 text-center">Active</span>
                                        <div class="flex justify-center h-9 items-center">
                                            {!! Form::checkbox('active', 1, @$additional->active, ['class' => 'active-person h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-600/30']) !!}
                                        </div>
                                    </div>
                                    <div class="col-span-6 sm:col-span-2 flex justify-end">
                                        <a href="#" data-repeater-delete name="remove"
                                           class="inline-flex h-9 w-9 items-center justify-center rounded border border-slate-300 bg-white text-danger-600 hover:bg-danger-50 hover:border-danger-300" title="Remove">
                                            <i class="fa fa-trash-o"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <button data-repeater-create type="button"
                                class="mt-4 inline-flex h-9 items-center gap-2 rounded bg-primary-600 px-3 text-sm font-medium text-white hover:bg-primary-700">
                            <i class="fa fa-plus"></i>
                            {{ trans('main.Add') }}
                        </button>
                    </div>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- CALCULATION TAB                                                --}}
            {{-- ============================================================ --}}
            <div class="tab-pane" id="calculation">
                <script>
                    let quotationId = {{ $quotation->id }};
                    $(document).on('blur', 'input', function () {
                        let data_row    = $(this).attr('data_row');
                        let data_column = $(this).attr('data_column');
                    });
                </script>
                {{ csrf_field() }}

                <div class="space-y-4">
                    <div class="rounded border border-slate-200 overflow-x-auto">
                        <table id="calculation" class="min-w-full divide-y divide-slate-200 text-sm">
                            <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                                <tr>
                                    <td class="px-3 py-2"></td>
                                    <td class="px-3 py-2">{{ trans('main.Hotel') }}</td>
                                    <td class="px-3 py-2">{{ trans('main.Lunch') }}</td>
                                    <td class="px-3 py-2">{{ trans('main.Dinner') }}</td>
                                    <td class="px-3 py-2">{{ trans('main.Entrance') }}</td>
                                    <td class="px-3 py-2">{{ trans('main.LocalGD') }}</td>
                                    <td class="px-3 py-2">{{ trans('main.DriverRoom') }}</td>
                                    <td class="px-3 py-2">{{ trans('main.SingleSupple') }}.</td>
                                    <td class="px-3 py-2">{{ trans('SingleSuppleforDFs') }}.</td>
                                    <td class="px-3 py-2">{{ trans('main.Bus') }}</td>
                                    <td class="px-3 py-2">{{ trans('main.Bus') }}</td>
                                    <td class="px-3 py-2">{{ trans('main.Porterage') }}</td>
                                    <td class="px-3 py-2">{{ trans('main.TotalMeals') }}</td>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr data-row="quotation_sum">
                                    <td class="px-3 py-2"></td>
                                    <td class="px-3 py-2" data-column="htlpp"></td>
                                    <td class="px-3 py-2" data-column="lunch"></td>
                                    <td class="px-3 py-2" data-column="dinner"></td>
                                    <td class="px-3 py-2" data-column="entrance"></td>
                                    <td class="px-3 py-2" data-column="local_g_d"></td>
                                    <td class="px-3 py-2" data-column="driver_room"></td>
                                    <td class="px-3 py-2" data-column="sgl_suppl"></td>
                                    <td class="px-3 py-2" data-column="dfs_suppl"></td>
                                    <td class="px-3 py-2" data-column="bus"></td>
                                    <td class="px-3 py-2" data-column="group_cost"></td>
                                    <td class="px-3 py-2" data-column="porterage"></td>
                                    <td class="px-3 py-2" data-column="total_meals"></td>
                                </tr>
                                <tr data-row="combined_sum">
                                    <td class="px-3 py-2" data-column="entrance_porterage" data-container="body" data-toggle="tooltip" data-bs-toggle="tooltip" data-placement="bottom" data-original-title="entrance + porterage" data-bs-title="entrance + porterage"></td>
                                    <td class="px-3 py-2" data-column="htlpp" data-container="body" data-toggle="tooltip" data-bs-toggle="tooltip" data-placement="bottom" data-original-title="entrance + porterage + hotel" data-bs-title="entrance + porterage + hotel"></td>
                                    <td class="px-3 py-2" data-column="lunch" data-container="body" data-toggle="tooltip" data-bs-toggle="tooltip" data-placement="bottom" data-original-title="lunch + dinner" data-bs-title="lunch + dinner"></td>
                                    <td class="px-3 py-2" data-column="dinner" data-container="body" data-toggle="tooltip" data-bs-toggle="tooltip" data-placement="bottom" data-original-title="hotel + lunch" data-bs-title="hotel + lunch"></td>
                                    <td class="px-3 py-2" data-column="entrance" data-container="body" data-toggle="tooltip" data-bs-toggle="tooltip" data-placement="bottom" data-original-title="lunch + dinner + entrance" data-bs-title="lunch + dinner + entrance"></td>
                                    <td class="px-3 py-2" data-column="local_g_d" data-container="body" data-toggle="tooltip" data-bs-toggle="tooltip" data-placement="bottom" data-original-title="Local G/D + Driver + Bus" data-bs-title="Local G/D + Driver + Bus"></td>
                                    <td class="px-3 py-2" data-column="driver_room"></td>
                                    <td class="px-3 py-2" data-column="sgl_suppl"></td>
                                    <td class="px-3 py-2" data-column="dfs_suppl"></td>
                                    <td class="px-3 py-2" data-column="bus"></td>
                                    <td class="px-3 py-2" data-column="group_cost"></td>
                                    <td class="px-3 py-2" data-column="porterage"></td>
                                    <td class="px-3 py-2" data-column="total_meals"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    @foreach(['netto_first', 'netto_second', 'netto_third', 'netto_fourth'] as $nettoId)
                        <div class="rounded border border-slate-200 overflow-x-auto">
                            <table id="{{ $nettoId }}" class="min-w-full divide-y divide-slate-200 text-sm">
                                <tbody class="divide-y divide-slate-100">
                                    <tr data-row="free"></tr>
                                    <tr data-row="person"></tr>
                                    <tr data-row="meals"></tr>
                                    <tr data-row="package"></tr>
                                    <tr data-row="foc"></tr>
                                    <tr data-row="bus_g_d"></tr>
                                    <tr data-row="netto"></tr>
                                    <tr data-row="netto_euro"></tr>
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Card footer --}}
    <div class="border-t border-slate-200 bg-slate-50 px-5 py-3 flex items-center justify-end gap-2 rounded-b">
        <x-ui.button as="a" href="javascript:history.back()" variant="secondary">{{ trans('main.Cancel') }}</x-ui.button>
        <x-ui.button type="button" variant="primary" icon="save" class="update-quotation">{{ trans('main.Save') }}</x-ui.button>
    </div>
</div>

@push('styles')
<style>
    /* Pricing-grid + summary input styling — matches the create page so the
       two views feel like the same product. */
    #quotation_table td input[type="text"],
    #calculation td input[type="text"] {
        display: block;
        width: 100%;
        min-width: 70px;
        padding: 0.25rem 0.5rem;
        margin-top: 0.25rem;
        font-size: 0.8125rem;
        color: #334155;
        background-color: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 0.25rem;
        outline: none;
        transition: border-color 0.15s, box-shadow 0.15s;
    }
    #quotation_table td input[type="text"]:focus,
    #calculation td input[type="text"]:focus {
        border-color: #0d9488;
        box-shadow: 0 0 0 2px rgba(13, 148, 136, 0.18);
    }
    #quotation_table td, #calculation td { vertical-align: top; }
</style>
@endpush
@stop

@section('post_scripts')
    <script>
        // jquery.repeater binding — preserved verbatim from the legacy template.
        var $repeater = $('.repeater').repeater({
            selector: '.package-menu-item',
            show: function () {
                $(this).slideDown();
                $(this).find('.select2').remove();
                $(this).find('select').select2();
            },
            hide: function (deleteElement) {
                if (confirm('Are you sure you want to delete this element?')) {
                    $(this).slideUp(deleteElement);
                }
            },
        });

        var calculationArray = {!! $quotation->getCalculationJson() !!};
    </script>
    <script type="text/javascript" src='{{ asset('js/utils.js') }}'></script>
    <script type="text/javascript" src='{{ asset('js/quotation.js') }}'></script>
@stop
