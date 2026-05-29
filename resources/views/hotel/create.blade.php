@extends('scaffold-interface.layouts.tabler-app')
@section('title','Create Hotel')

@section('content')
<x-ui.page-header
    title="New hotel"
    description="Add a hotel supplier to the catalog."
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Hotels', 'href' => route('hotel.index')],
        ['label' => 'New'],
    ]"
>
    <x-slot name="actions">
        <x-ui.button as="a" href="{{ route('hotel.index') }}" variant="ghost" icon="arrow-left">
            {{ trans('main.Back') }}
        </x-ui.button>
    </x-slot>
</x-ui.page-header>

@if(count($errors) > 0)
    <div class="mb-4 rounded border border-danger-600/20 bg-danger-50 px-4 py-3 text-sm text-danger-700">
        <div class="flex items-center gap-2 font-medium">
            <x-ui.icon name="alert-octagon" class="text-danger-600" />
            Please correct the following:
        </div>
        <ul class="mt-2 list-disc pl-5 space-y-0.5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('hotel.store') }}" enctype="multipart/form-data" id="hotel-form" class="space-y-4">
    @csrf

    {{-- ============================================================ --}}
    {{-- Section 1: Identity --}}
    {{-- ============================================================ --}}
    <div class="rounded border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-3 flex items-start gap-3">
            <div class="flex h-8 w-8 items-center justify-center rounded bg-primary-50 text-primary-600 shrink-0"><x-ui.icon name="building" size="sm" /></div>
            <div class="flex-1 min-w-0">
                <h2 class="text-sm font-medium text-slate-700">Identity</h2>
                <p class="text-xs text-slate-500">Who is this hotel?</p>
            </div>
        </div>
        <div class="px-5 py-5 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label for="name" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">
                    {{ trans('main.Name') }} <span class="text-danger-600">*</span>
                </label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required
                       class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
            </div>
            <div class="md:col-span-2">
                <label for="address_first" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">
                    {{ trans('main.AddressFirst') }} <span class="text-danger-600">*</span>
                </label>
                <input id="address_first" name="address_first" type="text" value="{{ old('address_first') }}" required
                       class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
            </div>
            <div class="md:col-span-2">
                <label for="address_second" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.AddressSecond') }}</label>
                <input id="address_second" name="address_second" type="text" value="{{ old('address_second') }}"
                       class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
            </div>
            <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                @component('component.city_form', [
                    'country_label' => 'country', 'country_translation' => 'main.Country', 'country_default' => 0,
                    'city_label'    => 'city',    'city_translation'    => 'main.City',    'city_default'    => 0,
                ])@endcomponent
            </div>
            <div>
                <label for="code" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.Code') }}</label>
                <input id="code" name="code" type="text" value="{{ old('code') }}"
                       class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
            </div>
            <div>
                <label for="city_tax" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.CityTax') }}</label>
                <input id="city_tax" name="city_tax" type="number" step="0.01" value="{{ old('city_tax') }}"
                       class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
            </div>
            <div class="md:col-span-2">
                <label for="website" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.Website') }}</label>
                <input id="website" name="website" type="url" placeholder="https://example.com" value="{{ old('website') }}"
                       class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- Section 2: Contact --}}
    {{-- ============================================================ --}}
    <div class="rounded border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-3 flex items-start gap-3">
            <div class="flex h-8 w-8 items-center justify-center rounded bg-primary-50 text-primary-600 shrink-0"><x-ui.icon name="phone" size="sm" /></div>
            <div class="flex-1 min-w-0">
                <h2 class="text-sm font-medium text-slate-700">Contact</h2>
                <p class="text-xs text-slate-500">Reach the hotel directly.</p>
            </div>
        </div>
        <div class="px-5 py-5 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="work_phone" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.WorkPhone') }} <span class="text-danger-600">*</span></label>
                <input id="work_phone" name="work_phone" type="tel" value="{{ old('work_phone') }}" required
                       class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
            </div>
            <div>
                <label for="work_fax" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.WorkFax') }}</label>
                <input id="work_fax" name="work_fax" type="tel" value="{{ old('work_fax') }}"
                       class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
            </div>
            <div class="md:col-span-2">
                <label for="work_email" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.WorkEmail') }} <span class="text-danger-600">*</span></label>
                <input id="work_email" name="work_email" type="email" value="{{ old('work_email') }}" required
                       class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
            </div>
            <div>
                <label for="contact_name" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.ContactName') }}</label>
                <input id="contact_name" name="contact_name" type="text" value="{{ old('contact_name') }}"
                       class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
            </div>
            <div>
                <label for="contact_phone" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.ContactPhone') }}</label>
                <input id="contact_phone" name="contact_phone" type="tel" value="{{ old('contact_phone') }}"
                       class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
            </div>
            <div class="md:col-span-2">
                <label for="contact_email" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.ContactEmail') }}</label>
                <input id="contact_email" name="contact_email" type="email" value="{{ old('contact_email') }}"
                       class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- Section 3: Criteria & rate --}}
    {{-- ============================================================ --}}
    <div class="rounded border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-3 flex items-start gap-3">
            <div class="flex h-8 w-8 items-center justify-center rounded bg-primary-50 text-primary-600 shrink-0"><x-ui.icon name="star" size="sm" /></div>
            <div class="flex-1 min-w-0">
                <h2 class="text-sm font-medium text-slate-700">Criteria &amp; classification</h2>
                <p class="text-xs text-slate-500">Tags + rate band used when filtering supplier search.</p>
            </div>
        </div>
        <div class="px-5 py-5 space-y-4">
            <div>
                <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-2">{{ trans('main.Criteria') }}</label>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
                    @foreach($criterias as $criteria)
                        <label class="flex items-center gap-2 rounded border border-slate-300 bg-white px-3 py-2 cursor-pointer hover:bg-slate-50 has-[:checked]:border-primary-600 has-[:checked]:bg-primary-50">
                            <input type="checkbox" name="criterias" value="{{ $criteria->id }}"
                                   class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-600/30" />
                            <span class="text-sm text-slate-700">{{ $criteria->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
            <div>
                <label for="rate" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.Rate') }}</label>
                <select id="rate" name="rate"
                        class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
                    <option value="">— select —</option>
                    @foreach($rates as $rate)
                        <option value="{{ $rate->id }}" {{ old('rate') == $rate->id ? 'selected' : '' }}>{{ $rate->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- Section 4: Location & map --}}
    {{-- ============================================================ --}}
    <div class="rounded border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-3 flex items-start gap-3">
            <div class="flex h-8 w-8 items-center justify-center rounded bg-primary-50 text-primary-600 shrink-0"><x-ui.icon name="map-pin" size="sm" /></div>
            <div class="flex-1 min-w-0">
                <h2 class="text-sm font-medium text-slate-700">Location &amp; map</h2>
                <p class="text-xs text-slate-500">Drop a pin so itinerary maps render with the right coordinates.</p>
            </div>
        </div>
        <div class="px-5 py-5 space-y-3">
            <input type="hidden" name="place_id" id="place_id">
            <div class="flex flex-wrap gap-2">
                <x-ui.button type="button" id="btn_generate_map" icon="locate">
                    {{ trans('main.GenerateLocation') }}
                </x-ui.button>
                <x-ui.button type="button" variant="secondary" id="btn_select_location" icon="map" class="btn_google_maps">
                    {{ trans('main.SelectLocation') }}
                </x-ui.button>
            </div>
            <span id="page" data-page="create" style="display:none;"></span>
            <span id="error_map" class="text-sm text-danger-700"></span>
            <div class="block_map">
                <div id="map" class="rounded border border-slate-200" style="height: 400px;"></div>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- Section 5: Notes --}}
    {{-- ============================================================ --}}
    <div class="rounded border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-3 flex items-start gap-3">
            <div class="flex h-8 w-8 items-center justify-center rounded bg-primary-50 text-primary-600 shrink-0"><x-ui.icon name="notebook" size="sm" /></div>
            <div class="flex-1 min-w-0">
                <h2 class="text-sm font-medium text-slate-700">Notes</h2>
                <p class="text-xs text-slate-500">Public comments + internal notes (latter not visible to clients).</p>
            </div>
        </div>
        <div class="px-5 py-5 grid grid-cols-1 gap-4">
            <div>
                <label for="comments" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.Comments') }}</label>
                <textarea id="comments" name="comments" rows="3"
                          class="block w-full rounded border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">{{ old('comments') }}</textarea>
            </div>
            <div>
                <label for="note" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.Note') }}</label>
                <textarea id="note" name="note" rows="3"
                          class="block w-full rounded border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">{{ old('note') }}</textarea>
            </div>
            <div>
                <label for="int_comments" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.IntComments') }}</label>
                <textarea id="int_comments" name="int_comments" rows="3"
                          class="block w-full rounded border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">{{ old('int_comments') }}</textarea>
                <p class="mt-1 text-xs text-slate-500">Internal — not visible to clients.</p>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- Section 6: Files --}}
    {{-- ============================================================ --}}
    <div class="rounded border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-3 flex items-start gap-3">
            <div class="flex h-8 w-8 items-center justify-center rounded bg-primary-50 text-primary-600 shrink-0"><x-ui.icon name="paperclip" size="sm" /></div>
            <div class="flex-1 min-w-0">
                <h2 class="text-sm font-medium text-slate-700">{{ trans('main.Files') }}</h2>
                <p class="text-xs text-slate-500">Attach contracts, room sheets, anything related.</p>
            </div>
        </div>
        <div class="px-5 py-5">
            @component('component.file_upload_field')@endcomponent
        </div>
    </div>

    {{-- Form footer --}}
    <div class="sticky bottom-0 -mx-4 sm:mx-0 sm:static sm:rounded sm:border sm:border-slate-200 bg-white sm:bg-slate-50 px-4 sm:px-5 py-3 border-t border-slate-200 sm:border-t-0 sm:border flex items-center justify-end gap-2 shadow-[0_-4px_8px_-4px_rgba(15,23,42,0.05)] sm:shadow-none">
        <x-ui.button as="a" href="{{ route('hotel.index') }}" variant="secondary">{{ trans('main.Cancel') }}</x-ui.button>
        <x-ui.button type="submit" variant="primary" icon="save">{{ trans('main.Save') }}</x-ui.button>
    </div>
</form>
@endsection

@push('scripts')
<script type="text/javascript">
    $(document).on('keydown', '.price_room_type_in_hotel', function (e) {
        if (e.keyCode === 13) {
            e.preventDefault();
            $('.price_room_type_in_hotel').blur();
        }
    });
</script>
@endpush
