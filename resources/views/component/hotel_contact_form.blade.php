{{--
    Single empty contact row. Returned by /api/getItemContactView when the
    user clicks "Add contact" on hotel/client edit pages.

    DOM hooks PRESERVED (these are touched by the parent page's JS):
    - .item-contact         outer wrapper used by the delete handler to drop the row
    - #delete_contact_item  trigger that fires the delegated delete
    - input ids contact_full_name / contact_mobile_phone / contact_work_phone / contact_email
    - input names contacts[N][contact_full_name] etc.
--}}
<div class="item-contact relative rounded border border-slate-200 bg-white p-4 mb-3">
    <button type="button"
            id="delete_contact_item"
            class="absolute top-2 right-2 inline-flex h-7 w-7 items-center justify-center rounded text-slate-400 hover:bg-danger-50 hover:text-danger-700"
            aria-label="Remove contact">
        <i class="ti ti-x"></i>
    </button>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pr-8">
        <div class="md:col-span-2">
            <label for="contact_full_name" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.FullName') !!}</label>
            <input id="contact_full_name" name="contacts[{{ $count }}][contact_full_name]" type="text" value=""
                   class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
        </div>
        <div>
            <label for="contact_mobile_phone" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.MobilePhone') !!}</label>
            <input id="contact_mobile_phone" name="contacts[{{ $count }}][contact_mobile_phone]" type="tel" value=""
                   class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
        </div>
        <div>
            <label for="contact_work_phone" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.WorkPhone') !!}</label>
            <input id="contact_work_phone" name="contacts[{{ $count }}][contact_work_phone]" type="tel" value=""
                   class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
        </div>
        <div class="md:col-span-2">
            <label for="contact_email" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.Email') !!}</label>
            <input id="contact_email" name="contacts[{{ $count }}][contact_email]" type="email" value=""
                   class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
        </div>
    </div>
</div>
