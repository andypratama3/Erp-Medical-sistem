@extends('layouts.app')

@section('title', 'Inventory Adjustments')

@section('content')
<x-common.page-breadcrumb pageTitle="Inventory Adjustments" />

<div class="space-y-6 sm:space-y-7">
    <x-flash-message.flash />

    <x-common.component-card title="Adjustment History" desc="View all inventory adjustments">
        <div class="mb-4 flex flex-col sm:flex-row gap-3">
            <input type="text" placeholder="Search by product or reason..."
                class="h-10 rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900 focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 transition flex-1">
            <select class="h-10 rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900">
                <option value="">All Types</option>
                <option value="stock_in">Stock In</option>
                <option value="stock_out">Stock Out</option>
                <option value="adjustment">Adjustment</option>
                <option value="damage">Damage</option>
                <option value="expired">Expired</option>
            </select>
            <a href="{{ route('wqs.inventory.index') }}" 
                class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition text-sm whitespace-nowrap">
                Back to Inventory
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700 dark:text-gray-300 font-bold">Date</th>
                        <th class="px-4 py-3 text-left text-gray-700 dark:text-gray-300 font-bold">Product</th>
                        <th class="px-4 py-3 text-left text-gray-700 dark:text-gray-300 font-bold">Type</th>
                        <th class="px-4 py-3 text-right text-gray-700 dark:text-gray-300 font-bold">Qty Change</th>
                        <th class="px-4 py-3 text-right text-gray-700 dark:text-gray-300 font-bold">Before</th>
                        <th class="px-4 py-3 text-right text-gray-700 dark:text-gray-300 font-bold">After</th>
                        <th class="px-4 py-3 text-left text-gray-700 dark:text-gray-300 font-bold">Reason</th>
                        <th class="px-4 py-3 text-left text-gray-700 dark:text-gray-300 font-bold">By</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($adjustments as $adjustment)
                    <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition">
                        <td class="px-4 py-3 text-gray-900 dark:text-white">{{ $adjustment->adjustment_date->format('d M Y') }}</td>
                        <td class="px-4 py-3 text-gray-900 dark:text-white font-medium">{{ $adjustment->product->name }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                {{ $adjustment->adjustment_type == 'stock_in' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : '' }}
                                {{ $adjustment->adjustment_type == 'stock_out' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400' : '' }}
                                {{ in_array($adjustment->adjustment_type, ['damage', 'expired']) ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' : '' }}
                                {{ $adjustment->adjustment_type == 'adjustment' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' : '' }}">
                                {{ ucfirst(str_replace('_', ' ', $adjustment->adjustment_type)) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right font-semibold
                            {{ $adjustment->qty_change > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                            {{ $adjustment->qty_change > 0 ? '+' : '' }}{{ number_format($adjustment->qty_change) }}
                        </td>
                        <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-400">{{ number_format($adjustment->qty_before) }}</td>
                        <td class="px-4 py-3 text-right text-gray-900 dark:text-white font-semibold">{{ number_format($adjustment->qty_after) }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $adjustment->reason ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $adjustment->adjustedBy->name ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                            No adjustments found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($adjustments->hasPages())
        <div class="mt-4">
            {{ $adjustments->links() }}
        </div>
        @endif
    </x-common.component-card>
</div>
@endsection
