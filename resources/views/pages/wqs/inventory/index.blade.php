@extends('layouts.app')

@section('title', 'Inventory Management')

@section('content')
<x-common.page-breadcrumb pageTitle="Inventory" />

<div class="space-y-6 sm:space-y-7">
    <x-flash-message.flash />

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-700 rounded-lg p-4">
            <p class="text-xs text-blue-600 dark:text-blue-400 font-bold">Total Products</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['total_products'] ?? 0 }}</p>
        </div>
        <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 rounded-lg p-4">
            <p class="text-xs text-green-600 dark:text-green-400 font-bold">In Stock</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['in_stock'] ?? 0 }}</p>
        </div>
        <div class="bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4">
            <p class="text-xs text-yellow-600 dark:text-yellow-400 font-bold">Low Stock</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['low_stock'] ?? 0 }}</p>
        </div>
        <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 rounded-lg p-4">
            <p class="text-xs text-red-600 dark:text-red-400 font-bold">Out of Stock</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['out_of_stock'] ?? 0 }}</p>
        </div>
    </div>

    <!-- Inventory Table -->
    <x-common.component-card title="Stock Levels" desc="Current inventory status by product">
        <div class="mb-4 flex flex-col sm:flex-row gap-3">
            <input type="text" placeholder="Search products..."
                class="h-10 rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900 focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 transition flex-1">
            <select class="h-10 rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900">
                <option value="">All Categories</option>
                @foreach($categories ?? [] as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
            <a href="{{ route('wqs.inventory.adjustments') }}" 
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm whitespace-nowrap">
                View Adjustments
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700 dark:text-gray-300 font-bold">Product Code</th>
                        <th class="px-4 py-3 text-left text-gray-700 dark:text-gray-300 font-bold">Product Name</th>
                        <th class="px-4 py-3 text-left text-gray-700 dark:text-gray-300 font-bold">Category</th>
                        <th class="px-4 py-3 text-right text-gray-700 dark:text-gray-300 font-bold">Current Stock</th>
                        <th class="px-4 py-3 text-right text-gray-700 dark:text-gray-300 font-bold">Min Stock</th>
                        <th class="px-4 py-3 text-center text-gray-700 dark:text-gray-300 font-bold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($inventory as $item)
                    <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition">
                        <td class="px-4 py-3 font-mono text-sm text-gray-600 dark:text-gray-400">{{ $item->product->code }}</td>
                        <td class="px-4 py-3 text-gray-900 dark:text-white font-medium">{{ $item->product->name }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $item->product->category->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-gray-900 dark:text-white">{{ number_format($item->stock_quantity) }}</td>
                        <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-400">{{ number_format($item->min_stock) }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($item->stock_quantity <= 0)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">Out of Stock</span>
                            @elseif($item->stock_quantity <= $item->min_stock)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">Low Stock</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">In Stock</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                            No inventory records found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($inventory->hasPages())
        <div class="mt-4">
            {{ $inventory->links() }}
        </div>
        @endif
    </x-common.component-card>
</div>
@endsection
