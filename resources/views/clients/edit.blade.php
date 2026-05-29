@extends('scaffold-interface.layouts.tabler-app')
@section('title','Edit Client')

@section('content')
<x-ui.page-header
    :title="'Edit ' . $client->name"
    description="Update client details, contacts and billing addresses."
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Clients', 'href' => route('clients.index')],
        ['label' => $client->name, 'href' => route('clients.show', $client->id)],
        ['label' => 'Edit'],
    ]"
>
    <x-slot name="actions">
        <x-ui.button as="a" href="{{ route('clients.show', $client->id) }}" variant="ghost" icon="arrow-left">
            {!! trans('main.Back') !!}
        </x-ui.button>
    </x-slot>
</x-ui.page-header>

<span id="client_id_span" data-info="{{ $client->id }}"></span>

@if (count($errors) > 0)
    <div class="mb-4 rounded border border-danger-600/20 bg-danger-50 px-4 py-3 text-sm text-danger-700">
        <div class="flex items-center gap-2 font-medium">
            <x-ui.icon name="alert-octagon" class="text-danger-600" />
            Please correct the following:
        </div>
        <ul class="mt-2 list-disc pl-5 space-y-0.5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('clients.update', ['client' => $client->id]) }}" enctype="multipart/form-data" class="space-y-4">
    {{ csrf_field() }}
    {{ method_field('PUT') }}

    {{-- ============================================================ --}}
    {{-- Section 1: Identity --}}
    {{-- ============================================================ --}}
    <div class="rounded border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-3 flex items-start gap-3">
            <div class="flex h-8 w-8 items-center justify-center rounded bg-primary-50 text-primary-600 shrink-0">
                <x-ui.icon name="user" size="sm" />
            </div>
            <div class="flex-1 min-w-0">
                <h2 class="text-sm font-medium text-slate-700">Identity</h2>
                <p class="text-xs text-slate-500">Who is this client?</p>
            </div>
        </div>
        <div class="px-5 py-5 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="name" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">
                    {!! trans('main.Name') !!} <span class="text-danger-600">*</span>
                </label>
                <input id="name" name="name" type="text" value="{{ old('name', $client->name) }}" required
                       class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
            </div>

            <div>
                <label for="account_no" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('Account No') !!}</label>
                <input id="account_no" name="account_no" type="text" value="{{ old('account_no', $client->account_no) }}"
                       class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
            </div>

            <div class="md:col-span-2">
                <label for="address" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.Address') !!}</label>
                <input id="address" name="address" type="text" value="{{ old('address', $client->address) }}"
                       class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
            </div>

            <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                @component('component.city_form', [
                    'country_label' => 'country', 'country_translation' => 'main.Country', 'country_default' => $client->country,
                    'city_label'    => 'city',    'city_translation'    => 'main.City',    'city_default'    => \App\Helper\CitiesHelper::getCityById($client->city)['name'] ?? '',
                ])@endcomponent
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- Section 2: Contact --}}
    {{-- ============================================================ --}}
    <div class="rounded border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-3 flex items-start gap-3">
            <div class="flex h-8 w-8 items-center justify-center rounded bg-primary-50 text-primary-600 shrink-0">
                <x-ui.icon name="phone" size="sm" />
            </div>
            <div class="flex-1 min-w-0">
                <h2 class="text-sm font-medium text-slate-700">Contact</h2>
                <p class="text-xs text-slate-500">Phone, email, fax — the channels we'll reach them on.</p>
            </div>
        </div>
        <div class="px-5 py-5 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="work_phone" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.WorkPhone') !!}</label>
                <input id="work_phone" name="work_phone" type="tel" value="{{ old('work_phone', $client->work_phone) }}"
                       class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
            </div>
            <div>
                <label for="contact_phone" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.ContactPhone') !!}</label>
                <input id="contact_phone" name="contact_phone" type="tel" value="{{ old('contact_phone', $client->contact_phone) }}"
                       class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
            </div>
            <div>
                <label for="work_email" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.WorkEmail') !!}</label>
                <input id="work_email" name="work_email" type="email" value="{{ old('work_email', $client->work_email) }}"
                       class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
            </div>
            <div>
                <label for="contact_email" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.ContactEmail') !!}</label>
                <input id="contact_email" name="contact_email" type="email" value="{{ old('contact_email', $client->contact_email) }}"
                       class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
            </div>
            <div>
                <label for="work_fax" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.WorkFax') !!}</label>
                <input id="work_fax" name="work_fax" type="text" value="{{ old('work_fax', $client->work_fax) }}"
                       class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
            </div>
            <div>
                <label for="password" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('password') !!}</label>
                <input id="password" name="password" type="password"
                       class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                <p class="mt-1 text-xs text-slate-500">Leave blank to keep current. New value will replace the existing password.</p>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- Section 3: Billing --}}
    {{-- ============================================================ --}}
    <div class="rounded border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-3 flex items-start gap-3">
            <div class="flex h-8 w-8 items-center justify-center rounded bg-primary-50 text-primary-600 shrink-0">
                <x-ui.icon name="receipt" size="sm" />
            </div>
            <div class="flex-1 min-w-0">
                <h2 class="text-sm font-medium text-slate-700">Billing</h2>
                <p class="text-xs text-slate-500">Where invoices will be sent.</p>
            </div>
        </div>
        <div class="px-5 py-5 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label for="company_address" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('Company Address') !!}</label>
                <input id="company_address" name="company_address" type="text" value="{{ old('company_address', $client->company_address) }}"
                       class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
            </div>
            <div class="md:col-span-2">
                <label for="invoice_address" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('Invoice Address') !!}</label>
                <input id="invoice_address" name="invoice_address" type="text" value="{{ old('invoice_address', $client->invoice_address) }}"
                       class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                <p class="mt-1 text-xs text-slate-500">Leave blank to use the Company Address above.</p>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- Section 4: Contact persons (dynamic — ajax adds new rows) --}}
    {{-- ============================================================ --}}
    <div class="rounded border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-3 flex items-start gap-3">
            <div class="flex h-8 w-8 items-center justify-center rounded bg-primary-50 text-primary-600 shrink-0">
                <x-ui.icon name="users" size="sm" />
            </div>
            <div class="flex-1 min-w-0">
                <h2 class="text-sm font-medium text-slate-700">{!! trans('main.Contacts') !!}</h2>
                <p class="text-xs text-slate-500">Named people inside this client we can reach directly.</p>
            </div>
            <div class="shrink-0">
                <x-ui.button id="add_contact" type="button" size="sm" variant="secondary" icon="plus">
                    {!! trans('main.AddContact') !!}
                </x-ui.button>
            </div>
        </div>
        <div class="px-5 py-5">
            {{-- Populated by the JS in @push('scripts') below.
                 The legacy contact row partials returned from
                 /api/getClientContacts and /api/getItemContactView still
                 render Bootstrap form-control classes; they remain functional
                 (Bootstrap CSS is loaded for the page) until the partial itself
                 is migrated to Tailwind in a follow-up. --}}
            <div id="items-contacts" class="space-y-3"></div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- Section 5: Files --}}
    {{-- ============================================================ --}}
    <div class="rounded border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-3 flex items-start gap-3">
            <div class="flex h-8 w-8 items-center justify-center rounded bg-primary-50 text-primary-600 shrink-0">
                <x-ui.icon name="paperclip" size="sm" />
            </div>
            <div class="flex-1 min-w-0">
                <h2 class="text-sm font-medium text-slate-700">{!! trans('main.Files') !!}</h2>
                <p class="text-xs text-slate-500">Attach contracts, supplier sheets, anything related.</p>
            </div>
        </div>
        <div class="px-5 py-5">
            @component('component.file_upload_field')@endcomponent
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- Form footer --}}
    {{-- ============================================================ --}}
    <div class="sticky bottom-0 -mx-4 sm:mx-0 sm:static sm:rounded sm:border sm:border-slate-200 bg-white sm:bg-slate-50 px-4 sm:px-5 py-3 border-t border-slate-200 sm:border-t-0 sm:border flex items-center justify-end gap-2 shadow-[0_-4px_8px_-4px_rgba(15,23,42,0.05)] sm:shadow-none">
        <x-ui.button as="a" href="{{ \App\Helper\AdminHelper::getBackButton(route('clients.index')) }}" variant="secondary">
            {!! trans('main.Cancel') !!}
        </x-ui.button>
        <x-ui.button type="submit" variant="primary" icon="save">
            {!! trans('main.Save') !!}
        </x-ui.button>
    </div>
</form>
@endsection

@push('scripts')
<script type="text/javascript">
    // Dynamic contact-row management. Endpoints + DOM hooks preserved from the
    // legacy template — backend partials still drive the HTML for each row.
    var contactItemCount = 0;
    let clientId = $('#client_id_span').attr('data-info');

    $.ajax({
        url: '/api/getClientContacts',
        method: 'GET',
        data: { itemCount: contactItemCount, clientId: clientId }
    }).done((res) => {
        contactItemCount = res.count;
        $('#items-contacts').append(res.content);
    });

    $('#add_contact').on('click', function () {
        $.ajax({
            url: '/api/getItemContactView',
            method: 'GET',
            data: { itemCount: contactItemCount + 1 }
        }).done((res) => {
            contactItemCount++;
            $('#items-contacts').append(res);
        });
    });

    $(document).on('click', '#delete_contact_item', function () {
        $(this).closest('.item-contact').remove();
    });
</script>
@endpush
