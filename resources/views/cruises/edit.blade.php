@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Edit Cruise')

@section('content')
<x-ui.page-header
    :title="'Edit ' . $cruise->name"
    description="Cruise supplier record"
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Cruises', 'href' => route('cruises.index')],
        ['label' => $cruise->name, 'href' => route('cruises.show', $cruise->id)],
        ['label' => 'Edit'],
    ]"
>
    <x-slot name="actions">
        <x-ui.button as="a" href="{{ route('cruises.show', $cruise->id) }}" variant="ghost" icon="arrow-left">
            {{ trans('main.Back') }}
        </x-ui.button>
    </x-slot>
</x-ui.page-header>

<span id="page" data-page="edit" hidden></span>

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

<form action="{{ route('cruises.update', ['cruise' => $cruise->id]) }}" method="post" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    @csrf
    @method('PUT')

    {{-- ============================================================ --}}
    {{-- LEFT COLUMN (2/3) — form sections                             --}}
    {{-- ============================================================ --}}
    <div class="lg:col-span-2 space-y-4">

        {{-- Section 1: Identity --}}
        <div class="rounded border border-slate-200 bg-white">
            <div class="border-b border-slate-200 px-5 py-3 flex items-start gap-3">
                <div class="flex h-8 w-8 items-center justify-center rounded bg-primary-50 text-primary-600 shrink-0"><x-ui.icon name="ship" size="sm" /></div>
                <div class="flex-1 min-w-0">
                    <h2 class="text-sm font-medium text-slate-700">Identity</h2>
                </div>
            </div>
            <div class="px-5 py-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label for="name" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.Name') !!}</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $cruise->name) }}"
                           class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                </div>
                <div>
                    <label for="code" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.Code') !!}</label>
                    <input id="code" name="code" type="text" value="{{ old('code', $cruise->code) }}"
                           class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                </div>
                <div>
                    <label for="website" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.Website') !!}</label>
                    <input id="website" name="website" type="text" value="{{ old('website', $cruise->website) }}"
                           class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                </div>
                <div class="md:col-span-2">
                    <label for="address_first" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.AddressFirst') !!}</label>
                    <input id="address_first" name="address_first" type="text" value="{{ old('address_first', $cruise->address_first) }}"
                           class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                </div>
                <div class="md:col-span-2">
                    <label for="address_second" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.AddressSecond') !!}</label>
                    <input id="address_second" name="address_second" type="text" value="{{ old('address_second', $cruise->address_second) }}"
                           class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                </div>
            </div>
        </div>

        {{-- Section 2: Route --}}
        <div class="rounded border border-slate-200 bg-white">
            <div class="border-b border-slate-200 px-5 py-3 flex items-start gap-3">
                <div class="flex h-8 w-8 items-center justify-center rounded bg-primary-50 text-primary-600 shrink-0"><x-ui.icon name="map-pin" size="sm" /></div>
                <div class="flex-1 min-w-0">
                    <h2 class="text-sm font-medium text-slate-700">Route</h2>
                </div>
            </div>
            <div class="px-5 py-5 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @component('component.city_form', [
                        'country_label'       => 'country_from',
                        'country_translation' => 'main.CountryFrom',
                        'country_default'     => $cruise->country_from,
                        'city_label'          => 'city_from',
                        'city_translation'    => 'main.Cityfrom',
                        'city_default'        => \App\Helper\CitiesHelper::getCityById($cruise->city_to)['name'],
                    ])@endcomponent
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @component('component.city_form', [
                        'country_label'       => 'country_to',
                        'country_translation' => 'main.CountryTo',
                        'country_default'     => $cruise->country_to,
                        'city_label'          => 'city_to',
                        'city_translation'    => 'main.CityTo',
                        'city_default'        => \App\Helper\CitiesHelper::getCityById($cruise->city_to)['name'],
                    ])@endcomponent
                </div>
            </div>
        </div>

        {{-- Section 3: Schedule --}}
        <div class="rounded border border-slate-200 bg-white">
            <div class="border-b border-slate-200 px-5 py-3 flex items-start gap-3">
                <div class="flex h-8 w-8 items-center justify-center rounded bg-primary-50 text-primary-600 shrink-0"><x-ui.icon name="calendar" size="sm" /></div>
                <div class="flex-1 min-w-0">
                    <h2 class="text-sm font-medium text-slate-700">Schedule</h2>
                </div>
            </div>
            <div class="px-5 py-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="from_date" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.DateFrom') !!}</label>
                    {!! Form::text('from_date', $cruise->from_date, ['class' => 'block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600 datepicker', 'id' => 'from_date']) !!}
                </div>
                <div>
                    <label for="from_time" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.TimeFrom') !!}</label>
                    {!! Form::text('from_time', $cruise->from_time, ['class' => 'block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600 timepicker', 'id' => 'from_time']) !!}
                </div>
                <div>
                    <label for="to_date" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.DateTo') !!}</label>
                    {!! Form::text('to_date', $cruise->to_date, ['class' => 'block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600 datepicker', 'id' => 'to_date']) !!}
                </div>
                <div>
                    <label for="to_time" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.TimeTo') !!}</label>
                    {!! Form::text('to_time', $cruise->to_time, ['class' => 'block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600 timepicker', 'id' => 'to_time']) !!}
                </div>
                <div class="md:col-span-2">
                    <input type="text" hidden name="date_from">
                    @if($errors->has('date_from'))
                        <p class="text-xs text-danger-600">{{ $errors->first('date_from') }}</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Section 4: Contact --}}
        <div class="rounded border border-slate-200 bg-white">
            <div class="border-b border-slate-200 px-5 py-3 flex items-start gap-3">
                <div class="flex h-8 w-8 items-center justify-center rounded bg-primary-50 text-primary-600 shrink-0"><x-ui.icon name="phone" size="sm" /></div>
                <div class="flex-1 min-w-0">
                    <h2 class="text-sm font-medium text-slate-700">Contact</h2>
                </div>
            </div>
            <div class="px-5 py-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="work_phone" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.WorkPhone') !!}</label>
                    <input id="work_phone" name="work_phone" type="text" value="{{ old('work_phone', $cruise->work_phone) }}"
                           class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                </div>
                <div>
                    <label for="work_fax" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.WorkFax') !!}</label>
                    <input id="work_fax" name="work_fax" type="text" value="{{ old('work_fax', $cruise->work_fax) }}"
                           class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                </div>
                <div class="md:col-span-2">
                    <label for="work_email" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.WorkEmail') !!}</label>
                    <input id="work_email" name="work_email" type="text" value="{{ old('work_email', $cruise->work_email) }}"
                           class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                </div>
                <div>
                    <label for="contact_name" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.ContactName') !!}</label>
                    <input id="contact_name" name="contact_name" type="text" value="{{ old('contact_name', $cruise->contact_name) }}"
                           class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                </div>
                <div>
                    <label for="contact_phone" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.ContactPhone') !!}</label>
                    <input id="contact_phone" name="contact_phone" type="text" value="{{ old('contact_phone', $cruise->contact_phone) }}"
                           class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                </div>
            </div>
        </div>

        {{-- Section 5: Criteria & rate --}}
        <div class="rounded border border-slate-200 bg-white">
            <div class="border-b border-slate-200 px-5 py-3 flex items-start gap-3">
                <div class="flex h-8 w-8 items-center justify-center rounded bg-primary-50 text-primary-600 shrink-0"><x-ui.icon name="star" size="sm" /></div>
                <div class="flex-1 min-w-0">
                    <h2 class="text-sm font-medium text-slate-700">Criteria &amp; classification</h2>
                </div>
            </div>
            <div class="px-5 py-5 space-y-4">
                <div>
                    <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-2">{!! trans('main.Criteria') !!}</label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
                        @foreach($criterias as $criteria)
                            @php
                                $checked = false;
                                foreach ($cruise->criterias as $item) {
                                    if ($criteria->id == $item->criteria_id) { $checked = true; break; }
                                }
                            @endphp
                            <label class="flex items-center gap-2 rounded border border-slate-300 bg-white px-3 py-2 cursor-pointer hover:bg-slate-50 has-[:checked]:border-primary-600 has-[:checked]:bg-primary-50">
                                <input type="checkbox" name="criterias" value="{{ $criteria->id }}" {{ $checked ? 'checked' : '' }}
                                       class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-600/30" />
                                <span class="text-sm text-slate-700">{{ $criteria->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div>
                    <label for="rate" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.Rate') !!}</label>
                    <select id="rate" name="rate"
                            class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
                        @foreach($rates as $rate)
                            <option value="{{ $rate->id }}" {{ ($errors != null && count($errors) > 0 ? old('rate') == $rate->id : $cruise->rate == $rate->id) ? 'selected' : '' }}>{{ $rate->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Section 6: Notes --}}
        <div class="rounded border border-slate-200 bg-white">
            <div class="border-b border-slate-200 px-5 py-3 flex items-start gap-3">
                <div class="flex h-8 w-8 items-center justify-center rounded bg-primary-50 text-primary-600 shrink-0"><x-ui.icon name="notebook" size="sm" /></div>
                <div class="flex-1 min-w-0">
                    <h2 class="text-sm font-medium text-slate-700">Notes</h2>
                </div>
            </div>
            <div class="px-5 py-5 space-y-4">
                <div>
                    <label for="comments" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.Comments') !!}</label>
                    <input id="comments" name="comments" type="text" value="{{ old('comments', $cruise->comments) }}"
                           class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                </div>
                <div>
                    <label for="int_comments" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.IntComments') !!}</label>
                    <input id="int_comments" name="int_comments" type="text" value="{{ old('int_comments', $cruise->int_comments) }}"
                           class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                    <p class="mt-1 text-xs text-slate-500">Internal — not visible to clients.</p>
                </div>
            </div>
        </div>

        {{-- Section 7: Files (current + add more) --}}
        <div class="rounded border border-slate-200 bg-white">
            <div class="border-b border-slate-200 px-5 py-3 flex items-start gap-3">
                <div class="flex h-8 w-8 items-center justify-center rounded bg-primary-50 text-primary-600 shrink-0"><x-ui.icon name="paperclip" size="sm" /></div>
                <div class="flex-1 min-w-0">
                    <h2 class="text-sm font-medium text-slate-700">{!! trans('main.Files') !!}</h2>
                </div>
            </div>
            <div class="px-5 py-5 space-y-5">
                @php
                    $existingImages = collect($files['image'] ?? [])
                        ->filter(fn($i) => !empty($i->attach_file_name))
                        ->values();
                    $existingAttach = collect($files['attach'] ?? [])
                        ->filter(fn($a) => !empty($a->attach_file_name))
                        ->values();
                    $hasExisting = $existingImages->count() + $existingAttach->count() > 0;
                @endphp

                @if($hasExisting)
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-xs font-medium uppercase tracking-wide text-slate-500">Current files</h3>
                            <span class="text-xs text-slate-400">{{ $existingImages->count() + $existingAttach->count() }} total</span>
                        </div>

                        @if($existingImages->count())
                            <div class="image grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 mb-4">
                                @foreach($existingImages as $image)
                                    @php $imgUrl = asset('storage/' . $image->attach_file_name); @endphp
                                    <div class="del-container relative group rounded overflow-hidden border border-slate-200 bg-slate-50 aspect-square">
                                        <a href="{{ $imgUrl }}" class="block w-full h-full">
                                            <img src="{{ $imgUrl }}" alt="" loading="lazy" class="w-full h-full object-cover" />
                                        </a>
                                        <button type="button"
                                                class="del-attach absolute top-1.5 right-1.5 inline-flex h-7 w-7 items-center justify-center rounded-full bg-white/90 text-danger-600 shadow-subtle opacity-0 group-hover:opacity-100 transition-opacity"
                                                data-attach-url="{{ route('file_delete', ['id' => $image->id]) }}" aria-label="Delete photo">
                                            <x-ui.icon name="trash-2" size="xs" />
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if($existingAttach->count())
                            <ul class="divide-y divide-slate-100 list-none pl-0 m-0 rounded border border-slate-200">
                                @foreach($existingAttach as $attach)
                                    @php $fileUrl = asset('storage/' . $attach->attach_file_name); $displayName = basename($attach->attach_file_name); @endphp
                                    <li class="del-container px-3 py-2 flex items-center gap-3 hover:bg-slate-50">
                                        <span class="flex h-8 w-8 items-center justify-center rounded bg-slate-100 text-slate-500 shrink-0">
                                            <x-ui.icon name="paperclip" size="sm" />
                                        </span>
                                        <div class="min-w-0 flex-1">
                                            <a href="{{ $fileUrl }}" target="_blank" class="block text-sm font-medium text-slate-700 hover:text-primary-700 truncate">{{ $displayName }}</a>
                                            <p class="text-xs text-slate-500 mt-0.5">{{ $attach->created_at }}@if(!empty($attach->attach_file_size)) · {{ round($attach->attach_file_size / 1024, 1) }} KB @endif</p>
                                        </div>
                                        <button type="button" class="del-attach inline-flex h-7 w-7 items-center justify-center rounded text-slate-400 hover:bg-danger-50 hover:text-danger-700 shrink-0"
                                                data-attach-url="{{ route('file_delete', ['id' => $attach->id]) }}" aria-label="Delete file">
                                            <x-ui.icon name="trash-2" size="sm" />
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    <div class="relative">
                        <div class="absolute inset-0 flex items-center" aria-hidden="true"><div class="w-full border-t border-slate-200"></div></div>
                        <div class="relative flex justify-center"><span class="bg-white px-2 text-xs text-slate-500">Add more</span></div>
                    </div>
                @endif

                @component('component.file_upload_field', ['enableAjaxUploads' => false])@endcomponent
            </div>
        </div>

        {{-- Form footer --}}
        <div class="sticky bottom-0 -mx-4 sm:mx-0 sm:static sm:rounded sm:border sm:border-slate-200 bg-white sm:bg-slate-50 px-4 sm:px-5 py-3 border-t border-slate-200 sm:border-t-0 sm:border flex items-center justify-end gap-2 shadow-[0_-4px_8px_-4px_rgba(15,23,42,0.05)] sm:shadow-none">
            <x-ui.button as="a" href="{{ route('cruises.show', $cruise->id) }}" variant="secondary">{{ trans('main.Cancel') }}</x-ui.button>
            <x-ui.button type="submit" variant="primary" icon="save">{{ trans('main.Save') }}</x-ui.button>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- RIGHT COLUMN — Map sidebar (sticky on desktop)                --}}
    {{-- ============================================================ --}}
    <div>
        <div class="lg:sticky lg:top-4 rounded border border-slate-200 bg-white">
            <div class="border-b border-slate-200 px-5 py-3 flex items-center gap-2">
                <x-ui.icon name="map" size="sm" class="text-slate-400" />
                <h2 class="text-sm font-medium text-slate-700">Location</h2>
            </div>
            <div class="px-5 py-5 space-y-3">
                <div class="flex flex-col gap-2">
                    <x-ui.button type="button" variant="secondary" id="btn_select_location" icon="mouse-pointer-click" block class="btn_google_maps">
                        {!! trans('main.SelectLocation') !!}
                    </x-ui.button>
                </div>
                <span id="error_map" class="text-sm text-danger-700"></span>
                <div class="block_map">
                    <div id="map" class="rounded border border-slate-200" style="height: 400px;"></div>
                </div>
                <input type="hidden" name="place_id" id="place_id">
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    // Inline file delete handler
    $(document).on('click', '.del-attach', function (e) {
        e.preventDefault();
        var btn = this;
        var url = $(btn).attr('data-attach-url');
        if (!url) return;
        if (!confirm('Are you sure you want to delete this file?')) return;
        $.ajax({
            url: url, method: 'POST', data: { "_token": "{{ csrf_token() }}" },
            success: function () { $(btn).closest('.del-container').hide(); },
            error:   function (res) { console.log(res); }
        });
    });
    if ($.fn.magnificPopup) {
        $('.image').magnificPopup({ delegate: 'a', type: 'image', gallery: { enabled: true } });
    }
</script>
@endpush
