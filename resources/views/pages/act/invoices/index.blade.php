@extends('layouts.app')

@section('title', 'Invoices')

@section('content')
<div class="space-y-6">

    <x-flash-message.flash />

    <x-common.page-breadcrumb pageTitle="Invoices" />

    <!-- Filters -->
    <form method="GET" class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium mb-1 dark:text-white">Search</label>
                <input type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Invoice number / customer"
                    class="h-11 w-full rounded-lg border px-4 text-sm dark:text-white dark:bg-gray-900 dark:border-gray-700">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1 dark:text-white">Status</label>
                <select name="status" class="h-11 w-full rounded-lg border px-4 text-sm dark:text-white dark:bg-gray-900 dark:border-gray-700">
                    <option value="">ALL</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                    <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <a href="{{ route('act.invoices.index') }}"
                   class="px-4 py-3 text-sm bg-red-500 text-white rounded-lg">
                    Reset
                </a>
                <button type="submit"
                    class="px-4 py-3 text-sm bg-brand-500 text-white rounded-lg">
                    Apply
                </button>
            </div>
        </div>
    </form>

    <!-- Table -->
    <x-common.component-card
        title="Invoice List"
        desc="Manage all invoices"
        link="{{ route('act.invoices.create') }}">

        <x-table.table-component
            :data="$invoicesData"
            :columns="$columns"
            :searchable="true"
            :filterable="true"
            :pagination="$invoices" />

    </x-common.component-card>

</div>
@endsection
