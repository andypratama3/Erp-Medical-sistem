@extends('layouts.app')

@section('title', 'Deliveries')

@section('content')
<x-common.page-breadcrumb pageTitle="Deliveries" />

<div class="space-y-6 sm:space-y-7">
    <x-flash-message.flash />

    <!-- Action Bar -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex gap-3">
            <a href="{{ route('scm.deliveries.create') }}"
                class="inline-flex items-center justify-center font-medium gap-2 rounded-lg transition px-4 py-3 text-sm bg-brand-500 text-white shadow-theme-xs hover:bg-brand-600">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8 1v14M1 8h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
                New Delivery
            </a>
            <a href="{{ route('scm.task-board.index') }}"
                class="inline-flex items-center justify-center font-medium gap-2 rounded-lg transition px-4 py-3 text-sm bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-white hover:bg-gray-300 dark:hover:bg-gray-600">
                Task Board
            </a>
        </div>
    </div>

    <!-- Statistics -->
    <div class="grid sm:grid-cols-2 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-5 border-t-4 border-blue-500">
            <p class="text-xs text-gray-600 dark:text-gray-400 font-semibold uppercase">Scheduled</p>
            <p class="text-xl font-bold text-blue-600 dark:text-blue-400 mt-1">
                {{ $deliveries->where('delivery_status', 'scheduled')->count() }}
            </p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-5 border-t-4 border-orange-500">
            <p class="text-xs text-gray-600 dark:text-gray-400 font-semibold uppercase">On Route</p>
            <p class="text-xl font-bold text-orange-600 dark:text-orange-400 mt-1">
                {{ $deliveries->where('delivery_status', 'on_route')->count() }}
            </p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-5 border-t-4 border-green-500">
            <p class="text-xs text-gray-600 dark:text-gray-400 font-semibold uppercase">Delivered</p>
            <p class="text-xl font-bold text-green-600 dark:text-green-400 mt-1">
                {{ $deliveries->where('delivery_status', 'delivered')->count() }}
            </p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-5 border-t-4 border-red-500">
            <p class="text-xs text-gray-600 dark:text-gray-400 font-semibold uppercase">Failed</p>
            <p class="text-xl font-bold text-red-600 dark:text-red-400 mt-1">
                {{ $deliveries->where('delivery_status', 'failed')->count() }}
            </p>
        </div>
    </div>

    <!-- Filters -->
    <x-common.component-card title="Filters" desc="Filter deliveries by various criteria">
        <form method="GET" action="{{ route('scm.deliveries.index') }}" class="space-y-4">
            <div class="grid grid-cols-2 sm:grid-cols-2 gap-4">
                <div>
                    <label class="mb-1.5 block text-sm font-medium dark:text-white">Search</label>
                    <input type="text" name="search" placeholder="DO code / Tracking..." value="{{ request('search') }}"
                        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium dark:text-white">Status</label>
                    <select name="delivery_status"
                        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('delivery_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="scheduled" {{ request('delivery_status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="on_route" {{ request('delivery_status') === 'on_route' ? 'selected' : '' }}>On Route</option>
                        <option value="delivered" {{ request('delivery_status') === 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="failed" {{ request('delivery_status') === 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="cancelled" {{ request('delivery_status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium dark:text-white">Driver</label>
                     <x-form.select.searchable-select
                        name="driver_id"
                        :options="$drivers->map(fn($o) => ['value' => $o->id, 'label' => $o->name])->toArray()"
                        :selected="old('driver_id', request('driver_id') ?: '')"
                        placeholder="-- Select Driver --"
                        searchPlaceholder="Search driver..."
                        :required="true" />
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium dark:text-white">From Date</label>
                  <x-form.date-picker
                        id="from_date"
                        name="from_date"
                        placeholder="Select From Date"
                        :defaultDate="old('from_date', request('from_date') ?: '')"
                    />
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit"
                        class="inline-flex items-center justify-center font-medium gap-2 rounded-lg transition px-4 py-3 text-sm bg-brand-500 text-white shadow-theme-xs hover:bg-brand-600">Apply</button>
                    <a href="{{ route('scm.deliveries.index') }}"
                        class="inline-flex items-center justify-center font-medium gap-2 rounded-lg transition px-4 py-3 text-sm bg-red-500 text-white shadow-theme-xs hover:bg-red-600">Reset</a>
                </div>
            </div>
        </form>
    </x-common.component-card>

    <!-- Deliveries Table -->
    <x-common.component-card title="Delivery List" desc="Manage all delivery records">
        <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="max-w-full overflow-x-auto">
                <table class="w-full min-w-[900px]">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <th class="px-6 py-4 text-left text-sm font-medium dark:text-white">DO Code</th>
                            <th class="px-6 py-4 text-left text-sm font-medium dark:text-white">Customer</th>
                            <th class="px-6 py-4 text-left text-sm font-medium dark:text-white">Driver</th>
                            <th class="px-6 py-4 text-left text-sm font-medium dark:text-white">Delivery Date</th>
                            <th class="px-6 py-4 text-left text-sm font-medium dark:text-white">Tracking #</th>
                            <th class="px-6 py-4 text-left text-sm font-medium dark:text-white">Status</th>
                            <th class="px-6 py-4 text-left text-sm font-medium dark:text-white">Receiver</th>
                            <th class="px-6 py-4 text-center text-sm font-medium dark:text-white">Action</th>
                        </tr>
                    </thead>
                    <tbody>

                        @forelse($deliveries as $delivery)

                        <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors">
                            <td class="px-6 py-4">
                                <a href="{{ route('scm.deliveries.show', $delivery) }}" class="text-sm font-semibold text-blue-600 dark:text-blue-400 font-mono hover:underline">
                                    {{ $delivery->salesDO?->do_code ?? '-' }}
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-700 dark:text-white font-medium">{{ $delivery->salesDO?->customer?->name ?? '-' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-700 dark:text-white font-medium">{{ $delivery->driver?->name ?? '-' }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $delivery->driver?->vehicle_number ?? '-' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-700 dark:text-white">{{ $delivery->delivery_date?->format('d M Y') ?? '-' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-700 dark:text-white font-mono">{{ $delivery->tracking_number ?? '-' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $delivery->status_badge['color'] }}-500 text-white">
                                    <span class="inline-block w-2 h-2 rounded-full mr-2 bg-current"></span>
                                    {{ $delivery->status_badge['label'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($delivery->receiver_name)
                                    <p class="text-sm text-gray-700 dark:text-white font-medium">{{ $delivery->receiver_name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $delivery->received_at?->format('d M Y H:i') ?? '-' }}
                                    </p>
                                @else
                                    <span class="text-gray-400 dark:text-gray-500 italic text-sm">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex justify-center gap-2">
                                    <a href="{{ route('scm.deliveries.show', $delivery) }}"
                                        class="inline-flex items-center gap-1 px-3 py-1 bg-blue-600 dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-800 text-white rounded text-xs font-bold transition">
                                        View
                                    </a>
                                    <a href="{{ route('scm.deliveries.edit', $delivery) }}"
                                        class="inline-flex items-center gap-1 px-3 py-1 bg-gray-500 dark:bg-gray-600 hover:bg-gray-600 dark:hover:bg-gray-700 text-white rounded text-xs font-bold transition">
                                        Edit
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                                    </svg>
                                    <p class="text-gray-500 dark:text-gray-400 font-medium">Tidak ada delivery</p>
                                    <p class="text-gray-400 dark:text-gray-500 text-sm">Buat delivery baru atau dari task board</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </x-common.component-card>

    @if($deliveries->hasPages())
    <div class="flex justify-start gap-2">{{ $deliveries->links() }}</div>
    @endif
</div>
@endsection
