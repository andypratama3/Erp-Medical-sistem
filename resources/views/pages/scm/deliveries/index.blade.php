@extends('layouts.app')

@section('title', 'Deliveries')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="mb-6 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white">🚚 Deliveries</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Manage all delivery records and status</p>
        </div>

        <a href="{{ route('scm.task-board.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-800 text-white rounded-lg font-semibold transition">
            ← Back to Task Board
        </a>
    </div>

    <x-flash-message.flash />

    <!-- Statistics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-6 mb-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-4">
            <p class="text-sm text-gray-600 dark:text-gray-400 font-semibold">Scheduled</p>
            <p class="text-3xl font-bold text-blue-600 dark:text-blue-400 mt-1">
                {{ $deliveries->where('delivery_status', 'scheduled')->count() }}
            </p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-4">
            <p class="text-sm text-gray-600 dark:text-gray-400 font-semibold">On Delivery</p>
            <p class="text-3xl font-bold text-orange-600 dark:text-orange-400 mt-1">
                {{ $deliveries->where('delivery_status', 'on_delivery')->count() }}
            </p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-4">
            <p class="text-sm text-gray-600 dark:text-gray-400 font-semibold">Delivered</p>
            <p class="text-3xl font-bold text-green-600 dark:text-green-400 mt-1">
                {{ $deliveries->where('delivery_status', 'delivered')->count() }}
            </p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-4">
            <p class="text-sm text-gray-600 dark:text-gray-400 font-semibold">Failed</p>
            <p class="text-3xl font-bold text-red-600 dark:text-red-400 mt-1">
                {{ $deliveries->where('delivery_status', 'failed')->count() }}
            </p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl mb-6 p-4">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-2">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="DO code or customer..."
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
            </div>

            <div>
                <label class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-2">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
                    <option value="">All Status</option>
                    <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    <option value="on_delivery" {{ request('status') === 'on_delivery' ? 'selected' : '' }}>On Delivery</option>
                    <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
            </div>

            <div>
                <label class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-2">From Date</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
            </div>

            <div>
                <label class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-2">To Date</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-800 text-white rounded-lg font-bold transition">
                    🔍 Filter
                </button>
                <a href="{{ route('scm.deliveries.index') }}" class="flex-1 px-4 py-2 bg-gray-300 dark:bg-gray-700 hover:bg-gray-400 dark:hover:bg-gray-600 text-gray-900 dark:text-white rounded-lg font-bold transition text-center">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Deliveries Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-gray-700 dark:text-white font-bold">DO Code</th>
                        <th class="px-6 py-3 text-left text-gray-700 dark:text-white font-bold">Customer</th>
                        <th class="px-6 py-3 text-left text-gray-700 dark:text-white font-bold">Driver</th>
                        <th class="px-6 py-3 text-left text-gray-700 dark:text-white font-bold">Delivery Date</th>
                        <th class="px-6 py-3 text-left text-gray-700 dark:text-white font-bold">Address</th>
                        <th class="px-6 py-3 text-left text-gray-700 dark:text-white font-bold">Status</th>
                        <th class="px-6 py-3 text-left text-gray-700 dark:text-white font-bold">Received</th>
                        <th class="px-6 py-3 text-left text-gray-700 dark:text-white font-bold">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($deliveries as $delivery)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                            <td class="px-6 py-4">
                                <a href="{{ route('scm.deliveries.show', $delivery) }}" class="text-blue-600 dark:text-blue-400 hover:underline font-mono font-semibold">
                                    {{ $delivery->salesDo->do_code }}
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-gray-900 dark:text-white font-medium">{{ $delivery->salesDo->customer?->name ?? '-' }}</p>
                                <p class="text-xs text-gray-600 dark:text-gray-400">{{ $delivery->salesDo->office?->name ?? '-' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-gray-900 dark:text-white font-medium">{{ $delivery->driver?->name ?? '-' }}</p>
                                <p class="text-xs text-gray-600 dark:text-gray-400">{{ $delivery->driver?->phone ?? '-' }}</p>
                            </td>
                            <td class="px-6 py-4 text-gray-900 dark:text-white">
                                {{ $delivery->delivery_date ? \Carbon\Carbon::parse($delivery->delivery_date)->format('d M Y') : '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-gray-900 dark:text-white text-sm max-w-xs truncate" title="{{ $delivery->delivery_address }}">
                                    {{ Str::limit($delivery->delivery_address, 40) }}
                                </p>
                            </td>
                            <td class="px-6 py-4">
                                @if($delivery->delivery_status === 'scheduled')
                                    <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded text-xs font-bold">
                                        Scheduled
                                    </span>
                                @elseif($delivery->delivery_status === 'on_delivery')
                                    <span class="px-2 py-1 bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200 rounded text-xs font-bold">
                                        On Delivery
                                    </span>
                                @elseif($delivery->delivery_status === 'delivered')
                                    <span class="px-2 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded text-xs font-bold">
                                        Delivered
                                    </span>
                                @elseif($delivery->delivery_status === 'failed')
                                    <span class="px-2 py-1 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 rounded text-xs font-bold">
                                        Failed
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($delivery->received_by)
                                    <p class="text-gray-900 dark:text-white font-medium">{{ $delivery->received_by }}</p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">
                                        {{ $delivery->received_at ? \Carbon\Carbon::parse($delivery->received_at)->format('d M Y H:i') : '-' }}
                                    </p>
                                @else
                                    <span class="text-gray-500 dark:text-gray-400 italic">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('scm.deliveries.show', $delivery) }}" class="inline-flex items-center gap-1 px-3 py-1 bg-blue-600 dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-800 text-white rounded text-xs font-bold transition">
                                    👁️ View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                <p class="text-lg font-semibold">No deliveries found</p>
                                <p class="text-sm mt-1">Start by creating deliveries from the task board</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($deliveries->hasPages())
        <div class="mt-6">
            {{ $deliveries->links() }}
        </div>
    @endif
</div>
@endsection
