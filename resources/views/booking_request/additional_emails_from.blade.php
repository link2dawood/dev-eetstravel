{{-- AJAX fragment — appended into #additionalEmails by invoice_items() in
     booking_request.blade.php. Keep .item-contact + #delete_contact_item
     selectors intact for the in-page remove handler. --}}
@foreach($additional_emails as $additional_email)
    <div class="item-contact flex items-end gap-2 mb-2">
        <div class="flex-1">
            <input type="email" name="additionalEmail[]" required value="{{ $additional_email->additional_email }}"
                   placeholder="Enter additional email"
                   class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
        </div>
        <button id="delete_contact_item" type="button"
                class="remove-email inline-flex items-center gap-1 rounded bg-danger-600 px-3 h-9 text-sm text-white hover:bg-danger-700">
            Remove
        </button>
    </div>
@endforeach
