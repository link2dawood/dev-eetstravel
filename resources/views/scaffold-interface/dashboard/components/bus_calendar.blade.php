<!--  Buses CALENDAR  -->
<div class="col-md-12">
    <div class="card">
        <div class="card-header d-flex flex-wrap align-items-center gap-2">
            <div>
                <h3 class="card-title mb-0">Bus schedule</h3>
                <div class="text-muted small">Click a bar to edit a day, or an empty cell to add a trip on that day.</div>
            </div>
            <div class="ms-auto d-flex gap-2 position-relative">
                @if(\App\Helper\PermissionHelper::checkPermission('tour_package.create'))
                <button type="button" class="btn btn-primary" onclick="openBusTripForm()"><i class="fa fa-plus me-1"></i>Add trip</button>
                @endif
                <button type="button" id="filter" class="btn btn-outline-secondary" title="Filter buses" aria-label="Filter buses"><i class="fa fa-filter me-1" aria-hidden="true"></i>Buses</button>
                <button type="button" id="help" class="btn btn-outline-secondary" title="Legend" aria-label="Legend"><i class="fa fa-question-circle me-1" aria-hidden="true"></i>Legend</button>

                <div id="leggend" class="bus-panel" style="display:none;"></div>
                <div id="filter_block" class="bus-panel" style="display:none;"></div>
            </div>
        </div>
        <div class="card-body">
            <div id="busdiv" style="width: 100%; height: 700px;"></div>

            <div id="leggend_array" class="d-none">
                @foreach( $bus_statuses as $status)
                <span id="{{$status->color}}">{{$status->name}}</span>
                @endforeach
            </div>
        </div>
    </div>
    <span id="trip_edit_permission" data-info="{{ \App\Helper\PermissionHelper::checkPermission('tour_package.edit') }}"></span>
    <span id="trip_create_permission" data-info="{{ \App\Helper\PermissionHelper::checkPermission('tour_package.create') }}"></span>
</div>
<style>
    .bus-panel {
        position: absolute; top: calc(100% + 6px); right: 0; z-index: 1050;
        width: 320px; max-width: 90vw; max-height: 420px; overflow: auto;
        padding: 12px; background: #fff; border: 1px solid rgba(0,0,0,.1); border-radius: 6px;
        box-shadow: 0 8px 24px rgba(0,0,0,.15);
    }
    #leggend { width: 300px; }
    #filter_block label { cursor: pointer; font-size: .875rem; }
    .bus-modal .bus-modal-overlay {
        position: absolute; inset: 0; z-index: 10; display: flex; align-items: center; justify-content: center;
        background: rgba(255,255,255,.6); border-radius: inherit;
    }
    .bus-modal .bus-modal-overlay[style*="display: none"] { display: none !important; }
    .bus-modal .select2-container { width: 100% !important; }
    .bus-modal .select2-container .select2-selection--single { height: calc(2.5rem + 2px); display: flex; align-items: center; }
    .bus-modal .select2-container .select2-selection--single .select2-selection__arrow { height: 100%; }
    .bus-modal .select2-container .select2-selection--multiple { min-height: calc(2.5rem + 2px); }
    #modalUpdateTrip #show_comments:empty::before { content: "No comments yet."; color: #6c7a91; font-size: .875rem; }
    .bus-modal .form-label.required:after { content: " *"; color: #d63939; }
</style>
