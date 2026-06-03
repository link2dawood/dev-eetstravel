{{-- Buses calendar widget — embedded in bus/calendar.blade.php.
     #busdiv is populated by the calendar JS, #leggend / #filter_block
     are JS-toggled popovers (kept by id), #filter / #help are the
     popover trigger buttons. --}}
<div class="col-12">
    <div class="rounded border border-slate-200 bg-white shadow-subtle overflow-hidden">

        <div class="border-b border-slate-200 px-5 py-3 flex items-center justify-between gap-3">
            <h3 class="text-sm font-semibold text-slate-900 flex items-center gap-2">
                <x-ui.icon name="bus" class="text-primary-600" />
                Buses calendar
            </h3>

            <div class="form-inline relative flex items-center gap-1">
                <span id="filter" class="inline-flex h-8 w-8 items-center justify-center rounded text-slate-500 hover:bg-slate-100 hover:text-slate-700 cursor-pointer" title="Filter">
                    <x-ui.icon name="filter" size="sm" />
                </span>
                <span id="help" class="inline-flex h-8 w-8 items-center justify-center rounded text-slate-500 hover:bg-slate-100 hover:text-slate-700 cursor-pointer" title="Help">
                    <x-ui.icon name="help-circle" size="sm" />
                </span>

                {{-- JS-toggled popovers (legend + filter). The legacy JS sets
                     `opacity: 1` to show; kept as IDs only so the JS hooks land. --}}
                <div id="leggend" class="absolute hidden rounded border border-slate-200 bg-white shadow-overlay"
                     style="width:275px; height:94px; z-index:9999; top:100%; right:0; opacity:0;"></div>
                <div id="filter_block" class="absolute hidden rounded border border-slate-200 bg-white shadow-overlay"
                     style="z-index:99999; top:100%; right:0; opacity:0;"></div>
            </div>
        </div>

        <div class="px-5 py-4">
            {{-- Calendar canvas — sized exactly as the legacy JS expects. --}}
            <div id="busdiv" style="width:100%; height:700px; position:relative;"></div>

            {{-- Hidden colour ↔ status name map used by the calendar JS. --}}
            <div id="leggend_array" class="hidden">
                @foreach($bus_statuses as $status)
                    <span id="{{ $status->color }}">{{ $status->name }}</span>
                @endforeach
            </div>
        </div>
    </div>

    <span id="trip_edit_permission"   data-info="{{ \App\Helper\PermissionHelper::checkPermission('tour_package.edit') }}"   class="hidden"></span>
    <span id="trip_create_permission" data-info="{{ \App\Helper\PermissionHelper::checkPermission('tour_package.create') }}" class="hidden"></span>
</div>
