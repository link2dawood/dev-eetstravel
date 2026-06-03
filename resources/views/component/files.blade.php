{{-- Attached files component. Renders three blocks:
       1. Image thumbnails (always)
       2. Landing-page image picker (when $tour is provided)
       3. Generic attachments table
     JS hooks at the bottom — magnificPopup gallery on .image, click
     handler on .del-attach for AJAX delete, click on .link_file to
     open a new tab. .del-container is the row/card the JS hides on
     successful delete. --}}

<div class="grid grid-cols-1 {{ isset($tour) ? 'md:grid-cols-2' : '' }} gap-4">

    {{-- Photos block --}}
    <div class="rounded border border-slate-200 bg-white shadow-subtle overflow-hidden table_photos">
        <div class="border-b border-slate-200 px-5 py-3 flex items-center gap-2">
            <x-ui.icon name="image" class="text-primary-600" size="sm" />
            <h3 class="text-sm font-semibold text-slate-900">{{ trans('main.Photos') }}</h3>
        </div>
        <div class="image px-5 py-4 flex flex-wrap gap-3">
            @forelse($files['image'] as $image)
                <div class="del-container relative rounded border border-slate-200 bg-white overflow-hidden" style="width: 220px;">
                    <div class="absolute top-2 right-2 z-10">
                        <button class="del-attach inline-flex h-7 w-7 items-center justify-center rounded bg-danger-600 text-white shadow-overlay hover:bg-danger-700"
                                data-attach-id="{{ $image->id }}"
                                data-attach-url="{{ route('file_delete', ['id' => $image->id]) }}"
                                title="Delete">
                            <x-ui.icon name="x" size="xs" />
                        </button>
                    </div>
                    <a href="{{ '/public' . $image->attach->url() }}" class="block">
                        <img src="{{ '/public' . $image->attach->url() }}"
                             class="block w-full"
                             style="height: 170px; object-fit: cover;"
                             alt="Photo">
                    </a>
                </div>
            @empty
                <p class="text-sm text-slate-500 italic">No photos uploaded.</p>
            @endforelse
        </div>
    </div>

    {{-- Landing-page image picker (only when $tour exists) --}}
    @if(isset($tour))
        <div class="rounded border border-slate-200 bg-white shadow-subtle overflow-hidden table_photos">
            <div class="border-b border-slate-200 px-5 py-3 flex items-center gap-2">
                <x-ui.icon name="image-plus" class="text-primary-600" size="sm" />
                <h3 class="text-sm font-semibold text-slate-900">{{ trans('main.imageforlanding') }}</h3>
            </div>
            <div class="image px-5 py-4">
                <div class="rounded border border-slate-200 bg-slate-50 text-center p-4">
                    <img class="pic mx-auto"
                         src="@if($tour->attachments()->first()){{ $tour->attachments()->first()->url }}@endif"
                         alt="Image for landing page"
                         style="max-height: 320px; max-width: 100%; object-fit: contain;">

                    <div class="upload-btn-wrapper inline-block mt-3">
                        <label for="check"
                               class="btn btn-primary inline-flex items-center gap-1.5 rounded bg-primary-600 px-3 h-9 text-sm font-medium text-white hover:bg-primary-700 cursor-pointer">
                            <x-ui.icon name="upload" size="sm" />
                            Change
                        </label>
                        <input id="check"
                               name="fileToUpload[]"
                               data-model="Tour"
                               data-id="{{ $tour->id }}"
                               class="fileToUpload hidden"
                               type="file">
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

{{-- Generic attachments table --}}
<div class="mt-4 rounded border border-slate-200 bg-white shadow-subtle overflow-hidden">
    <div class="border-b border-slate-200 px-5 py-3 flex items-center gap-2">
        <x-ui.icon name="paperclip" class="text-primary-600" size="sm" />
        <h3 class="text-sm font-semibold text-slate-900">{{ trans('main.Files') }}</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="table w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                    <th class="px-4 py-3">{{ trans('main.Name') }}</th>
                    <th class="px-4 py-3 text-right" style="width: 180px">{{ trans('main.Uploaded') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($files['attach'] as $attach)
                    <tr class="del-container hover:bg-slate-50">
                        <td class="px-4 py-3">
                            <div class="link_attach_file inline-flex items-center gap-2">
                                <a href="{{ url('public/' . $attach->attach->url()) }}" target="_blank" class="link_file inline-flex items-center gap-2 text-primary-700 hover:text-primary-900">
                                    <x-ui.icon name="paperclip" size="sm" />
                                    <span class="name_link_file">{{ $attach->attach_file_name }}</span>
                                </a>
                            </div>
                            <button class="del-attach inline-flex h-7 w-7 items-center justify-center rounded bg-danger-600 text-white hover:bg-danger-700 ml-2 align-middle"
                                    data-attach-url="{{ route('file_delete', ['id' => $attach->id]) }}"
                                    title="Delete">
                                <x-ui.icon name="x" size="xs" />
                            </button>
                        </td>
                        <td class="px-4 py-3 text-right text-xs text-slate-500 font-mono">
                            {{ $attach->created_at }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="px-4 py-10 text-center text-sm text-slate-500 italic">
                            No files uploaded.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
    $('.link_file').click(function (e) {
        window.open($(this).attr('href'));
    });

    $('.image').magnificPopup({
        delegate: 'a',
        type: 'image',
        gallery: { enabled: true }
    });

    $('.del-attach').on('click', function (e) {
        e.preventDefault();
        let elem = $(this).context;
        let deleteUrl = $(elem).attr('data-attach-url');

        // Ask for confirmation before proceeding
        let confirmDelete = confirm("Are you sure you want to delete this attachment?");

        if (confirmDelete) {
            $.ajax({
                url: deleteUrl,
                method: 'POST',
                data: { "_token": "{{ csrf_token() }}" },
                success: (res) => {
                    $(this).closest('.del-container').hide();
                },
                error: (res) => {
                    console.log(res);
                }
            });
        } else {
            console.log("Deletion cancelled.");
        }
    });
</script>
@endpush
