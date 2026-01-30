@extends('layouts.app')

@section('title', 'SCM Task Board')

@section('content')
<x-common.page-breadcrumb pageTitle="SCM Task Board" />

<div class="space-y-6 sm:space-y-7">
    <x-flash-message.flash />

    <!-- Statistics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 font-semibold">Ready for Delivery</p>
                    <p class="text-3xl font-bold text-blue-600 dark:text-blue-400 mt-1">{{ $stats['ready'] ?? 0 }}</p>
                </div>
                <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-full">
                    <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 font-semibold">On Delivery</p>
                    <p class="text-3xl font-bold text-orange-600 dark:text-orange-400 mt-1">{{ $stats['on_delivery'] ?? 0 }}</p>
                </div>
                <div class="p-3 bg-orange-100 dark:bg-orange-900/30 rounded-full">
                    <svg class="w-8 h-8 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 font-semibold">Delivered</p>
                    <p class="text-3xl font-bold text-green-600 dark:text-green-400 mt-1">{{ $stats['delivered'] ?? 0 }}</p>
                </div>
                <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-full">
                    <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="dark:bg-gray-800 rounded-lg shadow dark:shadow-xl mb-6 p-4">
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-2">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="DO code or customer..."
                       class="h-11 w-full flex items-center justify-between rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:text-white dark:border-gray-700 dark:bg-gray-900 focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 transition">
            </div>

            <div>
                <label class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-2">Status</label>
                <select name="status" class="h-11 w-full flex items-center justify-between rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:text-white dark:border-gray-700 dark:bg-gray-900 focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 transition">
                    <option value="">All Status</option>
                    <option value="wqs_ready" {{ request('status') === 'wqs_ready' ? 'selected' : '' }}>Ready for Delivery</option>
                    <option value="scm_on_delivery" {{ request('status') === 'scm_on_delivery' ? 'selected' : '' }}>On Delivery</option>
                    <option value="scm_delivered" {{ request('status') === 'scm_delivered' ? 'selected' : '' }}>Delivered</option>
                </select>
            </div>

            <div>
                <label class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-2">Driver</label>
                <select name="driver" class="h-11 w-full flex items-center justify-between rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:text-white dark:border-gray-700 dark:bg-gray-900 focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 transition">
                    <option value="">All Drivers</option>
                    @foreach($drivers as $driver)
                        <option value="{{ $driver->id }}" {{ request('driver') == $driver->id ? 'selected' : '' }}>
                            {{ $driver->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="inline-flex items-center justify-center font-medium gap-2 rounded-lg transition px-4 py-3 text-sm bg-brand-500 text-white shadow-theme-xs hover:bg-brand-600">
                    Apply
                </button>
                <a href="{{ route('scm.task-board.index') }}" class="inline-flex items-center justify-center font-medium gap-2 rounded-lg transition px-4 py-3 text-sm bg-gray-500 text-white shadow-theme-xs hover:bg-gray-600">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Task Board -->
    <div class=" dark:bg-gray-800 rounded-lg shadow dark:shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-gray-700 dark:text-white font-bold">DO Code</th>
                        <th class="px-6 py-3 text-left text-gray-700 dark:text-white font-bold">Customer</th>
                        <th class="px-6 py-3 text-left text-gray-700 dark:text-white font-bold">DO Date</th>
                        <th class="px-6 py-3 text-center text-gray-700 dark:text-white font-bold">Items</th>
                        <th class="px-6 py-3 text-left text-gray-700 dark:text-white font-bold">Driver</th>
                        <th class="px-6 py-3 text-left text-gray-700 dark:text-white font-bold">Scheduled</th>
                        <th class="px-6 py-3 text-left text-gray-700 dark:text-white font-bold">Status</th>
                        <th class="px-6 py-3 text-left text-gray-700 dark:text-white font-bold">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($salesDOs as $do)
                        <tr class=" dark:hover:bg-gray-700/50 transition">
                            <td class="px-6 py-4">
                                <a href="{{ route('scm.task-board.show', $do) }}" class="text-blue-600 dark:text-blue-400 hover:underline font-mono font-semibold">
                                    {{ $do->do_code }}
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-gray-900 dark:text-white font-medium">{{ $do->customer?->name ?? '-' }}</p>
                                <p class="text-xs text-gray-600 dark:text-gray-400">{{ $do->office?->name ?? '-' }}</p>
                            </td>
                            <td class="px-6 py-4 text-gray-900 dark:text-white">
                                {{ $do->do_date->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-block px-2 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-white rounded font-bold">
                                    {{ $do->items->count() }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($do->delivery)
                                    <p class="text-gray-900 dark:text-white font-medium">{{ $do->delivery->driver?->name ?? '-' }}</p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">{{ $do->delivery->vehicle_plate ?? '-' }}</p>
                                @else
                                    <span class="text-gray-500 dark:text-gray-400 italic">Not assigned</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-900 dark:text-white">
                                @if($do->delivery && $do->delivery->scheduled_date)
                                    {{ \Carbon\Carbon::parse($do->delivery->scheduled_date)->format('d M Y') }}
                                @else
                                    <span class="text-gray-500 dark:text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($do->status === 'wqs_ready')
                                    <span class="px-2 py-1 bg-blue-900 dark:bg-blue-900 text-black dark:text-white rounded text-xs font-bold">
                                        Ready
                                    </span>
                                @elseif($do->status === 'scm_on_delivery')
                                    <span class="px-2 py-1 bg-orange-100 dark:bg-orange-900 text-black dark:text-white rounded text-xs font-bold">
                                        On Delivery
                                    </span>
                                @elseif($do->status === 'scm_delivered')`
                                    <span class="px-2 py-1 bg-green-100 dark:bg-green-900 text-black dark:text-white rounded text-xs font-bold">
                                        Delivered
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('scm.task-board.show', $do) }}" class="inline-flex items-center gap-1 px-3 py-1 bg-blue-600 dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-800 text-white rounded text-xs font-bold transition">
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                <p class="text-lg font-semibold">No delivery orders found</p>
                                <p class="text-sm mt-1">Wait for orders from WQS or adjust your filters</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($salesDOs->hasPages())
        <div class="mt-6">
            {{ $salesDOs->links() }}
        </div>
    @endif
</div>
@endsection
