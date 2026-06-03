@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Method not allowed')

@section('content')
<div class="max-w-3xl mx-auto py-12">
    <div class="rounded border border-slate-200 bg-white shadow-subtle overflow-hidden text-center">

        <div class="border-b border-slate-200 px-5 py-3 flex items-center justify-center gap-2">
            <x-ui.icon name="ban" class="text-danger-600" />
            <h3 class="text-sm font-semibold text-slate-900">405 — Method not allowed</h3>
        </div>

        <div class="px-8 py-12">
            <div class="mx-auto mb-6 inline-flex h-24 w-24 items-center justify-center rounded-full bg-danger-50 text-danger-600">
                <x-ui.icon name="ban" class="h-12 w-12" />
            </div>

            <h2 class="text-2xl font-semibold text-slate-900 tracking-tight">Method not allowed.</h2>
            <p class="mt-3 text-sm text-slate-600 max-w-xl mx-auto leading-relaxed">
                The request method is not supported for this resource.
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
