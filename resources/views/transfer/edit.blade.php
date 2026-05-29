@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Edit Bus Company')

@section('content')
<x-ui.page-header
    :title="'Edit ' . $transfer->name"
    description="Bus company supplier record"
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Bus Companies', 'href' => route('transfer.index')],
        ['label' => $transfer->name, 'href' => route('transfer.show', $transfer->id)],
        ['label' => 'Edit'],
    ]"
>
    <x-slot name="actions">
        <x-ui.button as="a" href="{{ route('transfer.show', $transfer->id) }}" variant="ghost" icon="arrow-left">
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

{{-- Legacy edit uses url('transfer/{id}/update'), not route('transfer.update') — preserved exactly --}}
<form method="POST" action="{{ url('transfer/' . $transfer->id . '/update') }}" enctype="multipart/form-data"
      class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    @csrf

    {{-- ============================================================ --}}
    {{-- LEFT COLUMN (2/3) — form sections                             --}}
    {{-- ============================================================ --}}
    <div class="lg:col-span-2 space-y-4">

        {{-- Section 1: Identity --}}
        <div class="rounded border border-slate-200 bg-white">
            <div class="border-b border-slate-200 px-5 py-3 flex items-start gap-3">
                <div class="flex h-8 w-8 items-center justify-center rounded bg-primary-50 text-primary-600 shrink-0"><x-ui.icon name="bus" size="sm" /></div>
                <div class="flex-1 min-w-0">
                    <h2 class="text-sm font-medium text-slate-700">Identity</h2>
                </div>
            </div>
            <div class="px-5 py-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">
                        {!! trans('main.Name') !!} <span class="text-danger-600">*</span>
                    </label>
                    <input id="name" name="name" type="text" value="{{ old('name', $transfer->name) }}" required
                           class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                    @error('name')
                        <p class="mt-1 text-xs text-danger-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="code" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.Code') !!}</label>
                    <input id="code" name="code" type="text" value="{{ old('code', $transfer->code) }}"
                           class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                </div>
                <div>
                    <label for="address_first" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">
                        {!! trans('main.AddressFirst') !!} <span class="text-danger-600">*</span>
                    </label>
                    <input id="address_first" name="address_first" type="text" value="{{ old('address_first', $transfer->address_first) }}" required
                           class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                </div>
                <div>
                    <label for="address_second" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.AddressSecond') !!}</label>
                    <input id="address_second" name="address_second" type="text" value="{{ old('address_second', $transfer->address_second) }}"
                           class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                </div>
                <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if(!empty($transfer->city))
                        @component('component.city_form', [
                            'country_label' => 'country', 'country_translation' => 'main.Country', 'country_default' => $transfer->country,
                            'city_label'    => 'city',    'city_translation'    => 'main.City',    'city_default'    => \App\Helper\CitiesHelper::getCityById($transfer->city)['name'] ?? '',
                        ])@endcomponent
                    @else
                        @component('component.city_form', [
                            'country_label' => 'country', 'country_translation' => 'main.Country', 'country_default' => 0,
                            'city_label'    => 'city',    'city_translation'    => 'main.City',    'city_default'    => 0,
                        ])@endcomponent
                    @endif
                </div>
                <div class="md:col-span-2">
                    <label for="website" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.Website') !!}</label>
                    <input id="website" name="website" type="url" value="{{ old('website', $transfer->website) }}"
                           class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                </div>
            </div>
        </div>

        {{-- Section 2: Contact --}}
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
                    <input id="work_phone" name="work_phone" type="text" value="{{ old('work_phone', $transfer->work_phone) }}"
                           class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                </div>
                <div>
                    <label for="work_fax" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.WorkFax') !!}</label>
                    <input id="work_fax" name="work_fax" type="text" value="{{ old('work_fax', $transfer->work_fax) }}"
                           class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                </div>
                <div class="md:col-span-2">
                    <label for="work_email" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.WorkEmail') !!}</label>
                    <input id="work_email" name="work_email" type="email" value="{{ old('work_email', $transfer->work_email) }}"
                           class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                </div>
                <div>
                    <label for="contact_name" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.ContactName') !!}</label>
                    <input id="contact_name" name="contact_name" type="text" value="{{ old('contact_name', $transfer->contact_name) }}"
                           class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                </div>
                <div>
                    <label for="contact_phone" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.ContactPhone') !!}</label>
                    <input id="contact_phone" name="contact_phone" type="text" value="{{ old('contact_phone', $transfer->contact_phone) }}"
                           class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                </div>
                <div class="md:col-span-2">
                    <label for="contact_email" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.ContactEmail') !!}</label>
                    <input id="contact_email" name="contact_email" type="email" value="{{ old('contact_email', $transfer->contact_email) }}"
                           class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                </div>
            </div>
        </div>

        {{-- Section 3: Criteria & rate --}}
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
                            <label class="flex items-center gap-2 rounded border border-slate-300 bg-white px-3 py-2 cursor-pointer hover:bg-slate-50 has-[:checked]:border-primary-600 has-[:checked]:bg-primary-50">
                                <input type="checkbox" name="criterias" value="{{ $criteria->id }}"
                                       @foreach($transfer->criterias as $item)
                                           {{ $criteria->id == $item->criteria_id ? 'checked' : '' }}
                                       @endforeach
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
                            <option value="{{ $rate->id }}" {{ old('rate', $transfer->rate) == $rate->id ? 'selected' : '' }}>{{ $rate->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Section 4: Notes --}}
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
                    <input id="comments" name="comments" type="text" value="{{ old('comments', $transfer->comments) }}"
                           class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                </div>
                <div>
                    <label for="int_comments" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.IntComments') !!}</label>
                    <textarea id="int_comments" name="int_comments" rows="3"
                              class="block w-full rounded border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">{{ old('int_comments', $transfer->int_comments) }}</textarea>
                    <p class="mt-1 text-xs text-slate-500">Internal — not visible to clients.</p>
                </div>
            </div>
        </div>

        {{-- Section 5: Files (current + add more) --}}
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
            <x-ui.button as="a" href="{{ route('transfer.show', $transfer->id) }}" variant="secondary">{{ trans('main.Cancel') }}</x-ui.button>
            <x-ui.button type="submit" variant="primary" icon="save">{!! trans('main.Save') !!}</x-ui.button>
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
                    <x-ui.button type="button" id="btn_generate_map" icon="map-pin" block>
                        {!! trans('main.GenerateLocation') !!}
                    </x-ui.button>
                    <x-ui.button type="button" variant="secondary" id="btn_select_location" icon="mouse-pointer-click" block>
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

<span id="page" data-page="edit" hidden></span>
@endsection

@push('scripts')
<script src="{{ asset('js/google_map.js') }}"></script>
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
