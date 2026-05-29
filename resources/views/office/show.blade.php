@extends('scaffold-interface.layouts.tabler-app')
@section('title','Show')

@section('post_styles')
    @include('component.datatables_cdn')
@endsection

@section('content')
<x-ui.page-header
    :title="$offices->office_name"
    description="Office accounting"
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Offices', 'href' => route('office.index')],
        ['label' => $offices->office_name],
    ]"
>
    <x-slot name="actions">
        <x-ui.button as="a" href="{{ route('office.index') }}" variant="ghost" icon="arrow-left">{{ trans('main.Back') }}</x-ui.button>
    </x-slot>
</x-ui.page-header>

<input id="office_id" type="hidden" name="office_id" value="{{ $offices->id }}">

@php
    $tabBase   = 'group inline-flex items-center gap-2 whitespace-nowrap border-b-2 px-1 pb-3 pt-3 text-sm transition-colors border-transparent text-slate-600 hover:text-slate-900 hover:border-slate-300';
    $tabActive = '[&.active]:border-primary-600 [&.active]:text-primary-700 [&.active]:font-medium';
    $tabClass  = $tabBase . ' ' . $tabActive;
@endphp

<div id="fixed-scroll" class="rounded border border-slate-200 bg-white">
    <div class="border-b border-slate-200 px-1">
        <ul class="nav nav-tabs nav-tabs-underline -mb-px flex flex-nowrap gap-6 overflow-x-auto border-0 px-3 list-none pl-0 m-0 [&_.nav-link]:cursor-pointer" data-bs-toggle="tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active {{ $tabClass }}" href="#info-tab" data-toggle="tab" data-bs-toggle="tab" aria-controls="info-tab" role="tab" aria-selected="true">
                    <x-ui.icon name="info" />{!! trans('main.Info') !!}
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link {{ $tabClass }}" href="#office-invoice-tab" data-toggle="tab" data-bs-toggle="tab" aria-controls="office-invoice-tab" role="tab" aria-selected="false" tabindex="-1">
                    <x-ui.icon name="receipt" />{!! trans('Office Invoice') !!}
                </a>
            </li>
        </ul>
    </div>

    <div class="p-5">
        <div class="tab-content">

            {{-- Info tab --}}
            <div class="tab-pane fade in active show" role="tabpanel" id="info-tab">
                {{-- Summary --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
                    <div class="rounded border border-slate-200 bg-white">
                        <div class="border-b border-slate-200 px-4 py-3 flex items-center gap-2">
                            <x-ui.icon name="building" size="sm" class="text-slate-400" />
                            <h2 class="text-sm font-medium text-slate-700">Office</h2>
                        </div>
                        <dl class="px-4 py-4 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-sm">
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('Office Name') !!}</dt>
                                <dd class="mt-0.5 text-slate-800">{!! $offices->office_name !!}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('Office Address') !!}</dt>
                                <dd class="mt-0.5 text-slate-800">{!! $offices->office_address !!}</dd>
                            </div>
                        </dl>
                    </div>
                    <div class="rounded border border-slate-200 bg-white">
                        <div class="border-b border-slate-200 px-4 py-3 flex items-center gap-2">
                            <x-ui.icon name="calculator" size="sm" class="text-slate-400" />
                            <h2 class="text-sm font-medium text-slate-700">Totals</h2>
                        </div>
                        <dl class="px-4 py-4 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-sm">
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('Tour Expenses') !!}</dt>
                                <dd class="mt-0.5 font-mono text-slate-800">{!! $total_tour_expense !!}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('Utility Expenses') !!}</dt>
                                <dd class="mt-0.5 font-mono text-slate-800">{!! $total_utility_expense !!}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('Employee Salaries') !!}</dt>
                                <dd class="mt-0.5 font-mono text-slate-800">{!! $total_employee_salary !!}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('Office Earnings') !!}</dt>
                                <dd class="mt-0.5 font-mono text-slate-800">{!! $total_office_earning !!}</dd>
                            </div>
                            <div class="sm:col-span-2 pt-2 border-t border-slate-200">
                                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('Total B.Amount') !!}</dt>
                                <dd class="mt-0.5 font-mono text-lg font-semibold text-slate-900">{!! $total_balance !!}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                {{-- TOUR EXPENSES --}}
                <div class="rounded border border-slate-200 bg-white mb-6">
                    <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3">
                        <h3 class="text-sm font-semibold text-slate-700">{{ trans('TOUR EXPENSES') }}</h3>
                        @if(Auth::user()->can('tour.create'))
                            <x-ui.button as="a" href="{{ url('tour_expenses/create/'.$offices->id) }}" icon="plus" size="sm">New</x-ui.button>
                        @endif
                    </div>
                    <div class="overflow-x-auto">
                        <table id="tour-expenses-table" class="min-w-full divide-y divide-slate-200 text-sm" style="background:#fff">
                            <thead class="bg-slate-50">
                                <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                                    <th class="px-3 py-2 cursor-pointer" onclick="sortTable(0, 'tour-expenses-table')">ID</th>
                                    <th class="px-3 py-2 cursor-pointer" onclick="sortTable(1, 'tour-expenses-table')">Tour name</th>
                                    <th class="px-3 py-2 cursor-pointer" onclick="sortTable(2, 'tour-expenses-table')">Expense</th>
                                    <th class="px-3 py-2 cursor-pointer" onclick="sortTable(3, 'tour-expenses-table')">Departure</th>
                                    <th class="px-3 py-2 cursor-pointer" onclick="sortTable(4, 'tour-expenses-table')">Return</th>
                                    <th class="px-3 py-2 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($tour_expenses as $expense)
                                    <tr class="hover:bg-slate-50">
                                        <td class="px-3 py-2 font-mono text-xs text-slate-500">#{{ $expense->id }}</td>
                                        <td class="px-3 py-2 text-slate-700">{{ $expense->tour_name }}</td>
                                        <td class="px-3 py-2 font-mono text-slate-700">{{ $expense->tour_expenses }}</td>
                                        <td class="px-3 py-2 text-xs text-slate-500 whitespace-nowrap">{{ $expense->date_depart }}</td>
                                        <td class="px-3 py-2 text-xs text-slate-500 whitespace-nowrap">{{ $expense->date_return }}</td>
                                        <td class="px-3 py-2"><div class="flex items-center justify-end gap-1">{!! $expense->action_buttons !!}</div></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="px-3 py-6 text-center text-sm text-slate-500">No tour expenses found</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- UTILITY EXPENSES --}}
                <div class="rounded border border-slate-200 bg-white mb-6">
                    <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3">
                        <h3 class="text-sm font-semibold text-slate-700">{{ trans('UTILITY EXPENSES') }}</h3>
                        @if(Auth::user()->can('tour.create'))
                            <x-ui.button as="a" href="{{ url('utility_expenses/create/'.$offices->id) }}" icon="plus" size="sm">New</x-ui.button>
                        @endif
                    </div>
                    <div class="overflow-x-auto">
                        <table id="utility-expenses-table" class="min-w-full divide-y divide-slate-200 text-sm" style="background:#fff">
                            <thead class="bg-slate-50">
                                <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                                    <th class="px-3 py-2 cursor-pointer" onclick="sortTable(0, 'utility-expenses-table')">ID</th>
                                    <th class="px-3 py-2 cursor-pointer" onclick="sortTable(1, 'utility-expenses-table')">Subject</th>
                                    <th class="px-3 py-2 cursor-pointer" onclick="sortTable(2, 'utility-expenses-table')">Month</th>
                                    <th class="px-3 py-2 cursor-pointer" onclick="sortTable(3, 'utility-expenses-table')">Monthly expense</th>
                                    <th class="px-3 py-2 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($utility_expenses as $utility)
                                    <tr class="hover:bg-slate-50">
                                        <td class="px-3 py-2 font-mono text-xs text-slate-500">#{{ $utility->id }}</td>
                                        <td class="px-3 py-2 text-slate-700">{{ $utility->subject }}</td>
                                        <td class="px-3 py-2 text-slate-700">{{ $utility->month }}</td>
                                        <td class="px-3 py-2 font-mono text-slate-700">{{ $utility->monthly_expense }}</td>
                                        <td class="px-3 py-2"><div class="flex items-center justify-end gap-1">{!! $utility->action_buttons !!}</div></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="px-3 py-6 text-center text-sm text-slate-500">No utility expenses found</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- EMPLOYEE SALARY --}}
                <div class="rounded border border-slate-200 bg-white mb-6">
                    <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3">
                        <h3 class="text-sm font-semibold text-slate-700">{{ trans('EMPLOYEE SALARY') }}</h3>
                        @if(Auth::user()->can('tour.create'))
                            <x-ui.button as="a" href="{{ url('employes-salary/create/'.$offices->id) }}" icon="plus" size="sm">New</x-ui.button>
                        @endif
                    </div>
                    <div class="overflow-x-auto">
                        <table id="employee-salary-table" class="min-w-full divide-y divide-slate-200 text-sm" style="background:#fff">
                            <thead class="bg-slate-50">
                                <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                                    <th class="px-3 py-2 cursor-pointer" onclick="sortTable(0, 'employee-salary-table')">ID</th>
                                    <th class="px-3 py-2 cursor-pointer" onclick="sortTable(1, 'employee-salary-table')">Name</th>
                                    <th class="px-3 py-2 cursor-pointer" onclick="sortTable(2, 'employee-salary-table')">Salary</th>
                                    <th class="px-3 py-2 cursor-pointer" onclick="sortTable(3, 'employee-salary-table')">Month</th>
                                    <th class="px-3 py-2 cursor-pointer" onclick="sortTable(4, 'employee-salary-table')">Bonuses</th>
                                    <th class="px-3 py-2 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($employee_salaries as $salary)
                                    <tr class="hover:bg-slate-50">
                                        <td class="px-3 py-2 font-mono text-xs text-slate-500">#{{ $salary->id }}</td>
                                        <td class="px-3 py-2 text-slate-700">{{ $salary->employe_name }}</td>
                                        <td class="px-3 py-2 font-mono text-slate-700">{{ $salary->employe_salary }}</td>
                                        <td class="px-3 py-2 text-slate-700">{{ $salary->month }}</td>
                                        <td class="px-3 py-2 font-mono text-slate-700">{{ $salary->bonuses }}</td>
                                        <td class="px-3 py-2"><div class="flex items-center justify-end gap-1">{!! $salary->action_buttons !!}</div></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="px-3 py-6 text-center text-sm text-slate-500">No employee salaries found</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- OFFICE EARNINGS --}}
                <div class="rounded border border-slate-200 bg-white mb-6">
                    <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3">
                        <h3 class="text-sm font-semibold text-slate-700">{{ trans('OFFICE EARNINGS') }}</h3>
                        @if(Auth::user()->can('tour.create'))
                            <x-ui.button as="a" href="{{ url('office_earning/create/'.$offices->id) }}" icon="plus" size="sm">New</x-ui.button>
                        @endif
                    </div>
                    <div class="overflow-x-auto">
                        <table id="office-earnings-table" class="min-w-full divide-y divide-slate-200 text-sm" style="background:#fff">
                            <thead class="bg-slate-50">
                                <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                                    <th class="px-3 py-2 cursor-pointer" onclick="sortTable(0, 'office-earnings-table')">ID</th>
                                    <th class="px-3 py-2 cursor-pointer" onclick="sortTable(1, 'office-earnings-table')">Month</th>
                                    <th class="px-3 py-2 cursor-pointer" onclick="sortTable(2, 'office-earnings-table')">Revenue</th>
                                    <th class="px-3 py-2 cursor-pointer" onclick="sortTable(3, 'office-earnings-table')">Profit</th>
                                    <th class="px-3 py-2 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($office_earnings as $earning)
                                    <tr class="hover:bg-slate-50">
                                        <td class="px-3 py-2 font-mono text-xs text-slate-500">#{{ $earning->id }}</td>
                                        <td class="px-3 py-2 text-slate-700">{{ $earning->month }}</td>
                                        <td class="px-3 py-2 font-mono text-slate-700">{{ $earning->revenue }}</td>
                                        <td class="px-3 py-2 font-mono text-slate-700">{{ $earning->profit }}</td>
                                        <td class="px-3 py-2"><div class="flex items-center justify-end gap-1">{!! $earning->action_buttons !!}</div></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="px-3 py-6 text-center text-sm text-slate-500">No office earnings found</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- OFFICE BALANCES --}}
                <div class="rounded border border-slate-200 bg-white mb-6">
                    <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3">
                        <h3 class="text-sm font-semibold text-slate-700">{{ trans('OFFICE BALANCES') }}</h3>
                        @if(Auth::user()->can('tour.create'))
                            <x-ui.button as="a" href="{{ url('office_balance/create/'.$offices->id) }}" icon="plus" size="sm">New</x-ui.button>
                        @endif
                    </div>
                    <div class="overflow-x-auto">
                        <table id="office-balances-table" class="min-w-full divide-y divide-slate-200 text-sm" style="background:#fff">
                            <thead class="bg-slate-50">
                                <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                                    <th class="px-3 py-2 cursor-pointer" onclick="sortTable(0, 'office-balances-table')">ID</th>
                                    <th class="px-3 py-2 cursor-pointer" onclick="sortTable(1, 'office-balances-table')">Subject</th>
                                    <th class="px-3 py-2 cursor-pointer" onclick="sortTable(2, 'office-balances-table')">Month</th>
                                    <th class="px-3 py-2 cursor-pointer" onclick="sortTable(3, 'office-balances-table')">Total amount</th>
                                    <th class="px-3 py-2 cursor-pointer" onclick="sortTable(4, 'office-balances-table')">Due date</th>
                                    <th class="px-3 py-2 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($balances as $balance)
                                    <tr class="hover:bg-slate-50">
                                        <td class="px-3 py-2 font-mono text-xs text-slate-500">#{{ $balance->id }}</td>
                                        <td class="px-3 py-2 text-slate-700">{{ $balance->subject_of_balance }}</td>
                                        <td class="px-3 py-2 text-slate-700">{{ $balance->month }}</td>
                                        <td class="px-3 py-2 font-mono text-slate-700">{{ $balance->total_amount }}</td>
                                        <td class="px-3 py-2 text-xs text-slate-500 whitespace-nowrap">{{ $balance->due_date }}</td>
                                        <td class="px-3 py-2"><div class="flex items-center justify-end gap-1">{!! $balance->action_buttons !!}</div></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="px-3 py-6 text-center text-sm text-slate-500">No office balances found</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Office Invoice tab --}}
            <div class="tab-pane fade in" role="tabpanel" id="office-invoice-tab">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <x-ui.icon name="receipt" size="sm" class="text-slate-400" />
                        <h3 class="text-sm font-semibold text-slate-700">Office invoices</h3>
                    </div>
                    @if(Auth::user()->can('tour.create'))
                        <x-ui.button as="a" href="{{ url('officeInvoices/create/'.$offices->id) }}" icon="plus" size="sm">New invoice</x-ui.button>
                    @endif
                </div>

                <div class="rounded border border-slate-200 bg-white">
                    <div class="overflow-x-auto">
                        <table id="officesinvoice-table" class="min-w-full divide-y divide-slate-200 text-sm" style="background:#fff">
                            <thead class="bg-slate-50">
                                <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                                    <th class="px-3 py-2 cursor-pointer" onclick="sortTable(0, 'officesinvoice-table')">ID</th>
                                    <th class="px-3 py-2 cursor-pointer" onclick="sortTable(1, 'officesinvoice-table')">Office name</th>
                                    <th class="px-3 py-2 cursor-pointer" onclick="sortTable(2, 'officesinvoice-table')">Office date</th>
                                    <th class="px-3 py-2 cursor-pointer" onclick="sortTable(3, 'officesinvoice-table')">Invoice no</th>
                                    <th class="px-3 py-2 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($office_invoices as $invoice)
                                    <tr class="hover:bg-slate-50">
                                        <td class="px-3 py-2 font-mono text-xs text-slate-500">#{{ $invoice->officeinvoice_dataId }}</td>
                                        <td class="px-3 py-2 text-slate-700">{{ $invoice->officeName }}</td>
                                        <td class="px-3 py-2 text-xs text-slate-500 whitespace-nowrap">{{ $invoice->date }}</td>
                                        <td class="px-3 py-2 font-mono text-slate-700">{{ $invoice->invoice_no }}</td>
                                        <td class="px-3 py-2"><div class="flex items-center justify-end gap-1">{!! $invoice->action_buttons !!}</div></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="px-3 py-6 text-center text-sm text-slate-500">No office invoices found</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<span id="services_name" data-service-name='accounting' data-history-route="{{ route('services_history', ['id' => $offices->id]) }}"></span>
@endsection

@section('post_scripts')
    <script src="{{ asset('js/comment.js') }}"></script>
@endsection
