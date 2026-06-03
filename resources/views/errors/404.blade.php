@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Page not found')

@section('content')
<div class="max-w-3xl mx-auto py-12">
    <div class="rounded border border-slate-200 bg-white shadow-subtle overflow-hidden text-center">

        <div class="border-b border-slate-200 px-5 py-3 flex items-center justify-center gap-2">
            <x-ui.icon name="alert-triangle" class="text-danger-600" />
            <h3 class="text-sm font-semibold text-slate-900">404 — Page not found</h3>
        </div>

        <div class="px-8 py-12">
            <div class="mx-auto mb-6 inline-flex h-24 w-24 items-center justify-center rounded-full bg-danger-50 text-danger-600">
                <x-ui.icon name="search-x" class="h-12 w-12" />
            </div>

            <h2 class="text-2xl font-semibold text-slate-900 tracking-tight">Oops! Page not found.</h2>

            @if(isset($message))
                <p class="mt-3 text-sm text-slate-600 max-w-xl mx-auto leading-relaxed">{{ $message }}</p>
            @else
                <p class="mt-3 text-sm text-slate-600 max-w-xl mx-auto leading-relaxed">
                    The page you are looking for could not be found.
                </p>
            @endif

            <p class="mt-8 text-xs uppercase tracking-wide text-slate-400">Here are some helpful links instead</p>

            <div class="mt-4 flex flex-wrap items-center justify-center gap-2">
                <x-ui.button as="a" :href="url('/home')" variant="primary" icon="home">
                    Dashboard
                </x-ui.button>
                <x-ui.button as="a" href="javascript:history.back()" variant="secondary" icon="arrow-left">
                    Back
                </x-ui.button>
            </div>

            <hr class="my-8 border-slate-200">

            <div class="flex flex-wrap items-center justify-center gap-2">
                <x-ui.button as="a" :href="route('tour.index')" variant="ghost" icon="briefcase" size="sm">
                    Tours
                </x-ui.button>
                <x-ui.button as="a" :href="route('clients.index')" variant="ghost" icon="users" size="sm">
                    Clients
                </x-ui.button>
                <x-ui.button as="a" :href="route('announcements.index')" variant="ghost" icon="megaphone" size="sm">
                    Announcements
                </x-ui.button>
            </div>
        </div>
    </div>
</div>
@endsection
