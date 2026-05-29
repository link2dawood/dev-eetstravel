@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Settings')

@section('content')
<x-ui.page-header
    title="Settings"
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Settings', 'href' => route('settings.index')],
        ['label' => 'Show'],
    ]"
/>

<div class="rounded border border-slate-200 bg-white px-5 py-8 text-sm text-slate-500 text-center">
    Nothing to display here. Use the <a href="{{ route('settings.index') }}" class="text-primary-700 hover:underline">settings list</a> to manage configuration keys.
</div>
@endsection
