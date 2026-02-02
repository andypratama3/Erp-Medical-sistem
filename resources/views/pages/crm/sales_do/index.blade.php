@extends('layouts.app')

@section('content')
<div class="space-y-6">
    {{-- Flash Message --}}
    <x-flash-message.flash />

    <!-- Header -->
    <x-common.page-breadcrumb pageTitle="Sales Delivery Order" />

    <!-- Filters -->
    <form method="GET" class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 dark:bg-white/[0.03] dark:shadow-none dark:border dark:border-gray-700">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Search
                </label>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="DO Code / Tracking Code / Customer..."
                    class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 px-4 text-sm text-gray-800 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:focus:border-blue-400 transition"
                />
            </div>

            <!-- Status -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Status
                </label>
                <select name="status" class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 px-4 text-sm text-gray-800 dark:text-gray-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:focus:border-blue-400 transition">
                    <option value="">All Status</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>

            <!-- Office -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Office
                </label>
                <x-form.select.searchable-select
                    name="office_id"
                    :options="$offices->map(fn($o) => ['value' => $o->id, 'label' => $o->name])->toArray()"
                    :selected="old('office_id', request('office_id') ?: '')"
                    placeholder="Select Office..."
                    searchPlaceholder="Search office..."
                />
            </div>

            <!-- Customer -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Customer
                </label>
                <x-form.select.searchable-select
                    name="customer_id"
                    :options="$customers->map(fn($c) => ['value' => $c->id, 'label' => $c->name])->toArray()"
                    :selected="old('customer_id', request('customer_id') ?: '')"
                    placeholder="Select Customer..."
                    searchPlaceholder="Search customer..."
                />
            </div>

            <!-- Date From -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    From Date
                </label>
                <input
                    type="date"
                    name="date_from"
                    value="{{ request('date_from') }}"
                    class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 px-4 text-sm text-gray-800 dark:text-gray-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:focus:border-blue-400 transition"
                />
            </div>

            <!-- Date To -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    To Date
                </label>
                <input
                    type="date"
                    name="date_to"
                    value="{{ request('date_to') }}"
                    class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 px-4 text-sm text-gray-800 dark:text-gray-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:focus:border-blue-400 transition"
                />
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center gap-3 mt-6">
            <a href="{{ route('crm.sales-do.index') }}"
                class="inline-flex items-center justify-center font-medium gap-2 rounded-lg transition px-5 py-2.5 text-sm bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-100 hover:bg-gray-300 dark:hover:bg-gray-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Reset
            </a>
            <button type="submit"
                class="inline-flex items-center justify-center font-medium gap-2 rounded-lg transition px-5 py-2.5 text-sm bg-blue-600 text-white hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                </svg>
                Apply Filter
            </button>
            @can('create_sales_do')
            <a href="{{ route('crm.sales-do.create') }}"
                class="inline-flex items-center justify-center font-medium gap-2 rounded-lg transition px-5 py-2.5 text-sm bg-green-600 text-white hover:bg-green-700 dark:bg-green-600 dark:hover:bg-green-700 ml-auto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                New DO
            </a>
            @endcan
        </div>
    </form>

    <!-- Table -->
    <x-common.component-card
        title="Sales Delivery Order List"
        desc="Manage all sales delivery orders in your system">

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-gray-600 dark:text-gray-300">
                <thead class="bg-gray-100 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                    <tr>
                        <th class="px-6 py-3 text-left font-medium text-gray-900 dark:text-gray-100">DO Code</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-900 dark:text-gray-100">Customer</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-900 dark:text-gray-100">Office</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-900 dark:text-gray-100">Date</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-900 dark:text-gray-100">Amount</th>
                        <th class="px-6 py-3 text-center font-medium text-gray-900 dark:text-gray-100">Status</th>
                        <th class="px-6 py-3 text-center font-medium text-gray-900 dark:text-gray-100">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($salesDOs as $do)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-gray-100">
                            {{ $do->do_code }}
                        </td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-300">
                            {{ $do->customer->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-300">
                            {{ $do->office->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-300">
                            {{ $do->do_date->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 text-right font-medium text-gray-900 dark:text-gray-100">
                            Rp {{ number_format($do->grand_total, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $do->status_badge_class }}">
                                {{ $do->status_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('crm.sales-do.show', $do) }}"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 hover:bg-blue-200 dark:hover:bg-blue-900/50 transition"
                                    title="View">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                @if($do->canBeEdited())
                                <a href="{{ route('crm.sales-do.edit', $do) }}"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400 hover:bg-yellow-200 dark:hover:bg-yellow-900/50 transition"
                                    title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                @endif
                                @if($do->canBeDeleted())
                                <form action="{{ route('crm.sales-do.destroy', $do) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 hover:bg-red-200 dark:hover:bg-red-900/50 transition" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <svg class="w-12 h-12 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                                <p class="text-sm font-medium">No data found</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-4">
            {{ $salesDOs->links() }}
        </div>
    </x-common.component-card>
</div>
@endsection
