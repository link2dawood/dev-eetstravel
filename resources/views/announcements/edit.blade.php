@extends('scaffold-interface.layouts.tabler-app')
@section('title','Edit Announcement')

@section('content')
<x-ui.page-header
    :title="'Edit ' . $announcement->title"
    description="Announcement"
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Announcements', 'href' => route('announcements.index')],
        ['label' => $announcement->title, 'href' => route('announcements.show', $announcement->id)],
        ['label' => 'Edit'],
    ]"
>
    <x-slot name="actions">
        <x-ui.button as="a" href="{{ route('announcements.index') }}" variant="ghost" icon="arrow-left">{{ trans('main.Back') }}</x-ui.button>
    </x-slot>
</x-ui.page-header>

@if($errors->any())
    <div class="mb-4 rounded border border-danger-600/20 bg-danger-50 px-4 py-3 text-sm text-danger-700">
        <div class="flex items-center gap-2 font-medium"><x-ui.icon name="alert-octagon" class="text-danger-600" />Validation errors:</div>
        <ul class="mt-2 list-disc pl-5 space-y-0.5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif

<form method="POST" action="{{ route('announcements.update', ['announcement' => $announcement->id]) }}" enctype="multipart/form-data" id="announcementForm" class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    @csrf
    @method('PUT')
    <input type="hidden" name="deleted_files" id="deleted_files_input" value="">

    {{-- Main column --}}
    <div class="lg:col-span-2 space-y-4">
        <div class="rounded border border-slate-200 bg-white">
            <div class="border-b border-slate-200 px-5 py-3 flex items-start gap-3">
                <div class="flex h-8 w-8 items-center justify-center rounded bg-primary-50 text-primary-600 shrink-0"><x-ui.icon name="megaphone" size="sm" /></div>
                <div class="flex-1 min-w-0">
                    <h2 class="text-sm font-medium text-slate-700">Announcement</h2>
                </div>
            </div>
            <div class="px-5 py-5 space-y-4">
                <div>
                    <label for="title" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">
                        Title <span class="text-danger-600">*</span>
                    </label>
                    <input type="text" id="title" name="title" value="{{ old('title', $announcement->title) }}" placeholder="Enter announcement title" required
                           class="form-control block w-full h-9 rounded border {{ $errors->has('title') ? 'border-danger-600' : 'border-slate-300' }} bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                </div>
                <div>
                    <label for="content" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">
                        Content <span class="text-danger-600">*</span>
                    </label>
                    <textarea id="content" name="content" rows="10" placeholder="Enter announcement content" required
                              class="form-control block w-full rounded border {{ $errors->has('content') ? 'border-danger-600' : 'border-slate-300' }} bg-white px-3 py-2 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">{{ old('content', $announcement->content) }}</textarea>
                </div>
                <div>
                    <label for="files" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Attach new files</label>
                    <input type="file" id="files" name="files[]" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif"
                           class="block w-full text-sm text-slate-700 file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:bg-primary-600 file:text-white file:cursor-pointer hover:file:bg-primary-700" />
                    <p class="mt-1 text-xs text-slate-500">PDF, DOC, XLS, JPG, PNG, GIF.</p>
                </div>

                @if($files && count($files) > 0)
                    <div>
                        <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-2">Existing files</label>
                        <ul id="existing-files-list" class="divide-y divide-slate-100 list-none pl-0 m-0 rounded border border-slate-200">
                            @foreach($files as $file)
                                <li class="px-3 py-2 flex items-center gap-3 hover:bg-slate-50">
                                    <span class="flex h-8 w-8 items-center justify-center rounded bg-slate-100 text-slate-500 shrink-0"><x-ui.icon name="paperclip" size="sm" /></span>
                                    <div class="min-w-0 flex-1">
                                        <a href="{{ $file->url }}" target="_blank" class="text-sm font-medium text-slate-700 hover:text-primary-700 truncate block">{{ $file->name }}</a>
                                    </div>
                                    <button type="button" class="delete-file inline-flex h-7 w-7 items-center justify-center rounded text-slate-400 hover:bg-danger-50 hover:text-danger-700" data-file-id="{{ $file->id }}" title="Delete this file">
                                        <x-ui.icon name="trash-2" size="sm" />
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>

        <div class="sticky bottom-0 -mx-4 sm:mx-0 sm:static sm:rounded sm:border sm:border-slate-200 bg-white sm:bg-slate-50 px-4 sm:px-5 py-3 border-t border-slate-200 sm:border-t-0 sm:border flex items-center justify-end gap-2 shadow-[0_-4px_8px_-4px_rgba(15,23,42,0.05)] sm:shadow-none">
            <x-ui.button as="a" href="{{ route('announcements.index') }}" variant="secondary">{{ trans('main.Cancel') }}</x-ui.button>
            <x-ui.button type="submit" id="submitBtnFooter" variant="primary" icon="save">{{ trans('main.Save') }}</x-ui.button>
        </div>
    </div>

    {{-- Sidebar --}}
    <div>
        <div class="lg:sticky lg:top-4 rounded border border-slate-200 bg-white">
            <div class="border-b border-slate-200 px-4 py-3 flex items-center gap-2">
                <x-ui.icon name="info" size="sm" class="text-slate-400" />
                <h2 class="text-sm font-medium text-slate-700">Announcement info</h2>
            </div>
            <dl class="px-4 py-4 grid grid-cols-3 gap-x-3 gap-y-3 text-sm">
                <dt class="col-span-1 text-xs font-medium uppercase tracking-wide text-slate-500">ID</dt>
                <dd class="col-span-2 font-mono text-slate-800">#{{ $announcement->id }}</dd>

                <dt class="col-span-1 text-xs font-medium uppercase tracking-wide text-slate-500">Created</dt>
                <dd class="col-span-2 text-slate-800">{{ $announcement->created_at->format('Y-m-d H:i') }}</dd>

                <dt class="col-span-1 text-xs font-medium uppercase tracking-wide text-slate-500">Updated</dt>
                <dd class="col-span-2 text-slate-800">{{ $announcement->updated_at->format('Y-m-d H:i') }}</dd>

                <dt class="col-span-1 text-xs font-medium uppercase tracking-wide text-slate-500">Author</dt>
                <dd class="col-span-2 text-slate-800">{{ $announcement->author_name ?? 'Unknown' }}</dd>
            </dl>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let deletedFileIds = [];
    const deletedFilesInput = document.getElementById('deleted_files_input');

    document.querySelectorAll('.delete-file').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            if (confirm('Are you sure you want to delete this file? This will be permanent upon saving.')) {
                const fileId = this.getAttribute('data-file-id');
                deletedFileIds.push(fileId);
                deletedFilesInput.value = deletedFileIds.join(',');
                this.closest('li').remove();
            }
        });
    });

    const form = document.getElementById('announcementForm');
    const submitBtns = form ? form.querySelectorAll('button[type="submit"]') : [];

    if (form) {
        form.addEventListener('submit', function (e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
                form.classList.add('was-validated');
                return;
            }
            submitBtns.forEach(btn => {
                btn.disabled = true;
                btn.innerHTML = '<span class="inline-block h-4 w-4 mr-2 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>Saving...';
            });
        });
    }
});
</script>
@endpush
