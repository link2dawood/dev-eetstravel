@extends('scaffold-interface.layouts.tabler-app')

@section('content')
<x-ui.page-header
    title="Offices"
    description="Branch offices, bank details, and per-office accounting."
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Offices'],
    ]"
>
    <x-slot name="actions">
        @if(Auth::user()->can('office.create'))
            <x-ui.button as="a" href="{{ route('office.create') }}" icon="plus">New office</x-ui.button>
        @endif
    </x-slot>
</x-ui.page-header>

@if(session('message_buses'))
    <div class="mb-4 flex items-start gap-3 rounded border border-info-600/20 bg-info-50 px-4 py-3 text-sm text-info-700">
        <x-ui.icon name="info" class="mt-0.5 text-info-600" />
        <div class="flex-1">{{ session('message_buses') }}</div>
    </div>
@endif

@if(count($officesData) === 0)
    <div class="rounded border border-slate-200 bg-white">
        <x-ui.empty-state icon="building" title="No offices yet" message="Add your first office to start tracking expenses and earnings.">
            @if(Auth::user()->can('office.create'))
                <x-ui.button as="a" href="{{ route('office.create') }}" icon="plus">New office</x-ui.button>
            @endif
        </x-ui.empty-state>
    </div>
@else
    <div class="rounded border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table id="offices-table" class="min-w-full divide-y divide-slate-200 text-sm" style="background:#fff">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                        <th class="px-4 py-3">ID</th>
                        <th class="px-4 py-3">Office name</th>
                        <th class="px-4 py-3">Address</th>
                        <th class="px-4 py-3">Bank</th>
                        <th class="px-4 py-3">Account no</th>
                        <th class="px-4 py-3">Swift</th>
                        <th class="px-4 py-3">Tel</th>
                        <th class="px-4 py-3">Fax</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($officesData as $office)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-mono text-xs text-slate-500">#{{ $office->id }}</td>
                            <td class="px-4 py-3 font-medium text-slate-900" data-delete-label>{{ $office->office_name }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $office->office_address ?: '—' }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $office->bank_name ?: '—' }}</td>
                            <td class="px-4 py-3 font-mono text-slate-700">{{ $office->account_no ?: '—' }}</td>
                            <td class="px-4 py-3 font-mono text-slate-700">{{ $office->swift_code ?: '—' }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $office->tel ?: '—' }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $office->fax ?: '—' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    @include('component.action_buttons', ['item' => $office, 'routePrefix' => 'office'])
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
@endsection

@include('component.delete_modal_simple')
