@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Edit Driver')

@section('content')
<x-ui.page-header
    :title="'Edit ' . $driver->name"
    description="Driver record"
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Drivers', 'href' => route('driver.index')],
        ['label' => $driver->name, 'href' => route('driver.show', $driver->id)],
        ['label' => 'Edit'],
    ]"
>
    <x-slot name="actions">
        <x-ui.button as="a" href="{{ route('driver.show', $driver->id) }}" variant="ghost" icon="arrow-left">
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

<form method="POST" action="{{ route('driver.update', ['driver' => $driver->id]) }}" enctype="multipart/form-data" class="space-y-4">
    @csrf
    @method('PUT')

    {{-- ============================================================ --}}
    {{-- Section 1: Identity --}}
    {{-- ============================================================ --}}
    <div class="rounded border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-3 flex items-start gap-3">
            <div class="flex h-8 w-8 items-center justify-center rounded bg-primary-50 text-primary-600 shrink-0"><x-ui.icon name="user" size="sm" /></div>
            <div class="flex-1 min-w-0">
                <h2 class="text-sm font-medium text-slate-700">Identity</h2>
                <p class="text-xs text-slate-500">Who is this driver?</p>
            </div>
        </div>
        <div class="px-5 py-5 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label for="name" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">
                    Name <span class="text-danger-600">*</span>
                </label>
                <input id="name" name="name" type="text" value="{{ old('name', $driver->name) }}" required
                       class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
            </div>
            <div>
                <label for="phone" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Phone</label>
                <input id="phone" name="phone" type="text" value="{{ old('phone', $driver->phone) }}"
                       class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
            </div>
            <div>
                <label for="email" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Email</label>
                <input id="email" name="email" type="text" value="{{ old('email', $driver->email) }}"
                       class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
            </div>
            <div class="md:col-span-2">
                <label for="transfer_id" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Bus Company</label>
                <select id="transfer_id" name="transfer_id"
                        class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
                    <option value="0">— select —</option>
                    @foreach($transfers as $tid => $tname)
                        <option value="{{ $tid }}" {{ old('transfer_id', $driver->transfer_id) == $tid ? 'selected' : '' }}>{{ $tname }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- Section 2: Files (current + add more) --}}
    {{-- ============================================================ --}}
    <div class="rounded border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-3 flex items-start gap-3">
            <div class="flex h-8 w-8 items-center justify-center rounded bg-primary-50 text-primary-600 shrink-0"><x-ui.icon name="paperclip" size="sm" /></div>
            <div class="flex-1 min-w-0">
                <h2 class="text-sm font-medium text-slate-700">{{ trans('main.Files') }}</h2>
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
        <x-ui.button as="a" href="{{ route('driver.show', $driver->id) }}" variant="secondary">Cancel</x-ui.button>
        <x-ui.button type="submit" variant="primary" icon="save">{{ trans('main.Save') }}</x-ui.button>
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
