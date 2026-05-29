@extends('scaffold-interface.layouts.tabler-app')
@section('title','Show Client')

@section('content')
<x-ui.page-header
    :title="$client->name"
    description="Client record"
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Clients', 'href' => route('clients.index')],
        ['label' => $client->name],
    ]"
>
    <x-slot name="actions">
        <x-ui.button as="a" href="{{ route('clients.index') }}" variant="ghost" icon="arrow-left">
            {!! trans('main.Back') !!}
        </x-ui.button>
        @if(Auth::user()->can('clients.edit'))
            <x-ui.button as="a" href="{{ route('clients.edit', $client->id) }}" variant="secondary" icon="edit">
                {!! trans('main.Edit') !!}
            </x-ui.button>
        @endif
    </x-slot>
</x-ui.page-header>

@php
    $tabBase   = 'group inline-flex items-center gap-2 whitespace-nowrap border-b-2 px-1 pb-3 pt-3 text-sm transition-colors border-transparent text-slate-600 hover:text-slate-900 hover:border-slate-300';
    $tabActive = '[&.active]:border-primary-600 [&.active]:text-primary-700 [&.active]:font-medium';
    $tabClass  = $tabBase . ' ' . $tabActive;
@endphp

{{-- ============================================================ --}}
{{-- Tabs --}}
{{-- ============================================================ --}}
<div class="rounded border border-slate-200 bg-white">
    <div class="border-b border-slate-200 px-1" role="tablist">
        <ul class="nav nav-tabs -mb-px flex flex-nowrap gap-6 overflow-x-auto border-0 px-3 list-none pl-0 m-0 [&_.nav-link]:cursor-pointer" data-bs-toggle="tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a href="#info-tab" class="nav-link active {{ $tabClass }}" data-bs-toggle="tab" aria-selected="true" role="tab">
                    <x-ui.icon name="info" />{!! trans('main.Info') !!}
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="#contacts-tab" class="nav-link {{ $tabClass }}" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                    <x-ui.icon name="users" />{!! trans('main.Contacts') !!}
                    @if($contacts->count())
                        <span class="ml-1 inline-flex h-5 min-w-[1.25rem] items-center justify-center rounded-full bg-slate-100 px-1.5 text-xs font-medium text-slate-600 group-[.active]:bg-primary-50 group-[.active]:text-primary-700">{{ $contacts->count() }}</span>
                    @endif
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="#billing-tab" class="nav-link {{ $tabClass }}" id="billing_tab" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                    <x-ui.icon name="receipt" />{!! trans('Billing') !!}
                </a>
            </li>
        </ul>
    </div>

    <div class="p-5">
        <div class="tab-content">

            {{-- ============================================================ --}}
            {{-- Info tab --}}
            {{-- ============================================================ --}}
            <div class="tab-pane fade show active" role="tabpanel" id="info-tab">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                    {{-- Primary details (2/3 width on desktop) --}}
                    <div class="lg:col-span-2 rounded border border-slate-200 bg-white">
                        <div class="border-b border-slate-200 px-4 py-3 flex items-center gap-2">
                            <x-ui.icon name="user" size="sm" class="text-slate-400" />
                            <h2 class="text-sm font-medium text-slate-700">Details</h2>
                        </div>
                        <dl class="px-4 py-4 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('main.Name') !!}</dt>
                                <dd class="mt-0.5 text-slate-800">{{ $client->name ?: '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('main.Country') !!}</dt>
                                <dd class="mt-0.5 text-slate-800">{{ \App\Helper\CitiesHelper::getCountryById($client->country)['name'] ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('main.City') !!}</dt>
                                <dd class="mt-0.5 text-slate-800">{{ \App\Helper\CitiesHelper::getCityById($client->city)['name'] ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('main.Address') !!}</dt>
                                <dd class="mt-0.5 text-slate-800">{{ $client->address ?: '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('main.WorkPhone') !!}</dt>
                                <dd class="mt-0.5">
                                    @if($client->work_phone)
                                        <a href="tel:{{ $client->work_phone }}" class="text-primary-700 hover:underline">{{ $client->work_phone }}</a>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('main.ContactPhone') !!}</dt>
                                <dd class="mt-0.5">
                                    @if(!empty($client->contact_phone))
                                        <a href="tel:{{ $client->contact_phone }}" class="text-primary-700 hover:underline">{{ $client->contact_phone }}</a>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('main.WorkEmail') !!}</dt>
                                <dd class="mt-0.5">
                                    @if($client->work_email)
                                        <a href="mailto:{{ $client->work_email }}" class="text-primary-700 hover:underline break-all">{{ $client->work_email }}</a>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('main.ContactEmail') !!}</dt>
                                <dd class="mt-0.5">
                                    @if(!empty($client->contact_email))
                                        <a href="mailto:{{ $client->contact_email }}" class="text-primary-700 hover:underline break-all">{{ $client->contact_email }}</a>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('main.WorkFax') !!}</dt>
                                <dd class="mt-0.5 text-slate-800">{{ $client->work_fax ?: '—' }}</dd>
                            </div>
                            @if(!empty($client->account_no))
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('Account No') !!}</dt>
                                <dd class="mt-0.5 font-mono text-slate-800">{{ $client->account_no }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>

                    {{-- Side panel: Files (1/3 width on desktop) --}}
                    <div class="rounded border border-slate-200 bg-white">
                        <div class="border-b border-slate-200 px-4 py-3 flex items-center gap-2">
                            <x-ui.icon name="paperclip" size="sm" class="text-slate-400" />
                            <h2 class="text-sm font-medium text-slate-700">{!! trans('main.Files') !!}</h2>
                        </div>
                        <div class="px-4 py-4">
                            @component('component.files', ['files' => $files])@endcomponent
                        </div>
                    </div>
                </div>

                {{-- Comments — anchor IDs preserved for comment.js --}}
                <span id="showPreviewBlock" data-info="{{ true }}"></span>
                <div class="mt-6 rounded border border-slate-200 bg-white">
                    <div class="border-b border-slate-200 px-4 py-3 flex items-center gap-2">
                        <x-ui.icon name="message-circle" size="sm" class="text-slate-400" />
                        <h2 class="text-sm font-medium text-slate-700">{!! trans('main.Comments') !!}</h2>
                    </div>
                    <div class="px-4 py-4">
                        <div class="max-h-80 overflow-y-auto">
                            <div id="show_comments"></div>
                        </div>
                    </div>
                    <div class="border-t border-slate-200 bg-slate-50 px-4 py-4 rounded-b">
                        <form method='POST' action='{{ route('comment.store') }}' enctype="multipart/form-data" id="form_comment" class="space-y-3">
                            <div>
                                <span id="author_name" class="hidden mb-2 inline-flex items-center gap-2 rounded bg-primary-50 px-2 py-1 text-xs text-primary-700">
                                    Replying to <span id="name" class="font-medium"></span>
                                    <a href="#" id="reply_close" class="text-primary-700/70 hover:text-primary-900"><x-ui.icon name="x" size="xs" /></a>
                                </span>
                                <textarea
                                    class="form-control block w-full rounded border border-slate-300 bg-white px-3 py-2 text-sm shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600"
                                    id="content" name="content"
                                    placeholder="Add a comment — Ctrl + Enter to post"
                                    rows="3"></textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.Files') !!}</label>
                                @component('component.file_upload_field')@endcomponent
                            </div>
                            <input type="text" id="parent_comment" hidden name="parent" value="{{ null }}">
                            <input type="text" id="default_reference_id" hidden name="reference_id" value="{{ $client->id }}">
                            <input type="text" id="default_reference_type" hidden name="reference_type" value="{{ \App\Comment::$services['client'] }}">

                            <div class="flex">
                                <button type="submit" id="btn_send_comment" class="inline-flex h-9 items-center gap-2 rounded bg-primary-600 px-4 text-sm font-medium text-white hover:bg-primary-700">
                                    <x-ui.icon name="send" size="sm" />
                                    {!! trans('main.Send') !!}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- Contacts tab --}}
            {{-- ============================================================ --}}
            <div class="tab-pane fade" role='tabpanel' id='contacts-tab'>
                @if($contacts->count())
                    {{-- Desktop table --}}
                    <div class="hidden md:block overflow-x-auto rounded border border-slate-200">
                        <table class="min-w-full divide-y divide-slate-200 text-sm">
                            <thead class="bg-slate-50">
                                <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                                    <th class="px-4 py-3">{!! trans('main.FullName') !!}</th>
                                    <th class="px-4 py-3">{!! trans('main.MobilePhone') !!}</th>
                                    <th class="px-4 py-3">{!! trans('main.WorkPhone') !!}</th>
                                    <th class="px-4 py-3">{!! trans('main.Email') !!}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($contacts as $contact)
                                    <tr class="hover:bg-slate-50">
                                        <td class="px-4 py-3 font-medium text-slate-900">{{ $contact->full_name }}</td>
                                        <td class="px-4 py-3 text-slate-700">{{ $contact->mobile_phone ?: '—' }}</td>
                                        <td class="px-4 py-3 text-slate-700">{{ $contact->work_phone ?: '—' }}</td>
                                        <td class="px-4 py-3 text-slate-700">
                                            @if($contact->email)
                                                <a href="mailto:{{ $contact->email }}" class="text-primary-700 hover:underline break-all">{{ $contact->email }}</a>
                                            @else
                                                —
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile card list --}}
                    <div class="md:hidden space-y-3">
                        @foreach($contacts as $contact)
                            <div class="rounded border border-slate-200 bg-white p-4">
                                <p class="font-medium text-slate-900">{{ $contact->full_name }}</p>
                                <dl class="mt-3 space-y-2 text-xs">
                                    @if($contact->mobile_phone)
                                        <div><dt class="text-slate-500 uppercase tracking-wide">Mobile</dt><dd><a href="tel:{{ $contact->mobile_phone }}" class="text-primary-700">{{ $contact->mobile_phone }}</a></dd></div>
                                    @endif
                                    @if($contact->work_phone)
                                        <div><dt class="text-slate-500 uppercase tracking-wide">Work phone</dt><dd><a href="tel:{{ $contact->work_phone }}" class="text-primary-700">{{ $contact->work_phone }}</a></dd></div>
                                    @endif
                                    @if($contact->email)
                                        <div><dt class="text-slate-500 uppercase tracking-wide">Email</dt><dd><a href="mailto:{{ $contact->email }}" class="text-primary-700 break-all">{{ $contact->email }}</a></dd></div>
                                    @endif
                                </dl>
                            </div>
                        @endforeach
                    </div>
                @else
                    <x-ui.empty-state
                        icon="users"
                        title="No contacts yet"
                        message="Add contact persons from the Edit page." />
                @endif
            </div>

            {{-- ============================================================ --}}
            {{-- Billing tab --}}
            {{-- ============================================================ --}}
            <div role="tabpanel" class="tab-pane fade" id="billing-tab">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-4">
                    <div class="relative flex-1 max-w-md">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <x-ui.icon name="search" size="sm" />
                        </span>
                        <input type="text" id="searchInput" onkeyup="filterTable()"
                               placeholder="Search transactions..."
                               class="block w-full h-9 rounded border border-slate-300 bg-white pl-9 pr-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                    </div>
                    <div class="flex items-center gap-2">
                        @if(Auth::user()->can('accounting.create'))
                            <x-ui.button as="a" href="{{ route('accounting.create') }}" icon="plus" size="sm">New invoice</x-ui.button>
                        @endif
                        <x-ui.button variant="secondary" icon="download" size="sm" onclick="exportToCSV()">CSV</x-ui.button>
                        <x-ui.button variant="secondary" icon="file-spreadsheet" size="sm" onclick="exportToExcel()">Excel</x-ui.button>
                    </div>
                </div>

                <div class="overflow-x-auto rounded border border-slate-200">
                    <table id="transactions-table" class="min-w-full divide-y divide-slate-200 datatable text-sm">
                        <thead class="bg-slate-50">
                            <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                                <th class="px-4 py-3">ID</th>
                                <th class="px-4 py-3">Date</th>
                                <th class="px-4 py-3">Tour Name</th>
                                <th class="px-4 py-3">Client Name</th>
                                <th class="px-4 py-3">Amount Receivable</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @if(isset($transactions) && $transactions->count() > 0)
                                @foreach($transactions as $transaction)
                                    <tr class="hover:bg-slate-50">
                                        <td class="px-4 py-3 font-mono text-xs text-slate-500">#{{ $transaction->id }}</td>
                                        <td class="px-4 py-3 text-slate-700">{{ $transaction->date }}</td>
                                        <td class="px-4 py-3 text-slate-700">{{ $transaction->tour_name ?? 'N/A' }}</td>
                                        <td class="px-4 py-3 text-slate-700">{{ $transaction->client_name ?? 'N/A' }}</td>
                                        <td class="px-4 py-3 font-mono text-slate-700">{{ number_format($transaction->amount_receivable ?? 0, 2) }}</td>
                                        <td class="px-4 py-3">
                                            @php
                                                $st = $transaction->status ?? 'pending';
                                                $stClass = $st === 'paid'
                                                    ? 'bg-success-50 text-success-700'
                                                    : 'bg-warning-50 text-warning-700';
                                            @endphp
                                            <span class="inline-flex items-center rounded px-2 py-0.5 text-xs font-medium {{ $stClass }}">
                                                {{ ucfirst($st) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center justify-end gap-1">
                                                @include('component.action_buttons', [
                                                    'routePrefix' => 'accounting',
                                                    'item' => $transaction,
                                                    'showEdit' => true,
                                                    'showDelete' => true,
                                                    'showView' => true,
                                                ])
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-sm text-slate-500">No transactions found for this client</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                @if(isset($transactions) && method_exists($transactions, 'links'))
                    <div class="mt-3">
                        {{ $transactions->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<span id="services_name" data-service-name='Client' data-history-route="{{ route('services_history', ['id' => $client->id]) }}"></span>
@endsection

@section('post_scripts')
    <script src="{{ asset('js/comment.js') }}"></script>
    <script src="{{ asset('js/bootstrap-tables.js') }}"></script>
    <script>
        $(document).ready(function() {
            if (typeof initializeBootstrapTable === 'function') {
                initializeBootstrapTable('transactions-table');
            }
        });

        // Search the transactions table (Billing tab). Mirrors the original.
        function filterTable() {
            const input  = document.getElementById('searchInput');
            const filter = input.value.toUpperCase();
            const table  = document.getElementById('transactions-table');
            if (!table) return;
            const tr = table.getElementsByTagName('tr');

            for (let i = 1; i < tr.length; i++) {
                let display = false;
                const td = tr[i].getElementsByTagName('td');
                for (let j = 0; j < td.length - 1; j++) {
                    if (td[j]) {
                        const txtValue = td[j].textContent || td[j].innerText;
                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                            display = true;
                            break;
                        }
                    }
                }
                tr[i].style.display = display ? '' : 'none';
            }
        }

        function exportToCSV()   { exportTableToCSV('transactions-table',   'client-transactions.csv'); }
        function exportToExcel() { exportTableToExcel('transactions-table', 'client-transactions'); }
    </script>
@endsection
