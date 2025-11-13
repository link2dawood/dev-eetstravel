{{-- 
    Guest List Actions Component
    Action buttons for guest list operations
--}}
<div class="btn-list">
    <a href="javascript:history.back()" class="btn btn-ghost-secondary">
        <i class="ti ti-arrow-left me-1"></i>{!! trans('main.Back') !!}
    </a>
    <button type="button" class="btn btn-success roomlist_submit">
        <i class="ti ti-device-floppy me-1"></i>{!! trans('main.Save') !!}
    </button>
    <button type="button" 
            class="btn btn-primary roomlist_send hidden"
            id="roomlist_send">
        <i class="ti ti-send me-1"></i>{!! trans('main.SendtoHotels') !!}
    </button>
    <a href="javascript:history.back()" class="btn btn-warning">
        <i class="ti ti-x me-1"></i>{!! trans('main.Cancel') !!}
    </a>
</div>

