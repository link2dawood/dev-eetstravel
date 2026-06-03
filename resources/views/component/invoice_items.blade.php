{{-- Pre-existing invoice items for an edit view. Same grid shape as
     component/invoice_item_form.blade.php (the new-row template) so
     the layout stays consistent between existing and freshly-added
     rows. JS hooks: id="item_name|item_desc|amount|vat|total_amount|
     delete_contact_item", class .item-contact + .item_total + .delete,
     onchange="calculateItemTotal(this)". --}}

@foreach($invoice_items as $invoice_item)
<div class="item-contact grid grid-cols-1 md:grid-cols-12 gap-3 items-end py-3 border-t border-slate-100">
    <div class="md:col-span-3">
        <label for="item_name" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">
            {{ trans('Item Name') }}
        </label>
        <input id="item_name"
               name="items[{{$count}}][item_name]"
               type="text"
               required
               value="{{ $invoice_item->item_name }}"
               class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-900 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
    </div>

    <div class="md:col-span-2">
        <label for="item_desc" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">
            {{ trans('Quantity') }}
        </label>
        <input id="item_desc"
               name="items[{{$count}}][quantity]"
               type="number"
               onchange="calculateItemTotal(this)"
               value="{{ $invoice_item->quantity }}"
               required
               class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-900 text-right shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
    </div>

    <div class="md:col-span-2">
        <label for="amount" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">
            {{ trans('Price (excl. VAT)') }}
        </label>
        <input id="amount"
               name="items[{{$count}}][amount]"
               type="number"
               onchange="calculateItemTotal(this)"
               value="{{ $invoice_item->amount }}"
               required
               class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-900 text-right font-mono shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
    </div>

    <div class="md:col-span-2">
        <label for="vat" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">
            VAT rate
        </label>
        <select name="items[{{$count}}][vat]"
                id="vat"
                onchange="calculateItemTotal(this)"
                required
                class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-900 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
            <option value="" disabled selected>Choose option</option>
            @foreach($taxes as $tax)
                @php $tax_value = $tax->value / 100; @endphp
                <option value="{{ $tax_value }}" {{ $tax_value == $invoice_item->vat ? 'selected' : '' }}>{{ $tax->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="md:col-span-2">
        <label for="total_amount" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">
            {{ trans('Total Amount') }}
        </label>
        <input id="total_amount"
               name="items[{{$count}}][total_amount]"
               type="number"
               value="{{ $invoice_item->total_amount }}"
               readonly
               class="form-control item_total block w-full h-9 rounded border border-slate-300 bg-slate-50 px-3 text-sm text-slate-700 text-right font-mono shadow-subtle cursor-not-allowed">
    </div>

    <div class="md:col-span-1">
        <button id="delete_contact_item"
                type="button"
                class="delete btn btn-danger btn-sm inline-flex h-9 w-9 items-center justify-center rounded bg-danger-600 text-white hover:bg-danger-700"
                title="Remove row">
            <x-ui.icon name="trash-2" size="sm" />
        </button>
    </div>
</div>
@endforeach
