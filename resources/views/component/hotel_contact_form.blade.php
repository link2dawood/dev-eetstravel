<div class="item-contact">
    <div class="delete_contact_block">
        <span id="delete_contact_item">x</span>
    </div>
    <div class="form-group">
        <label for="contact_full_name">{!!trans('main.FullName')!!}</label>
        <input id="contact_full_name" name="contacts[{{$count}}][contact_full_name]" type="text" class="form-control" value="">
    </div>
    <div class="form-group col-md-6 col-lg-6" style="padding-left: 0">
        <label for="contact_mobile_phone">{!!trans('main.MobilePhone')!!}</label>
        <input id="contact_mobile_phone" name="contacts[{{$count}}][contact_mobile_phone]" type="text" class="form-control" value="">
    </div>
    <div class="form-group col-md-6 col-lg-6" style="padding-right: 0">
        <label for="contact_work_phone">{!!trans('main.WorkPhone')!!}</label>
        <input id="contact_work_phone" name="contacts[{{$count}}][contact_work_phone]" type="text" class="form-control" value="">
    </div>
    <div class="form-group">
        <label for="contact_email">{!!trans('main.Email')!!}</label>
        <input id="contact_email" name="contacts[{{$count}}][contact_email]" type="text" class="form-control" value="">
    </div>
</div>