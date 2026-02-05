@extends('layouts.app')

@section('title', 'Delivery Tracking')

@section('content')
<x-common.page-breadcrumb pageTitle="Delivery Tracking" />

<div class="space-y-6 sm:space-y-7">
    <x-flash-message.flash />

    <x-common.component-card title="Active Deliveries" desc="Track all ongoing deliveries">
        <div class="mb-4 flex flex-col sm:flex-row gap-3">
            <input type="text" placeholder="Search by DO code, customer..."
                class="h-10 rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900 focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 transition flex-1">
            <select class="h-10 rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900">
                <option value="">All Status</option>
                <option value="scheduled">Scheduled</option>
                <option value="on_route">On Route</option>
                <option value="delivered">Delivered</option>
            </select>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700 dark:text-gray-300 font-bold">DO Code</th>
                        <th class="px-4 py-3 text-left text-gray-700 dark:text-gray-300 font-bold">Customer</th>
                        <th class="px-4 py-3 text-left text-gray-700 dark:text-gray-300 font-bold">Driver</th>
                        <th class="px-4 py-3 text-left text-gray-700 dark:text-gray-300 font-bold">Delivery Date</th>
                        <th class="px-4 py-3 text-left text-gray-700 dark:text-gray-300 font-bold">Status</th>
                        <th class="px-4 py-3 text-center text-gray-700 dark:text-gray-300 font-bold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($deliveries as $delivery)
                    <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition">
                        <td class="px-4 py-3">
                            <span class="font-mono text-sm text-blue-600 dark:text-blue-400">{{ $delivery->salesDO->do_code }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-900 dark:text-white">{{ $delivery->salesDO->customer->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $delivery->driver->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-900 dark:text-white">{{ $delivery->delivery_date->format('d M Y') }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                {{ $delivery->delivery_status == 'scheduled' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' : '' }}
                                {{ $delivery->delivery_status == 'on_route' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400' : '' }}
                                {{ $delivery->delivery_status == 'delivered' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : '' }}">
                                {{ ucfirst(str_replace('_', ' ', $delivery->delivery_status)) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('scm.tracking.show', $delivery) }}" 
                                class="text-blue-600 dark:text-blue-400 hover:underline text-sm">Track</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                            No deliveries found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($deliveries->hasPages())
        <div class="mt-4">
            {{ $deliveries->links() }}
        </div>
        @endif
    </x-common.component-card>
</div>
@endsection
