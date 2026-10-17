@if($hotelContacts)
    @foreach($hotelContacts as $item)
        <div class="item-contact">
            <div class="delete_contact_block" style="position:static;display:flex;justify-content:flex-end;margin-bottom:.5rem;">
                <button type="button" class="remove-contact btn btn-outline-danger btn-sm" aria-label="Remove contact" style="min-width:44px;min-height:44px;">×</button>
            </div>
            <div class="form-group">
                <label for="contact_full_name_{{$item->count}}">{!!trans('main.FullName')!!}@if($contact_name_required ?? false) *@endif</label>
                <input id="contact_full_name_{{$item->count}}" name="contacts[{{$item->count}}][contact_full_name]" type="text" class="form-control"
                       value="{{ $item->full_name }}" @if($contact_name_required ?? false) required @endif>
            </div>
            <div class="form-group col-md-6 col-lg-6" style="padding-left: 0">
                <label for="contact_mobile_phone_{{$item->count}}">{!!trans('main.MobilePhone')!!}</label>
                <input id="contact_mobile_phone_{{$item->count}}" name="contacts[{{$item->count}}][contact_mobile_phone]" type="text" class="form-control"
                       value="{{ $item->mobile_phone }}">
            </div>
            <div class="form-group col-md-6 col-lg-6" style="padding-right: 0">
                <label for="contact_work_phone_{{$item->count}}">{!!trans('main.WorkPhone')!!}</label>
                <input id="contact_work_phone_{{$item->count}}" name="contacts[{{$item->count}}][contact_work_phone]" type="text" class="form-control"
                       value="{{ $item->work_phone }}">
            </div>
            <div class="form-group">
                <label for="contact_email_{{$item->count}}">{!!trans('main.Email')!!}</label>
                <input id="contact_email_{{$item->count}}" name="contacts[{{$item->count}}][contact_email]" type="text" class="form-control"
                       value="{{ $item->email }}">
            </div>
        </div>
    @endforeach
@endif
