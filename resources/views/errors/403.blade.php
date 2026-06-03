@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Access denied')

@section('content')
<div class="max-w-3xl mx-auto py-12">
    <div class="rounded border border-slate-200 bg-white shadow-subtle overflow-hidden text-center">

        <div class="border-b border-slate-200 px-5 py-3 flex items-center justify-center gap-2">
            <x-ui.icon name="lock" class="text-warning-600" />
            <h3 class="text-sm font-semibold text-slate-900">403 — Access denied</h3>
        </div>

        <div class="px-8 py-12">
            <div class="mx-auto mb-6 inline-flex h-24 w-24 items-center justify-center rounded-full bg-warning-50 text-warning-600">
                <x-ui.icon name="lock" class="h-12 w-12" />
            </div>

            <h2 class="text-2xl font-semibold text-slate-900 tracking-tight">Access denied.</h2>

            @if(isset($message))
                <p class="mt-3 text-sm text-slate-600 max-w-xl mx-auto leading-relaxed">{{ $message }}</p>
            @else
                <p class="mt-3 text-sm text-slate-600 max-w-xl mx-auto leading-relaxed">
                    You don't have permission to access this resource.
                </p>
            @endif

            <p class="mt-2 text-xs text-slate-500">
                If you believe this is an error, please contact your administrator.
            </p>

            <div class="mt-8 flex flex-wrap items-center justify-center gap-2">
                <x-ui.button as="a" :href="url('/home')" variant="primary" icon="home">
                    Dashboard
                </x-ui.button>
                <x-ui.button as="a" href="javascript:history.back()" variant="secondary" icon="arrow-left">
                    Back
                </x-ui.button>
            </div>
        </div>
    </div>
</div>
@endsection
