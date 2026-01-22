@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Sales Delivery Order" />

<div class="space-y-6 sm:space-y-7">
    <x-flash-message.flash />

    <!-- Filters Card -->
    <x-common.component-card title="Filters" desc="Filter Sales DO by various criteria">
        <form method="GET" action="{{ route('crm.sales-do.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Search -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium dark:text-white">Search</label>
                    <input type="text" name="search" placeholder="DO Number / Customer / Tracking Code"
                        value="{{ request('search') }}"
                        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900">
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium dark:text-white">Status</label>
                    <select name="status"
                        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900">
                        <option value="">All Status</option>
                        @foreach($statuses as $key => $label)
                            <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Office Filter -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium dark:text-white">Office</label>
                   <x-form.select.searchable-select
                        name="offices"
                        :options="$offices->map(fn($c) => ['value' => $c->id, 'label' => $c->name])->toArray()"
                        :selected="old('offices', request('offices') ?: '')"
                        placeholder="-- Select Office --"
                        searchPlaceholder="Search offices..."
                        :required="true"
                    />
                </div>

                <!-- Customer Filter -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium dark:text-white">Customer</label>
                    <x-form.select.searchable-select
                        name="customer"
                        :options="$customers->map(fn($c) => ['value' => $c->id, 'label' => $c->name])->toArray()"
                        :selected="old('customer', request('customer') ?: '')"
                        placeholder="-- Select Customer --"
                        searchPlaceholder="Search customer..."
                        :required="true"
                    />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium dark:text-white">From Date</label>
                        <x-form.date-picker
                            id="do_date"
                            name="date_from"
                            placeholder="Select DO Date From"
                            :defaultDate="old(
                                'date_from',
                                request('date_from') ? request('date_from') : now()->toDateString()
                            )"
                        />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium dark:text-white">To Date</label>
                          <x-form.date-picker
                            id="do_date"
                            name="date_to"
                            placeholder="Select DO Date To"
                            :defaultDate="old(
                                'date_to',
                                request('date_to') ? request('date_to') : now()->toDateString()
                            )"
                        />
                    </div>
                </div>

                <div class="flex items-end gap-2">
                     <a href="{{ route('crm.sales-do.index') }}"
                        class="inline-flex items-center justify-center font-medium gap-2 rounded-lg transition px-4 py-3 text-sm bg-red-500 text-white shadow-theme-xs hover:bg-red-600">
                        Reset
                    </a>
                    <button type="submit"
                        class="inline-flex items-center justify-center font-medium gap-2 rounded-lg transition px-4 py-3 text-sm bg-brand-500 text-white shadow-theme-xs hover:bg-brand-600">
                        Apply
                    </button>
                </div>
            </div>

            <!-- Date Range (Optional) -->

        </form>
    </x-common.component-card>

    <!-- Sales DO List Card -->
    <x-common.component-card
        title="Sales DO List"
        desc="Manage all Sales Delivery Orders"
        link="{{ route('crm.sales-do.create') }}">

        <x-table.table-component
            :data="$salesDOsData"
            :columns="$columns"
            :searchable="true"
            :filterable="true" />
    </x-common.component-card>

    <!-- Pagination -->
    @if($salesDOs->hasPages())
        <div class="flex justify-start gap-2">
            {{ $salesDOs->links() }}
        </div>
    @endif
</div>
@endsection
